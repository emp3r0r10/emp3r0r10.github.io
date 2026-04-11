---
title: "My First Bug - HTML Injection"
classes: wide
header:
  teaser: /assets/images/bug-hunting/HTML_Injection/Writeup_Cover.png
ribbon: red
description: "I found an HTML Injection in the comment section of the blog posts on the `sub.redacted.com` website that allows me to inject unintended texts, links, and images."
categories:
  - Bug-Hunting
toc: false
---

Hello hunters, my name is`Abdelrahman Elshinbary` also known as `emp3r0r10`. This is my first writeup in the bug bounty community that I found a vulnerability in a `Vulnerability Disclosure Program (VDP)`. Let's call it `redacted.com`.

### Summary

I found an HTML Injection vulnerability in the comment section of the blog posts on the `sub.redacted.com` website. It allows me to inject unintended texts, links, and images.

### Recon

Scope of the target is `*.redacted.com`, so I started collecting subdomains using subdomain enumeration tools such as [subfinder](https://github.com/projectdiscovery/subfinder).

> **Why I used subfinder?**
>
> - Fast and efficient
>
> - Uses many passive sources
>
> - Supports API keys for enhanced results
>
> - Integrates well with other tools (e.g., Amass, Assetfinder)

> I can use many other tools for subdomain enumeration such as sublist3r, amass or online subdomain enumeration [subdomainfinder.c99.nl](https://subdomainfinder.c99.nl/), but in this case I used one tool (which is subfinder) as I wanted to focus on the application itself not recon tools  

```bash
subfinder -all -d redacted.com -o subfinder_domains.txt -recursive
```

`-all` Use all sources for enumeration.

`-d` For a specific domain.

`-o` For output file.

`-recursive` To find subdomains of the discovered subdomains.

Then I used [httpx](https://github.com/projectdiscovery/httpx) to filter live subdomains:

```bash
cat subfinder_domains.txt | httpx -status-code -location -fc 404 -o live_subdomains.txt
```

`-status-code` Display response status-code

`-location` Display response redirect location

`-fc` Filter response with specified status code

`-o` For output file.

While the above process was running, I explored the main domain `redacted.com` and checked its functionalities. I found that it has signup and sign in pages, so let's create an account and login.

I couldn't find anything interesting, I'm back to `live_subdomains.txt` file and found an interesting subdomain (we can call it `sub.redacted.com`).

So, I navigate to `sub.redacted.com` and found that it has a login page which is depend on the account registered on the main domain. Since I had already registered an account, I was able to log in to `sub.redacted.com`.

After further enumeration on this subdomain and clicking on every link, I found a directory called `/blog` and in the blogs there is a comment section.

> I could found `/blog` directory also using directory brute force tools such as [dirsearch](https://github.com/maurosoria/dirsearch).
>
> ```bash
> dirsearch -u https://sub.redacted.com/ -w /usr/share/SecLists-master/Discovery/Web-Content/directory-list-2.3-medium.txt
> ```
>

> There are many other tools for directory brute force, such as **gobuster**, **feroxbuster**. you may wonder why I didn't use any of these tools. The answer is simple as I am beginner and didn't want to distract myself as I knew each tool and there is a small differences between them like **dirsearch** and **feroxbuseter** has recursive scanning but **gobuster** not.
>

### Exploitation

I started to test it against HTML Injection and injecting `<h1>Great</h1>`, to my surprise the payload is executed successfully.

![HTML_Code](/assets/images/bug-hunting/HTML_Injection/HTML_Code.png)

![HTML_Injection](/assets/images/bug-hunting/HTML_Injection/HTML_Injection.png)

So, I reported it to the company and they validated my report as `P3` bug (**medium** severity).

![Hackerone_Report](/assets/images/bug-hunting/HTML_Injection/Hackerone_Report.png)

This is just a beginning in my bug bounty journey. I hope you enjoyed this writeup.

Thanks for reading.