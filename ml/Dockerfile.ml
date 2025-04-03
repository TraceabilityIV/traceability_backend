FROM python:3.9-slim

WORKDIR /app

# Instalar dependencias básicas
RUN apt-get update && apt-get install -y \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Copiar requirements
COPY requirements.txt .

# Instalar dependencias de Python
RUN pip install --no-cache-dir -r requirements.txt

# Copiar el código
COPY . .

# Puerto para la API
EXPOSE 5000

# Comando para ejecutar la aplicación
CMD ["python", "app.py"]
