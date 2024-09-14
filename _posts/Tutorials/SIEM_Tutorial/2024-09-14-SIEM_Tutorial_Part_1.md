---
title: "SIEM Tutorial - Part 1"
classes: wide
header:
  teaser: /assets/images/tutorials/SIEM_Tutorial/Part_1/SIEM.png
ribbon: red
description: "Hello everyone, today I'm going to share with you a SIEM tutorial that consists of three parts:"
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/SIEM_Tutorial/Part_1/SIEM.png" alt="SIEM" style="zoom: 67%;" />

## Table of Contents
- [Introduction](#introduction)
- [Introduction to Logs and Events](#introduction-to-logs-and-events)
  - [What Are Events?](#what-are-events)
  - [What Are Logs?](#what-are-logs)
  - [Windows Event Logs](#windows-event-logs)
  - [Linux Event Logs](#linux-event-logs)
- [Introduction to Log Analysis and Monitoring](#introduction-to-log-analysis-and-monitoring)
  - [Why Monitor and Analyze Logs?](#why-monitor-and-analyze-logs)
  - [How to Perform Log Analysis?](#how-to-perform-log-analysis)
  - [Popular Log Management Systems](#popular-log-management-systems)
- [Introduction to SIEM](#introduction-to-siem)
  - [SIEM Works by Combining Two Technologies](#siem-works-by-combining-two-technologies)
  - [SIEM Input Sources](#siem-input-sources)
  - [Popular SIEM Solutions](#popular-siem-solutions)
  - [How to Choose a SIEM Solution](#how-to-choose-a-siem-solution)


## Introduction

Hello everyone, today I'm going to share with you a SIEM tutorial that consists of three parts:

1. **Part #1**
   1. Introduction to logs and events
   2. Introduction to logs analysis and monitoring
   3. Introduction to SIEM and its solutions
2. **Part #2**
   1. Introduction to Splunk
   2. How Splunk works
   3. Explain Splunk components
   4. Practice on a demo
3. **Part #3**
   1. Explore Splunk forwarder and create home lab

In **part 1**, we will start by understanding the basics of logs and events, which are essential for tracking activities within IT systems. Then, we will explore how to analyze and monitor these logs effectively to gain insights and detect anomalies. Finally, we will introduce SIEM solutions, which combine log management, analysis, and monitoring into a comprehensive security management system.

So, let's get started. Before we delve into SIEM and its capabilities, we need to understand some foundational concepts. We will cover two important concepts:	

**Logs and Events**: Understanding what logs and events are, where they come from, their types, and examples of each one.

**Log Analysis and Monitoring**: Understanding the processes and tools involved in analyzing and monitoring logs to identify and respond to security incidents.

## Introduction to Logs and Events

### What Are Events?

Events are actions that occur in a system at any time and are recorded in logs.

**Types of Events:**

- **Error:** Indicates a significant problem such as loss of data or functionality. For example, if a service fails to load during startup, an error event is logged.
- **Warning:** Indicates a possible future problem that is not necessarily significant at the moment. For example, when disk space is low, a warning event is logged.
- **Information:** Describes the successful operation of an application, driver, or service. For example, when a network driver loads successfully, an information event is logged.
- **Success Audit:** Records an audited security access attempt that is successful. For example, a user’s successful attempt to log on to the system is logged as a success audit event.
- **Failure Audit:** Records an audited security access attempt that fails. For example, if a user tries to access a network drive and fails, the attempt is logged as a failure audit event.

**Examples:**

1. When a user logs in to the system, this action will be recorded in logs.
2. When a system shows an error or warning alert.
3. If you enable a firewall and a suspected IP tries to bypass it.

### What Are Logs?

**Logs** are sets of recorded events in a system. essentially serving as a history of events. They provide detailed information about each event.

**Types of Logs:**

- **System Logs:** Record events associated with the operating system, such as hardware changes, device drivers, system changes, and other activities related to the device.
- **Security Logs:** Record events related to logon and logoff activities on a device. These logs are an excellent source for analysts to investigate attempted or successful unauthorized activity.
- **Application Logs:** Record events related to applications installed on a system, including application errors, events, and warnings.
- **Directory Service Events:** Record Active Directory changes and activities, mainly on domain controllers.
- **File Replication Service Events:** Record events associated with Windows Servers during the sharing of Group Policies and logon scripts to domain controllers.
- **DNS Event Logs:** Record domain events on DNS servers.
- **Custom Logs:** Events logged by applications that require custom data storage.

### Windows Event Logs

We can access logs and events in Windows using `Event Viewer`.

![Windows_Logs](/assets/images/tutorials/SIEM_Tutorial/Part_1/Windows_Logs.png)

If we go to Windows logs, we can see categories of logs. Let’s access one of them to view the events.

![Windows_Events](/assets/images/tutorials/SIEM_Tutorial/Part_1/Windows_Events.png)

### Linux Event Logs

Like Windows, Linux stores its logs in the `/var/log` directory.

![Linux_Logs](/assets/images/tutorials/SIEM_Tutorial/Part_1/Linux_Logs.png)

We can see above It's different from windows, let's try to explore nginx logs and events. 

![Linux_Events](/assets/images/tutorials/SIEM_Tutorial/Part_1/Linux_Events.png)

## Introduction to Log Analysis and Monitoring

Log monitoring involves tracking logs and observing log data to detect issues, ensure system health, and maintain security. 

Log analysis entails understanding system behavior and identifying issues in event logs.

### Why Monitor and Analyze Logs?

Monitoring and analyzing logs help detect suspicious activity, ensure security, and maintain compliance.

### How to Perform Log Analysis?

Log analysis is performed using software solutions that collect, sort, and store logs in a centralized location called a `Log Management System`.

**Log Management**  involves continuously gathering, storing, processing, synthesizing, and analyzing data from various programs and applications to optimize system performance, identify technical issues, better manage resources, strengthen security, and improve compliance.

**Log Management System** includes:

- **Ingestion:** Gathering information about the system, including OS, applications, errors, and networks.
- **Centralization:** Aggregating all data collected from ingestion in a centralized location with a standardized format regardless of the log source.
- **Search and Analysis:** Finding specific logs, errors, and suspicious activities using powerful search capabilities and understanding the context and cause of incidents.
- **Monitoring and Alerting:** Leveraging log analytics to continuously monitor the log for any event that requires attention or human intervention.
- **Reporting:** Providing streamlined reports of all events.

### Popular Log Management Systems

1. Splunk
2. ELK Stack (Elasticsearch, Logstash, Kibana)
3. Graylog

Now that we understand what logs and events are, and have seen examples of them, as well as explored log analysis and monitoring, let's delve into another critical topic: SIEM (Security Information and Event Management) and how it can benefit monitoring and analysis.

## Introduction to SIEM

SIEM (Security Information and Event Management) is a software solution that collects and aggregates data from various systems into a centralized location. It helps organizations monitor logs, detect security threats, and identify vulnerabilities before they cause damage.

SIEM identifies unwanted behavior or suspicious patterns within logs based on conditions set by analysts in the form of rules. When these conditions are met, a rule is triggered, generating an alert that prompts further investigation.

### SIEM Works by Combining Two Technologies

1. **Security Information Management (SIM):** Collects data from log files for analysis and reporting on security threats and events.
2. **Security Event Management (SEM):** Conducts real-time system monitoring, notifies network administrators of critical issues, and establishes correlations between security events.

### SIEM Input Sources

- **Network Devices:** Routers, switches, bridges, wireless access points, modems, line drivers, hubs
- **Servers:** Web, proxy, mail, FTP servers
- **Security Devices:** Intrusion prevention systems (IPS), firewalls, antivirus software, content filter devices, intrusion detection systems (IDS), and more
- **Applications:** Any software used on the above devices
- **Cloud and SaaS Solutions:** Software and services not hosted on-premises

### Popular SIEM Solutions

1. **Exabeam Fusion**
2. **Splunk**
3. **LogRhythm**
4. **IBM QRadar SIEM**
5. **Securonix**
6. **McAfee Enterprise Security Manager**

### How to Choose a SIEM Solution

1. **Scalability:** Ensure the SIEM solution can scale to accommodate your organization's growth and increasing data volumes.
2. **Integration:** Check compatibility with your existing infrastructure and the ability to integrate with other security tools and systems.
3. **Dashboard:** Evaluate the ease of use for SOC analysts to collect and analyze data using the dashboard.
4. **Analytics and Correlation:** Look for advanced analytics and event correlation capabilities to detect and respond to security threats.
5. **Real-Time Monitoring and Alerting:** Ensure the solution provides real-time monitoring and alerting to quickly identify and respond to incidents.
6. **Threat Intelligence:** Evaluate the integration of threat intelligence feeds to enhance detection capabilities.

In the rest of this tutorial, we will use **Splunk**. Splunk is a powerful and versatile SIEM solution that provides comprehensive log management and security analytics capabilities. It is widely used by organizations to gain real-time insights into their security posture and to detect and respond to threats efficiently.

Thank for reading.