from dotenv import load_dotenv
from fastapi import FastAPI, Depends
from fastapi.responses import StreamingResponse
import pandas as pd
from pydantic import BaseModel
from typing import List
import os
import io
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter
from app.forecast import forecast_product_sales

from sqlalchemy.orm import Session
from sqlalchemy.orm import Session
from sqlalchemy import text
import uvicorn

from app.database.session import engine, get_db
from app.database.base import Base
from app.database.models.product import Product

app = FastAPI()

load_dotenv()


@app.get("/ping")
def ping():
    return {"status": "ok", "service": "fastapi", "message": "FastAPI is reachable"}


@app.post("/echo")
def echo(payload: dict):
    return {"received": payload, "source": "fastapi"}


@app.get("/test-db")
def test_db():
    df = pd.read_sql("SELECT COUNT(*) as total FROM products", engine)
    return df.to_dict(orient="records")


@app.get("/products")
def get_products(db: Session = Depends(get_db)):
    return db.query(Product).all()


@app.get("/history/{product_id}")
def get_history(product_id: int, db: Session = Depends(get_db)):
    return db.execute(
        """
        SELECT DATE(created_at) as date, SUM(quantity) as qty
        FROM sales_items
        WHERE product_id = :pid
        GROUP BY DATE(created_at)
        ORDER BY date DESC
        LIMIT 7
        """,
        {"pid": product_id},
    ).fetchall()


@app.get("/health/db")
def db_health_check():
    try:
        with engine.connect() as conn:
            conn.execute("SELECT 1")
        return {"database": "connected"}
    except Exception as e:
        return {"database": "error", "detail": str(e)}


# @app.post("/forecast")
# def forecast_endpoint(payload: dict):
#     """
#     payload = {
#         "product_id": 1,
#         "data": [
#             {"date": "2024-12-01", "quantity": 10},
#             {"date": "2024-12-02", "quantity": 15}
#         ],
#         "forecast_days": 30,
#         "current_stock": 120
#     }
#     """

#     df = pd.DataFrame(payload["data"])
#     forecast_days = payload.get("forecast_days", 30)
#     current_stock = payload.get("current_stock", 0)

#     forecast = forecast_product_sales(df, forecast_days)

#     # Hitung estimasi pemakaian stok
#     forecast["daily_usage"] = forecast["yhat"].clip(lower=0)
#     forecast["remaining_stock"] = current_stock - forecast["daily_usage"].cumsum()

#     stock_out_day = forecast[forecast["remaining_stock"] <= 0]

#     return {
#         "forecast": forecast.tail(forecast_days).to_dict(orient="records"),
#         "estimated_stock_out_date": (
#             stock_out_day.iloc[0]["ds"].strftime("%Y-%m-%d")
#             if not stock_out_day.empty
#             else None
#         ),
#         "recommended_stock": int(forecast["daily_usage"].sum()),
#     }


class HistoryItem(BaseModel):
    date: str
    quantity: int


class ForecastRequest(BaseModel):
    product_id: int
    data: List[HistoryItem]
    forecast_days: int = 30
    current_stock: int = 0


@app.post("/forecast")
def forecast_endpoint(payload: ForecastRequest):
    """
    Menerima data dari Laravel dan melakukan forecasting.
    Payload: { "product_id": 1, "data": [...], "forecast_days": 30, "current_stock": 100 }
    """
    # Konversi input data ke DataFrame
    # HistoryItem memiliki field date dan quantity
    
    # Kita perlu list of dicts untuk DataFrame
    data_list = [{"date": item.date, "quantity": item.quantity} for item in payload.data]
    df = pd.DataFrame(data_list)
    
    if df.empty:
        return {
            "product_id": payload.product_id,
            "message": "No data provided",
            "forecast": []
        }

    # Helper function forecast_product_sales mengharapkan kolom 'date' dan 'quantity'
    # Pastikan format date benar
    df['date'] = pd.to_datetime(df['date'])

    if len(df) < 2:
        return {
            "product_id": payload.product_id,
            "message": f"Not enough data to forecast. Required: 2 rows. Found: {len(df)} row(s).",
            "forecast": []
        }

    try:
        forecast_df = forecast_product_sales(df, days=payload.forecast_days)
    except Exception as e:
        return {"error": f"Forecasting failed: {str(e)}"}

    # Hitung estimasi stock out jika diperlukan (opsional, sesuai komen lama)
    forecast_df["daily_usage"] = forecast_df["yhat"].clip(lower=0)
    forecast_df["remaining_stock"] = payload.current_stock - forecast_df["daily_usage"].cumsum()
    
    stock_out_day = forecast_df[forecast_df["remaining_stock"] <= 0]
    estimated_stock_out = (
        stock_out_day.iloc[0]["ds"].strftime("%Y-%m-%d") 
        if not stock_out_day.empty 
        else None
    )

    return {
        "product_id": payload.product_id,
        "forecast_days": payload.forecast_days,
        "forecast_data": forecast_df.tail(payload.forecast_days).to_dict(orient="records"),
        "estimated_stock_out_date": estimated_stock_out,
        "recommended_stock": int(forecast_df["daily_usage"].sum())
    }


