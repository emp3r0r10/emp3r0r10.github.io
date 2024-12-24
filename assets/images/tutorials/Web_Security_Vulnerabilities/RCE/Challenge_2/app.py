from flask import Flask, request
import pymysql
import os

app = Flask(__name__)

# Database connection settings
db_host = "mysql"
db_user = "root"
db_password = "rootpassword"
db_name = "vulnerable_db"

@app.route('/')
def index():
    return '''
        <form action="/search" method="post">
            Search: <input type="text" name="query"><br>
            <input type="submit" value="Search">
        </form>
    '''

@app.route('/search', methods=['POST'])
def search():
    query = request.form['query']
    connection = pymysql.connect(host=db_host,
                                 user=db_user,
                                 password=db_password,
                                 database=db_name)
    try:
        with connection.cursor() as cursor:
            # Vulnerable SQL query
            sql = f"SELECT * FROM users WHERE name = '{query}'"
            cursor.execute(sql)
            results = cursor.fetchall()
            return f"<pre>{results}</pre>"
    finally:
        connection.close()

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0')
