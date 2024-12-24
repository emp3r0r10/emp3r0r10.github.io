from flask import Flask, request
import os

app = Flask(__name__)

@app.route('/')
def index():
    return '''
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style type="text/css">
        body {
            background-color: #09B5E9;
            align-items: center;
            margin-top: 10rem;
            font-family: Roboto, sans-serif;
            text-align: center;
        }
        h1 {
            color:white;
            font-size:50px;
        }
        .btn {
            background-color: lightblue;
            border: none;
            border-radius: 10px;
            font-size: 40px;
            # padding: 5px;
            margin-top: 1rem;

        }   
        label {
            font-size: 20px;
        }
        .text{
            height: 25px;
        }                     
    </style>
</head>
<body>
    <h1>Ping A Network</h1>
    <form action="/ping" method="post">
        IP Address: <input type="text" name="ip" class="text"><br>
        <input type="submit" value="Ping" class="btn">
    </form>
</body>
</html>
    '''

@app.route('/ping', methods=['POST'])
def ping():
    ip = request.form['ip']
    # Vulnerable to command injection
    command = f"sudo ping -c 4 {ip}"
    output = os.popen(command).read()
    return f"<pre>{output}</pre>"

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0')
