from fastapi import FastAPI
import pandas as pd
from forecast import forecast_product_sales

app = FastAPI()


@app.get("/ping")
def ping():
    {"status": "ok", "message": "Python FastAPI is reachable"}


@app.post("/forecast")
def forecast_endpoint(payload: dict):
    """
    payload = {
        "product_id": 1,
        "data": [
            {"date": "2024-12-01", "quantity": 10},
            {"date": "2024-12-02", "quantity": 15}
        ],
        "forecast_days": 30,
        "current_stock": 120
    }
    """

    df = pd.DataFrame(payload["data"])
    forecast_days = payload.get("forecast_days", 30)
    current_stock = payload.get("current_stock", 0)

    forecast = forecast_product_sales(df, forecast_days)

    # Hitung estimasi pemakaian stok
    forecast["daily_usage"] = forecast["yhat"].clip(lower=0)
    forecast["remaining_stock"] = current_stock - forecast["daily_usage"].cumsum()

    stock_out_day = forecast[forecast["remaining_stock"] <= 0]

    return {
        "forecast": forecast.tail(forecast_days).to_dict(orient="records"),
        "estimated_stock_out_date": (
            stock_out_day.iloc[0]["ds"].strftime("%Y-%m-%d")
            if not stock_out_day.empty
            else None
        ),
        "recommended_stock": int(forecast["daily_usage"].sum()),
    }
