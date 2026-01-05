from dotenv import load_dotenv
import os

load_dotenv()



class Settings:
    APP_NAME: str = os.getenv("APP_NAME", "FastAPI")
    APP_ENV: str = os.getenv("APP_ENV", "local")
    APP_DEBUG: bool = os.getenv("APP_DEBUG", "true").lower() == "true"
    
    
    # Database Configuration
    DB_CONNECTION: str = os.getenv("DB_CONNECTION", "mysql")
    DB_HOST: str = os.getenv("DB_HOST", "127.0.0.1")
    DB_PORT: str = os.getenv("DB_PORT", "3306")
    DB_DATABASE: str = os.getenv("DB_DATABASE") or os.getenv("DB_NAME", "db_forecast")
    DB_USERNAME: str = os.getenv("DB_USERNAME") or os.getenv("DB_USER", "root")
    DB_PASSWORD: str = os.getenv("DB_PASSWORD", "")

    @property
    def DATABASE_URL(self) -> str:
        # Check for explicitly defined DATABASE_URL, handling potential copy-paste errors
        if url := os.getenv("DATABASE_URL"):
            if url.startswith("DATABASE_URL="):
                 url = url.replace("DATABASE_URL=", "", 1)
            return url
        
        # Construct from components
        password_part = f":{self.DB_PASSWORD}" if self.DB_PASSWORD else ""
        return f"{self.DB_CONNECTION}+pymysql://{self.DB_USERNAME}{password_part}@{self.DB_HOST}:{self.DB_PORT}/{self.DB_DATABASE}"

    AI_SECRET_KEY: str = os.getenv("AI_SECRET_KEY")


settings = Settings()
