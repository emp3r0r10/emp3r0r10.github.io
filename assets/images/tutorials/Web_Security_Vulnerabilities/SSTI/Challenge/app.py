from flask import Flask, request, render_template_string

app = Flask(__name__)

@app.route("/")
def home():
    if request.args.get('c'):
        template = f"<body style='text-align: center;background-color:rgb(7, 129, 142);align-items: center;margin-top: 20rem;font-family: Roboto, sans-serif;'><h1 style='color:white;font-size:40px'>Welcome, " + request.args.get('c') + "</h1>"
        return render_template_string(template)
    else:
        return "<body style='text-align: center;background-color:rgb(7, 129, 142);align-items: center;margin-top: 20rem;font-family: Roboto, sans-serif;'><h3 style='color:white;font-size:20px'>Hello, We are here to Learn Web Application Vulnerabilities.</h2></br><p style='color:red;font-size:15px'><b>Missing parameter c</b></p>"

if __name__ == "__main__":
    app.run()
