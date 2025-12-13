from fastapi import FastAPI
from app.config import settings

app = FastAPI(title=settings.APP_NAME, debug=settings.APP_DEBUG)


@app.get("/")
def read_root():
    return {
        "message": "FastAPI Forecasting Service is running",
        "env": settings.APP_ENV,
    }
