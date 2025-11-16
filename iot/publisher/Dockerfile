FROM python:3.12-slim
WORKDIR /app
COPY . .
RUN pip install paho-mqtt requests
ENV PYTHONUNBUFFERED=1
CMD ["python", "-u", "publisher.py"]
