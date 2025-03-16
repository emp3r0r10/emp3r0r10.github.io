---
title: "Web Security Vulnerabilities - File Upload Vulnerabilities"
classes: wide
header:
  teaser: /assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/File-Upload-Cover.png
ribbon: green
description: "File upload vulnerabilities arise when an application allows users to upload files to its system without properly sanitizing or validating the file type, filename, size, or content. Attackers can manipulate this functionality to upload malicious files, which can then be executed to compromise the server or the data stored on it."
categories:
  - Tutorials
toc: false
---


<img src="/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/File-Upload.png" alt="File-Upload" style="zoom: 100%;" />

## Table of Contents
  - [What is File Upload Vulnerability?](#what-is-File-Upload-Vulnerability)
  - [What is the Impact of File Upload Vulnerability?](#what-is-the-impact-of-file-upload-vulnerability)
  - [How to Find a File Upload Vulnerability?](#how-to-find-a-file-upload-vulnerability)
  - [Some Bypassing Techniques](#some-bypassing-techniques)
  - [How to Prevent a File Upload Vulnerability?](#how-to-prevent-a-file-upload-vulnerability)
  - [Time to Practice](#time-to-practice)
    - [Challenge #1](#challenge-1)
    - [Challenge #2](#challenge-2)
    - [Challenge #3](#challenge-3)
  - [Resources](#resources)
  - [Conclusion](#conclusion)

## What is File Upload Vulnerability?

File upload vulnerabilities arise when an application allows users to upload files to its system without properly sanitizing or validating the file type, filename, size, or content. Attackers can manipulate this functionality to upload malicious files, which can then be executed to compromise the server or the data stored on it.

**Example:**

Suppose an application allows users to upload profile images with the following code:

```php
<?php
if(isset($_FILES['file'])){
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    
    $file_ext = strtolower(end(explode('.',$_FILES['file']['name'])));
    
    $extensions= array("jpeg","jpg","png");
    
    if(in_array($file_ext,$extensions)=== false){
       echo "Extension not allowed, please choose a JPEG or PNG file.";
       exit();
    }
    
    if($file_size > 2097152){
       echo 'File size must be less than 2 MB';
       exit();
    }
    
    move_uploaded_file($file_tmp,"uploads/".$file_name);
    echo "Success";
 }
?>
```

```html
<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>
    <h1>Upload Profile Picture</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" name="submit" value="Upload">
    </form>
</body>
</html>
```

In the given code, the application only checks for the file extension using the `in_array` function. This leaves the application vulnerable to a potential attack, where an attacker can bypass the extension check and upload a malicious file with a double extension, such as `exploit.php.jpg`. The file will be accepted as a valid image file by the application but it may still be executed as `PHP` code on the server.

## What is the impact of File Upload Vulnerability?

The impact of a file upload vulnerability depends on the application code and how it handles the uploaded file. It may lead to:

1. **Remote Code Execution (RCE):** When an application fails to validate the file type, an attacker can upload a web shell and gain full control over the server.
2. **Directory Traversal:** When an application fails to validate the filename, an attacker can overwrite essential files by uploading a file with the same name
3. **Denial of Service (DOS):** When an application fails to validate the file size, an attacker can upload a large file that fills the available disk space.
4. **Cross Site Scripting (XSS):** An attacker could embed malicious scripts in filenames, leading to XSS attacks when the filename is rendered on a webpage.
5. **File Overwrites:** If filenames aren't managed properly, attackers can overwrite critical files by using identical names.

## How to find a File Upload Vulnerability?

To identify file upload vulnerabilities, follow these steps:

1. **Upload normal file:** The first step in testing any functionality is to use it as intended, so you should upload a normal file to check how the application handles it and where is it stored on the server.
2. **Checking file type:** Test how the application handles allowed and disallowed file extensions.
3. **Checking file content:** Check how the application handles the content of the uploaded file and whether you can add malicious content in an allowed extension (**Ex:** EXIF data).
4. **Checking file path:** Determine where the file is saved and try to escape the uploaded directory to access sensitive files on the server.

## Some bypassing techniques

1. **Bypass File Extension**

   - Upload files with double extensions (e.g., `shell.php.jpg`) if the server validates only the last extension.
   - Use `Null Byte` (e.g., `shell.php%00.jpg`) as some applicatons treat null byte as the end of string.
   - Change Extension styling. Instead of `shell.php` use:
     - `shell.PHP`
     - `shell.PhP`
     - `shell.php4`
     - `shell.php5`

   > **Note:** Modern PHP versions no longer allow the use of the null byte (`%00`) to bypass file extension validation. However, this may still work in certain older PHP setups, so it’s best to check specific configurations.

2. **Bypass `Content-Type`**

   - If the application validates `Content-Type` change the it to a safe one (`image/jpeg`) while uploading a malicious file (`file.php`).

3. **Bypass File Content**

   - If the application validates the content of file, try to inject it into a legitimate image. You can use `exiftool` to do so.

4. **Executable Extensions:**

   1. `.php`
   2. `.jsp`
   3. `.asp`
   4. `.exe`
   5. `.sh`

## How to prevent a File Upload Vulnerability?

To protect your website from File Upload Vulnerabilities, you can implement any of these measures:

1. Enforce strict file size limits to prevent DOS attacks.
2. Whitelist the permitted extensions.
3. Ensure that filenames do not contain substrings that may be interpreted as directory traversal sequences (e.g., `../`)
4. Rename uploaded files to avoid collisions that may overwrite existing files. 
5. Do not upload files to the server’s permanent filesystem until they have been fully validated.
6. Ensure uploaded files are within size limits to prevent DoS attacks.
7. Restrict allowed file types to a whitelist of safe extensions.
8. Avoid directory traversal by removing dangerous characters (`../`).
9. Generate unique names for uploaded files to avoid collisions.
10. Upload files to non-executable directories (e.g., outside the web root).
11. Use libraries like `finfo` in PHP to validate that the file contents match the expected file type.

## Time to practice

Now let's practice on some labs that I made. Our goal in these challenges is to access files on the server.

So, let's start with the first challenge.

### Challenge #1

We can see there is file upload functionality, so let's use it as normal user and identify how it works.

So, first we upload a normal `png` image 

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/Challenge_intro.png)

![Challenge_test](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/Challenge_test.png)

We can access it using `/uplaods` directory.

![uploaded_image](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/uploaded_image.png)

Now, let's upload the same image again and intercept the request using Burp Suite.

![Challenge_test_2](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/Challenge_test_2.png)

So, we can play with `filename` and check whether we can change the file extension to `.php`.

![Challenge_test_3](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/Challenge_test_3.png)

We can see above that the file extension changed without any errors indicating that there is no validation on it.

Now, let's change the file content to `php` and inject a web shell.

![Challenge_test_4](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/Challenge_test_4.png)

Since it’s uploaded successfully, let’s access the file and execute commands on the server

The first command we can use is `id` to know who we are in the server.

![Challenge_exploit_1.2](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/Challenge_exploit_1.2.png)

As we are `root` let's try `ls` to list all files. We observe the there is a file called `flag.txt`.

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/Challenge_exploit_1.png)

Let's read it and we solve the challenge.



![Challenge_exploit_1.3](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_1/Challenge_exploit_1.3.png)

### Challenge #2

Second Challenge from Portswigger, we can see in the lab description that our goal is to read `/home/carlos/secret` file to solve the lab. So let's start and access the lab. 

![Challenge_Description](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_Description.png)

We can see it's a blog and we can login using `My account` in the top right, so let's login with default credentials: `wiener:peter`.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_intro.png)

![Challenge_login](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_login.png)

We can see in our profile that we can upload avatar photo, so let's upload normal photo.

![Challenge_upload](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_upload.png)

The image uploads successfully in `avatars/` directory, but when we access it, it shows a `Not Found` error.

![Challenge_upload_2](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_upload_2.png)

![Challenge_image_not_fond](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_image_not_fond.png)

So, we can go to any blog post and click on the image to see the full path.

![Challenge_upload_3](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_upload_3.png)

![Challenge_uploaded_image](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_uploaded_image.png)

Now let's try to abuse file upload functionality, so let's go back to upload an image and intercept the request to burp repeater.

![Challenge_upload_burp](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_upload_burp.png)

When we try changing the file extension to `.php`, the application returns the following error:

![Challenge_test_1](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_test_1.png)

We can bypass the filtration using the `null byte` technique, which allows us to rename the file from `blackhat.png` to `blackhat.php%00.png`, successfully bypassing the filter.

![Challenge_test_2](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_test_2.png)

As we see above the file uploaded successfully as this cause discrepancies in what is treated as the end of the filename, now let's change the file content to `php`.

![Challenge_test_3](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_test_3.png)

Let's to access the upload file and we can execute system commands successfully.

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_exploit_1.png)

Finally let's try to read `/home/carlos/secret` file and solve the lab.

![Challenge_exploit_2](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_exploit_2.png)

![Challenge_exploit_3](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_exploit_3.png)

![Challenge_solve](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_2/Challenge_solve.png)

### Challenge #3

As in the previous challenge, the goal is to read the `/home/carlos/secret` file. So, let’s start by checking the web page.

![Challenge_description](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_description.png)

We can see it's a blog and we can login, so let's do so using default credentials: `wiener:peter`.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_intro.png)

![Challenge_login](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_login.png)

Let's upload an avatar image.

![Challenge_upload](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_upload.png)

![Challenge_upload_2](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_upload_2.png)

Let's intercept the request to repeater and change the filename to `blackhat.php`.

![Challenge_test_1](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_test_1.png)

We can see the file upload successfully which indicates there is no filtering on the filename. So, let's change content of the file to `php` code.

We can see that the application filter `Content-Type`. So, how can we bypass this filter?

![Challenge_filter](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_filter.png)

Some applications filter `Content-Type` header, but it fails in validating file content, which means the server only allows types like `image/jpeg` and `image/png`, but not checking whether the contents of the file actually match the supposed MIME type.

So, let's change `Content-Type` to `image/jpeg` and write `php` code into content.

![Challenge_exploit_3](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_exploit_3.png)

Let's view the file in browser and we can read `/home/carlos/secret` file successfully.

![Challenge_exploit_2](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challenge_exploit_2.png)

![Challege_solve](/assets/images/tutorials/Web_Security_Vulnerabilities/File_Upload/Challenge_3/Challege_solve.png)

## Resources

[PortSwigger - File Upload Vulnerabilities](https://portswigger.net/web-security/file-upload)

[OWASP - Unrestricted_File_Upload](https://owasp.org/www-community/vulnerabilities/Unrestricted_File_Upload)

[Intigriti - File Upload Vulnerabilties](https://www.intigriti.com/hackademy/file-upload-vulnerabilities)

[Cobalt - File Upload Vulnerabilities](https://www.cobalt.io/blog/file-upload-vulnerabilities)

[HacktTricks - File Upload Vulnerabilities](https://book.hacktricks.xyz/pentesting-web/file-upload)

## Conclusion

Upload files may represent a risk on applications if the server doesn't handle uploaded file successfully. In this blog, we discussed File Upload vulnerability, how to find it, ways to prevent it and solve some challenges to have better understanding. We also highlighted some bypassing techniques and the importance of proper server-side validation to mitigate these vulnerabilities.

Hope you enjoy this tutorial! Thanks for reading.
