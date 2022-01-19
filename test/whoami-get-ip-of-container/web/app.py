from flask import Flask
from flask import request

import requests

app = Flask(__name__)

@app.route('/')
def hello():
    response = requests.get("http://database/")
    return response.data

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8000, debug=True)