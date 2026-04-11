---
title: "Mobile Hacking Lab - Food Store Writeup"
classes: wide
header:
  teaser: /assets/images/tutorials/Android_Tutorial/MHL_FoodStore/Food_Store.png
ribbon: green
description: "Hello, in today's writeup, I will walk you through Food Store lab from MobileHackingLab."
categories:
  - Tutorials
toc: false
---

![Food_Store](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/Food_Store.png)

Hello, in today's writeup, I will walk you through [Food Store](https://www.mobilehackinglab.com/course/lab-food-store) lab [MobileHackingLab](https://www.mobilehackinglab.com/).

In this lab we will exploit `SQL Injection` vulnerability to in sign up to get access to `pro` account.

To understand what is SQL Injection, you can see this [tutorial](https://emp3r0r10.github.io/tutorials/SQL-Injection/) and to learn how to do android pentesting and learn tools, you can see [this](https://emp3r0r10.github.io/tutorials/).

So, let's download the APK file and open it in an Android emulator.

We can see signup and login pages, so let's create an account and log in using `test:test`.

![App_Signup](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/App_Signup.png)





![App_Login](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/App_Login.png)

The app redirects us to the dashboard, which contains some foods to order, and we are in `Regular User` mode.

![App_Dashboard](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/App_Dashboard.png)

As we explore the app at runtime, let's analyze it using `JADX-GUI`.

`AndroidManifest.xml` contains three activities which we saw earlier: `Signup`, `LoginActivity`, and `MainActivity`.

![Manifest](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/Manifest.png)

Let's start with `LoginActivity` as it is the `LAUNCHER` of the application.

We can see it first defines `dbHelper` and calls the database.

![LoginActivity](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/LoginActivity.png)

Note that if we become a `pro` user, the credits become `10000` instead of `100`.

![image-20251025071839479](C:\Users\abdel\AppData\Roaming\Typora\typora-user-images\image-20251025071839479.png)

The `Signup` activity takes username, password, and address from user inputs, checks they are not empty, then passes them to `DBHelper.adduser()`.

A `DBHelper` instance is created to save the user to the database.

![Signup](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/Signup.png)

Let's move to `DBHelper` to analyze the database behavior.

![DBHelper_1](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/DBHelper_1.png)

![DBHelper](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/DBHelper_2.png)

**`DBHelper` Analysis:**

1. Creates the `userdatabase.db` database, which can be found in `/data/data/<package_name>/databases/`.
2. Creates the `users` table and defines the following columns:
   - `id`
   - `username`
   - `password`
   - `address`
   - `isPro`
3. Inserts data into the `users` table; `isPro` is assigned `0` by default. The stored data format is:
   - Username in plain string format
   - Base64-encoded password
   - Base64-encoded address
4. Retrieves user data using `getUserByUsername`
   1. The `getReadableDatabase()` method opens a connection to the database with read-only permissions.
   2. Decodes the password and address
   3. Returns the record according to user id.


We can see that user-controlled data is inserted into the database without validation or sanitization.

Therefore, we can manipulate the SQL query and perform SQL Injection to become a `pro` user.

SQL Query:

````sql
insert INTO users (username, password, address, isPro) Values (' " + username + "', ' " + encodedPassword + "', ' " + encodedAddress + "', 0)
````

We need to inject a payload during signup that allows us to become `pro`. We can use the following SQL injection payload:

`jack','amFjaw==','amFjaw==',1); --`

Explanation:

1. `jack` is the username.
2. `amFjaw==` is the base64-encoded password and address, which decodes to `jack`.
3. `1` is the `isPro` value.

Then we can add random values in the password and address fields.

![SQL_Injection_Signup](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/SQL_Injection_Signup.png)

If we log in with `jack:jack`, we will be redirected to the dashboard and become a `Pro User`.

![Pro_Jack](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/Pro_Jack.png)

![Food_Store_Solved](/assets/images/tutorials/Android_Tutorial/MHL_FoodStore/Food_Store_Solved.png)



