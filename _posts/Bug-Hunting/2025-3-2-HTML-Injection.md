---
title: "My First Bug - HTML Injection"
classes: wide
header:
  teaser: /assets/images/bug-hunting/HTML_Injection/Hackerone_Report.png
ribbon: red
description: "I found an HTML Injection in the comment section of the blog posts on the `sub.redacted.com` website that allows me to inject unintended texts, links, and images."
categories:
  - Bug-Hunting
toc: false
---

# My First Bug Bounty - HTML Injection

Hello hunters, It's `Abdelrahman Elshinbary` known as `emp3r0r10` and this is my first writeup in the bug bounty community that I found in a `Vulnerability Disclosure Program (VDP)`. Let's call it `redacted.com`.

### Summary

I found an HTML Injection in the comment section of the blog posts on the `sub.redacted.com` website that allows me to inject unintended texts, links, and images.

### Recon

The scope of target is `*.redacted.com`, so I start to collect subdomains using subdomain enumeration tools such as [subfinder](https://github.com/projectdiscovery/subfinder).

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

While the above process is going, I was exploring the main domain `redacted.com` and check its functionalities. I found that it has signup and sign in pages, so let's create an account and login.

I can't find something interesting, I'm back to `live_subdomains.txt` file and found an interesting subdomain (we can call it `sub.redacted.com`).

So, I navigate to `sub.redacted.com` and found that it has a login page which is depend on the account registered on the main domain. As I registered an account before, I can login to `sub.redacted.com`.

After further enumeration on this subdomain, I found a directory called `/blog` and in the blogs there is a comment section.

> I can found `/blog` directory also using directory brute force tools such as [dirsearch](https://github.com/maurosoria/dirsearch).
>
> ```bash
> dirsearch -u https://sub.redacted.com/ -w /usr/share/SecLists-master/Discovery/Web-Content/directory-list-2.3-medium.txt
> ```
>

### Exploitation

I started to test it against HTML Injection and injecting `<h1>Great</h1>`, to my surprise the payload is executed successfully.

![HTML_Code](/assets/images/bug-hunting/HTML_Injection/HTML_Injection_developer_blogs - HTML_Code.png)



![HTML_Injection](/assets/images/bug-hunting/HTML_Injection/HTML_Injection_developer_blogs - HTML_Injection.png)

So, I reported it to the company and they validated my report as `P3` bug.



![Hackerone_Report](/assets/images/bug-hunting/HTML_Injection/HTML_Injection_developer_blogs - Hackerone_Report.png)

It is just a beginning. Hope you enjoyed this writeup.

Thanks for reading.