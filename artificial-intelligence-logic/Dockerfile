FROM python:3.11-slim

# Install system dependencies required for Propjet, MySQL client, and build tools
RUN apt-get update && apt-get install -y \
    build-essential \
    default-libmysqlclient-dev \
    pkg-config \
    python3-dev \
    && rm -rf /var/lib/apt/lists/*

# Set environment variables
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

WORKDIR /app

# Install Dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir -r requirements.txt

# Copy Project
COPY . .

# Expose port
EXPOSE 8025

# Run application
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8025"]
