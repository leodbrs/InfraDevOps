from flask import Flask
from flask import request

app = Flask(__name__)

@app.route('/')
def hello():
    curl "http://database/"
    return request.data, 200

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8000, debug=True)