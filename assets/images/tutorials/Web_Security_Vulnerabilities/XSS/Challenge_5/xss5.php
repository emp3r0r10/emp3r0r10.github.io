<!DOCTYPE html>
<html>
<head>
    <title>DOM XSS Test Page</title>
    <style>
        body {
            background-color: #148692;
            text-align:center;
            justify-content: center;
            margin: 10rem;
            color:white;
        }
        h1 {
            font-size: 40px;
            color: black;
        }
        label {
            font-size: 30px;
        }
        input {
            height: 30px;
        }
        textarea {
            width: 30%;
            height: 150px;
        }
        .forms {
            border: #6A7B7D solid 4px;
            padding: 20px;
            border-width: thick;
        }
        .btn {
            background-color: #008CBA; /* Green */
            border: none;
            color: white;
            font-size: 20px;
            color: black; 
            border: 2px solid #008CBA;
        }
        #searchResults {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <h1>Search Page</h1>
    <div class="forms">
        <form method="GET">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search">
            <br>
            <br>
            <button type="submit" class="btn">Search</button>
        </form>
        <br>
        <div id="searchResults"></div>        
    </div>


    <script>
        var urlParams = new URLSearchParams(window.location.search);
        var searchTerm = urlParams.get('search');
        if (searchTerm) {
            document.getElementById('searchResults').innerHTML = 'Search results for: ' + searchTerm; // Vulnerable to DOM XSS
        }
    </script>
</body>
</html>
