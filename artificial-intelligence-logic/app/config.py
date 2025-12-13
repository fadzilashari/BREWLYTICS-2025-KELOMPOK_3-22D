from dotenv import load_dotenv
import os

load_dotenv()


class Settings:
    APP_NAME: str = os.getenv("APP_NAME")
    APP_ENV: str = os.getenv("APP_ENV")
    APP_DEBUG: bool = os.getenv("APP_DEBUG") == "true"
    DATABASE_URL: str = os.getenv("DATABASE_URL")
    AI_SECRET_KEY: str = os.getenv("AI_SECRET_KEY")


settings = Settings()