class ForecastDbRequest(BaseModel):
    days: int = 30


@app.post("/forecast-from-db/{product_id}")
def forecast_from_db(product_id: int, request: ForecastDbRequest, db: Session = Depends(get_db)):
    """
    Melakukan forecasting berdasarkan data sales yang ada di database.
    """
    # 1. Query data sales untuk product_id tertentu
    #    Kita perlu aggregate quantity per tanggal
    query = """
        SELECT 
            DATE(created_at) as date, 
            SUM(quantity) as quantity
        FROM sales_items
        WHERE product_id = :pid
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    """
    
    # Menggunakan pandas untuk membaca sql langsung agar lebih mudah
    try:
        df = pd.read_sql(text(query), db.bind, params={"pid": product_id})
    except Exception as e:
        return {"error": f"Failed to read data: {str(e)}"}

    if df.empty:
        return {
            "product_id": product_id,
            "message": "No sales data found for this product",
            "forecast": []
        }

    # 2. Lakukan forecasting
    #    Pastikan kolom sesuai dengan yang diharapkan function forecast_product_sales
    #    Function mengharapkan column 'date' dan 'quantity' dalam DataFrame
    
    # Convert date to datetime just in case
    df['date'] = pd.to_datetime(df['date'])

    # VALIDASI: Prophet butuh minimal 2 data point
    if len(df) < 2:
        print(f"--> [SKIP] Product ID: {product_id} (Not enough data: {len(df)} rows)")
        return {
            "product_id": product_id,
            "message": f"Not enough data to forecast. Required: 2 rows. Found: {len(df)} row(s).",
            "forecast": []
        }

    try:
        # Panggil fungsi forecast dari forecast.py
        forecast_df = forecast_product_sales(df, days=request.days)
    except Exception as e:
        print(f"--> [ERROR] Product ID: {product_id} - Forecasting failed: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Forecasting failed: {str(e)}")

    print(f"--> [DONE] Forecast calculated for Product ID: {product_id}")
    # 3. Format hasil untuk response
    #    Kita ambil data forecast (future) saja
    return {
        "product_id": product_id,
        "forecast_days": request.days,
        "forecast_data": forecast_df.tail(request.days).to_dict(orient="records")
    }


