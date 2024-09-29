---
title: "SIEM Tutorial - Part 3"
classes: wide
header:
  teaser: /assets/images/tutorials/SIEM_Tutorial/Part_3/Splunk_Forwarder_3.png
ribbon: red
description: "Hello, in part 2, we covered what Splunk is, its components, explored its interface, and solve a small piece of the `botsv3` dataset. Today, we are going to dive into another functionality in Splunk."
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/SIEM_Tutorial/Part_3/Splunk_Forwarder_3.png" alt="SIEM" style="zoom: 67%;" />

# SIEM Solutions - Part 3

Hello, in **[Part 2](https://emp3r0r10.github.io/tutorials/SIEM_Tutorial_Part_2/)**, we covered what Splunk is, its components, explored its interface, and solve a small piece of the `botsv3` dataset. Today, we are going to dive into another functionality in Splunk.

In this part, we will learn how to forward data from different machines to Splunk for analysis.

## Table of Contents
  - [Lab Setup](#lab-setup)
  - [Installing Splunk Universal Forwarder](#installing-splunk-universal-forwarder)
  - [Configuring Splunk to Receive Data](#configuring-splunk-to-receive-data)
  - [Configuring Linux Splunk Forwarder](#configuring-linux-splunk-forwarder)
  - [Viewing Logs in Splunk Enterprise](#viewing-logs-in-splunk-enterprise)
  - [Attacker Simulation: Directory Brute-Forcing](#attacker-simulation-directory-brute-forcing)
  - [Creating Alerts for Suspicious Activity](#creating-alerts-for-suspicious-activity)
  - [Resources](#resources)
  - [Conclusion](#conclusion)

## Lab Setup

For this lab, we will need three virtual machines (VMs):

1. **VM #1:** for Splunk Enterprise (Windows VM)
2. **VM #2:** for Splunk Forwarder (Linux VM)

3. **VM #3:** for an attacker (Linux VM)

> **Note:** If you don't have a Windows VM, you can use your local host as the Splunk Enterprise machine.

## Installing Splunk Universal Forwarder

Let’s start by installing the Splunk Universal Forwarder. As we will use Linux, we need to download one of these distributions.

![splunk_forwarder_download_1](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_forwarder_download_1.png)

After downloading, let’s extract the files.

![splunk_setup_1](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_setup_1.png)

Let's navigate to the Splunk directory and start the setup by accepting the license agreement using: 

`./splunk start --accept-license`

> **Note:** You need to enter the login credentials as Splunk Enterprise.

![splunk_setup_2](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_setup_2.png)

## Configuring Splunk to Receive Data

Now that we’ve installed the Splunk Forwarder, we need to configure Splunk Enterprise (Windows VM) to receive data from the forwarder. Follow these steps to configure the port for receiving data:

1. Go to **Settings** -> **Forwarding and Receiving**.
2. Click on **Add new** under **New Receiving Port**.
3. Set the receiving port to `9997` (or any other port) and click **Save**.

![splunk_forwarding&receiving](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_forwarding&receiving.png)

![splunk_forward_port_1](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_forward_port_1.png)

![splunk_forward_port_2](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_forward_port_2.png)

> Port `9997` is the default for receiving forwarded data, but it can be configured differently.

Next, we need to create an index to store the logs coming from the Linux forwarder. To do this:

1. Go to **Settings** -> **Indexes**.
2. Click **New Index** and name it something like `kali_logs`.
3. Set the options and save.

![splunk_index](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_index.png)



## Configuring Linux Splunk Forwarder

Now let’s go back to the Linux machine and configure it to send logs to the Splunk server using:

`./splunk add forward-server <SPLUNK_ENTERPRISE_SERVER_IP:9997>`

Let’s restart Splunk to apply the changes.

![splunk_setup_2](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_setup_3.png)

If we go to the Splunk server, we can’t find any hosts because we haven’t monitored the logs. So, let’s monitor logs in the `/var/log` directory.

> **/var/log** contains logs from the OS itself, services, and various applications running on the system. It's like an `Event Viewer` in Windows

![splunk_setup_3](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_setup_4.png)

## Viewing Logs in Splunk Enterprise

After configuring the forwarder, let's go back to Splunk Enterprise (Windows VM) to check if the logs are coming through.

![splunk_data_summary](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_data_summary.png)

![splunk_search_hosts](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_search_hosts.png)

If we search for the `kali_logs` index, we can see all log files in `/var/log` here.

![splunk_search_index](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_search_index.png)

## Attacker Simulation: Directory Brute-Forcing

On the Linux VM (Kali), install and start `Apache2` and `SSH` services. These services generate logs that Splunk can monitor. Use the following commands:

![kali_ip](/assets/images/tutorials/SIEM_Tutorial/Part_3/kali_ip.png)

![linux_ports](/assets/images/tutorials/SIEM_Tutorial/Part_3/linux_ports.png)

Let's check the connectivity and go to `http://127.0.0.1/`.

![linux_webserver](/assets/images/tutorials/SIEM_Tutorial/Part_3/linux_webserver.png)

Now, simulate an attack using `dirb` from the attacker machine (Linux VM) against the  running `Apache` server.

![dirb](/assets/images/tutorials/SIEM_Tutorial/Part_3/dirb.png)

If we go back to Splunk search and search for logs in `/var/log/apache2/access.log`, we can see `dirb` requests logged in `/var/log/apache2/access.log`

As we learned from the previous part, we look at interesting fields and search for them in a tabular format to have a better view.

![splunk_search_SPL](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_search_SPL.png)

Don’t forget, if you want to search for something you have searched for before, you can go to `Search history`.

![splunk_search_history](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_search_history.png)

## Creating Alerts for Suspicious Activity

As we see above, an attacker tried to brute force directories, which may be suspicious activity for specific companies. So, we can create an alert for this suspicious activity.

![splunk_alert](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_alert.png)

We can set an action to send an email if an alert is triggered.

![splunk_alert_2](/assets/images/tutorials/SIEM_Tutorial/Part_3/splunk_alert_2.png)

## Resources

[Tryhackme - splunkexploringspl](https://tryhackme.com/jr/splunkexploringspl)

[Tryhackme - splunklab](https://tryhackme.com/jr/splunklab)

[Tryhackme - splunkdashboardsandreports](https://tryhackme.com/jr/splunkdashboardsandreports)

[Tryhackme - splunkdatamanipulation](https://tryhackme.com/jr/splunkdatamanipulation)

[Splunk Official Site](https://www.splunk.com/en_us/blog/learn/splunk-tutorials.html)

[Tutorialspoint](https://www.tutorialspoint.com/splunk/index.htm)

[Botsv3](https://www.youtube.com/watch?v=RXDbir6B5mE)

## Conclusion

In this tutorial, we successfully forwarded logs from a Linux machine to Splunk Enterprise, simulated an attack, and analyzed the logs using Splunk. As a summary:

- **Part 1**: Introduction to logs, monitoring, and SIEM solutions.
- **Part 2**: Introduction to Splunk, its components, and hands-on practice with a demo.
- **Part 3**: Set up a home lab with Splunk Forwarder and analyzed attack data.

If you find this tutorial helpful, you can follow me on [Twitter](https://x.com/emp3r0r10). Keep going!

Thanks for reading.