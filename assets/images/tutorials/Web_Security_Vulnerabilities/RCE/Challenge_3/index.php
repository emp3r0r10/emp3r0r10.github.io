<!DOCTYPE html>
<html>
<head>
    <title>SQL Injection to RCE Challenge</title>
</head>
<body>
    <h1>Welcome to Challenge #3: SQL Injection to RCE</h1>
    <form action="login.php" method="GET">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