def create_product_report_page(p, product_id, db):
    """
    Helper function to draw a report page for a single product on the provided canvas 'p'.
    Always returns True, drawing an error message if data is insufficient.
    """
    print(f"--> [PDF] Processing Page for Product ID: {product_id}")
    width, height = letter

    # 1. Fetch Product Info
    product_query = text("SELECT name, stock FROM products WHERE id = :pid")
    product = db.execute(product_query, {"pid": product_id}).fetchone()
    
    product_name = product[0] if product else f"Unknown Product ({product_id})"
    current_stock = product[1] if product else 0

    # --- Draw Header (Always visible) ---
    p.setFont("Helvetica-Bold", 18)
    p.drawString(50, height - 50, "Sales Forecast Report")
    
    p.setFont("Helvetica", 10)
    p.drawString(50, height - 70, f"Generated on: {pd.Timestamp.now().strftime('%Y-%m-%d %H:%M')}")
    p.line(50, height - 80, width - 50, height - 80)
    
    # --- Product Info ---
    y_pos = height - 120
    p.setFont("Helvetica-Bold", 12)
    p.drawString(50, y_pos, "Product Details")
    p.setFont("Helvetica", 11)
    p.drawString(50, y_pos - 20, f"Name: {product_name}")
    p.drawString(50, y_pos - 40, f"Current Stock: {current_stock} units")


    # 2. Fetch History
    history_query = """
        SELECT DATE(created_at) as date, SUM(quantity) as quantity
        FROM sales_items WHERE product_id = :pid
        GROUP BY DATE(created_at) ORDER BY date ASC
    """
    try:
        df = pd.read_sql(text(history_query), db.bind, params={"pid": product_id})
    except Exception as e:
        p.setFont("Helvetica-Oblique", 12)
        p.drawString(50, y_pos - 100, f"Error fetching history: {str(e)}")
        return True

    if len(df) < 2:
        p.setFont("Helvetica-Oblique", 12)
        p.drawString(50, y_pos - 100, "Insufficient data to generate forecast (Needs at least 2 days of sales).")
        return True

    df['date'] = pd.to_datetime(df['date'])
    
    # 3. Forecast
    try:
        forecast_df = forecast_product_sales(df, days=30)
    except Exception as e:
        p.setFont("Helvetica-Oblique", 12)
        p.drawString(50, y_pos - 100, f"Forecasting Error: {str(e)}")
        return True

    # Prepare Data
    history_data = df.tail(30).to_dict(orient="records")
    forecast_data = forecast_df.tail(30).to_dict(orient="records")
    
    total_predicted_sales = round(forecast_df.tail(30)['yhat'].sum())
    min_stock_recommendation = total_predicted_sales # Simple logic: Cover demand

    # 4. Draw Analysis & Tables
    # Right Column: Analysis
    p.setFont("Helvetica-Bold", 12)
    p.drawString(300, y_pos, "Analysis (Next 30 Days)")
    p.setFont("Helvetica", 11)
    p.drawString(300, y_pos - 20, f"Total Predicted Sales: {total_predicted_sales} units")
    
    stock_status = "Safe" if current_stock >= total_predicted_sales else "Restock Needed"
    color = (0, 0.5, 0) if stock_status == "Safe" else (0.8, 0, 0) # Green or Red
    
    p.setFillColorRGB(*color)
    p.drawString(300, y_pos - 40, f"Status: {stock_status}")
    p.setFillColorRGB(0, 0, 0) # Reset black

    p.drawString(300, y_pos - 60, f"Rec. Min Stock: {min_stock_recommendation} units")

    # --- TABLES ---
    y_pos -= 100
    
    def draw_table(title, data, x_start, y_start, is_forecast=False):
        current_y = y_start
        p.setFont("Helvetica-Bold", 12)
        p.drawString(x_start, current_y, title)
        current_y -= 20
        
        # Header
        p.setFont("Helvetica-Bold", 9)
        p.drawString(x_start, current_y, "Date")
        p.drawString(x_start + 80, current_y, "Qty")
        p.line(x_start, current_y - 5, x_start + 150, current_y - 5)
        current_y -= 15
        
        # Rows
        p.setFont("Helvetica", 9)
        for row in data:
            if current_y < 50: break
            if is_forecast:
                d_str = row['ds'].strftime('%Y-%m-%d')
                qty = round(row['yhat'])
            else:
                d_str = row['date'].strftime('%Y-%m-%d')
                qty = row['quantity']
            
            p.drawString(x_start, current_y, d_str)
            p.drawString(x_start + 80, current_y, str(qty))
            current_y -= 12
            
        return current_y

    # Draw Two Tables Side-by-Side
    # Left: History
    draw_table("Last 30 Days History", history_data, 50, y_pos)
    
    # Right: Forecast
    draw_table("Forecast Next 30 Days", forecast_data, 300, y_pos, is_forecast=True)
    
    return True


@app.get("/forecast-pdf/{product_id}")
def forecast_pdf(product_id: int, db: Session = Depends(get_db)):
    """
    Generate Single PDF.
    """
    buffer = io.BytesIO()
    p = canvas.Canvas(buffer, pagesize=letter)
    
    success = create_product_report_page(p, product_id, db)
    if not success:
         p.drawString(100, 700, "Not enough data to generate report.")
    
    p.showPage()
    p.save()
    buffer.seek(0)

    return StreamingResponse(
        buffer, 
        media_type="application/pdf",
        headers={"Content-Disposition": f"attachment; filename=forecast_{product_id}.pdf"}
    )


@app.get("/forecast-pdf-all")
def forecast_pdf_all(db: Session = Depends(get_db)):
    """
    Generate Unified PDF for ALL products.
    """
    print("--> [START] Generating Master PDF Report")
    
    # Get all product IDs
    products = db.execute(text("SELECT id FROM products")).fetchall()
    product_ids = [row[0] for row in products]
    
    buffer = io.BytesIO()
    p = canvas.Canvas(buffer, pagesize=letter)
    
    pages_generated = 0
    for pid in product_ids:
        # Always draws a page (forecast or error message)
        create_product_report_page(p, pid, db)
        p.showPage()
        pages_generated += 1
            
    if pages_generated == 0:
        p.drawString(100, 700, "No forecast data available for any product.")
        p.showPage()

    p.save()
    buffer.seek(0)
    print(f"--> [DONE] Master PDF generated with {pages_generated} pages.")

    return StreamingResponse(
        buffer, 
        media_type="application/pdf",
        headers={"Content-Disposition": "attachment; filename=master_forecast_report.pdf"}
    )


if __name__ == "__main__":
    import uvicorn
    # Membaca port dari environment variable atau default 8025
    port = int(os.getenv("FASTAPI_PORT", 8025))
    uvicorn.run("app.main:app", host="0.0.0.0", port=port, reload=True)
