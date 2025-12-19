import pandas as pd
from prophet import Prophet


def forecast_product_sales(df, days=30):
    """
    df: DataFrame dengan kolom ['date', 'quantity']
    days: jumlah hari ke depan
    """

    # Prophet membutuhkan format:
    # ds = date, y = value
    prophet_df = df.rename(columns={"date": "ds", "quantity": "y"})

    model = Prophet(
        daily_seasonality=True, weekly_seasonality=True, yearly_seasonality=False
    )

    model.fit(prophet_df)

    future = model.make_future_dataframe(periods=days)
    forecast = model.predict(future)

    return forecast[["ds", "yhat", "yhat_lower", "yhat_upper"]]
