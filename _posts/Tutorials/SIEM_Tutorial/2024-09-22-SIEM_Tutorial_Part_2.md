---
title: "SIEM Tutorial - Part 2"
classes: wide
header:
  teaser: /assets/images/tutorials/SIEM_Tutorial/Part_2/Splunk.png
ribbon: red
description: "Hello security analysts, today I'm back with the second part of the SIEM tutorial. In the first part, we discussed logs and events, understanding log monitoring and analysis, how to perform it, and we took a look at what SIEM is along with its benefits and solutions."
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/SIEM_Tutorial/Part_2/Splunk.png" alt="SIEM" style="zoom: 100%;" />

## Table of Contents
- [SIEM Solutions (Splunk)](#siem-solutions-splunk)
- [Introduction to Splunk](#introduction-to-splunk)
- [How Splunk Works](#how-splunk-works)
  - [Splunk Data Pipeline](#splunk-data-pipeline)
  - [Data Ingestion](#data-ingestion)
  - [Data Parsing & Indexing](#data-parsing--indexing)
- [Splunk Components](#splunk-components)
  - [Forwarder](#forwarder)
    - [Universal Forwarder](#universal-forwarder)
    - [Heavy Forwarder](#heavy-forwarder)
  - [Indexer](#indexer)
  - [Search Head](#search-head)
- [Splunk Interface](#splunk-interface)
  - [Search & Reporting](#search--reporting)
  - [Dashboard](#dashboard)
  - [Add Data](#add-data)
- [Splunk Demo](#splunk-demo)
  - [Setting Up Splunk](#setting-up-splunk)
  - [Basic Search Queries](#basic-search-queries)
  - [Creating Dashboards](#creating-dashboards)


# SIEM Solutions (Splunk)

Hello analysts, today I'm back with the second part of the SIEM tutorial. In the first part, we discussed logs and events, understanding log monitoring and analysis, how to perform it, and we took a look at what SIEM is along with its benefits and solutions.

Today, we will explore one of the most widely used SIEM solutions: **Splunk**. Without wasting time, let's delve into `Splunk`.

We'll cover:

1. Introduction to Splunk
2. How Splunk works
3. Explanation of Splunk components

## Introduction to Splunk

Splunk is one of the leading SIEM tools with the ability to collect, analyze, and correlate network and machine data in real-time.

> **Machine data**: This is generated from security technologies such as network, endpoint, access, malware, vulnerability, and identity information, or any data created by users.

## How Splunk works

Splunk works by collecting, storing, and aggregating data from different sources, indexing it in a searchable format, and then providing powerful search and reporting capabilities to help users monitor systems and applications. Splunk can collect data from various sources, including logs, metrics, and events.

## Splunk components

### Forwarder

Splunk Forwarder is a lightweight agent installed on the endpoint intended to be monitored. It is used to collect data and send it to the indexer. It does not significantly affect the endpoint’s performance as it uses very few resources. You can use Forwarder for real-time monitoring, configuring it on the machine you want to monitor to send data to Splunk indexers.

#### Universal Forwarder

The Universal Forwarder is a simple component. It is used to forward data only without indexing or making changes to the data.

> When using the Universal Forwarder, the indexer will parse and then index the data.

#### Heavy Forwarder

The Heavy Forwarder also forwards data to the indexer, but it performs parsing and indexing at the source. It intelligently routes the data to the indexer, saving on bandwidth and storage space.

> When using the Heavy Forwarder, the indexer will index the data only.

### Indexer

After data is forwarded, the indexer indexes and stores it. The Splunk instance transforms raw data into events and stores them in indexes for performing search operations.

### Search Head

With data being forwarded and indexed, you can search for what you want using the search head. Splunk uses Search Processing Language (SPL) to get the desired results from the datasets.

**Some SPL commands used while searching:**

- `table`: Specifies fields to keep in the result set, retaining data in a tabular format.
- `rename`: Renames a field; you can use wildcards for multiple fields.
- `dedup`: Removes duplicate results that match certain criteria.
- `sort`: Sorts the results by the specified field, which can be in ascending or descending order.
- `head/tail`: Returns the top/bottom N results.

## Splunk Interface

Now that we know what Splunk is and its components, let’s move to the application and explore it.

First, let’s download Splunk:

1. Go to Splunk Download.
2. Login/Register for an account.
3. Download the Splunk Enterprise Windows version (I will use it for this tutorial).
4. Start the installation process and follow the instructions.

![splunk_download_1](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_download_1.png)

![splunk_download_2](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_download_2.png)

![Splunk_download_3](/assets/images/tutorials/SIEM_Tutorial/Part_2/Splunk_download_3.png)

Now open Splunk and enter the login credentials from the above installation.

![Splunk_start](/assets/images/tutorials/SIEM_Tutorial/Part_2/Splunk_start.png)

![splunk_dashboard](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_dashboard.png)

We can see above there are different sections in the app, let’s explore some of them:

### Search & Reporting

Here we can search for logs. We can see the latest search queries that we used in `Search history`.

![Splunk_search_interface](/assets/images/tutorials/SIEM_Tutorial/Part_2/Splunk_search_interface.png)

We can see latest search queries that we use in `Search history`.

![splunk_search_history](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_search_history.png)

### Dashboard

We can save our project to an existing dashboard or new one.

![splunk_dashboards](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_dashboards.png)

![splunk_create_dashboard](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_create_dashboard.png)

### Add Data

As Splunk can ingest any data, we can add data using three ways:

1. Upload a file (CSV or JSON)
2. Monitor
3. Forwarder (we will use it in the third part)

Let’s try to upload a `.csv` file. Ensure the source type is `csv`.

![Splunk_add_data_0](/assets/images/tutorials/SIEM_Tutorial/Part_2/Splunk_add_data_0.png)

Let's try to upload `.csv` file.

![splunk_add_data_1](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_add_data_1.png)

![splunk_add_data_2](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_add_data_2.png)

Ensure the source type is `csv`.

![splunk_add_data_3](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_add_data_3.png)

Edit `Host field value` and select the index where these logs will be dumped and `hostName` to be associated with the logs.

**Note:** you can create your own index.

![splunk_add_data_4](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_add_data_4.png)

![splunk_add_data_5](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_add_data_5.png)

Now start searching for logs.

![splunk_add_data_6](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_add_data_6.png)

## Splunk Demo

Let’s put everything together and practice with a demo.

For today’s demo, we will install <a href='https://github.com/splunk/botsv3'>botsv3</a>. Follow the installation process and start the investigation.

First, type the index to access all logs under `botsv3`.

![splunk_search_1](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_search_1.png)

Then, look at `sourcetype` and select `stream:ip` as an example for our investigation.

![splunk_search_2](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_search_2.png)

We can see on the left side a lot of fields, which provide an overview of what we are searching for.

![ip_fields](/assets/images/tutorials/SIEM_Tutorial/Part_2/ip_fields.png)

So, let’s get the `_time`, `dest_ip`, `dest_mac`, `src_ip`, `src_mac`, and `count` fields, as these fields give us an idea of who is accessing this device.

Now we will use SPL to search for these queries. We can use `table` to return data in a tabular format.

`(src_mac=* AND dest_mac=*)` filters the events to only include those where both `src_mac` and `dest_mac` fields are present.

![splunk_search_3](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_search_3.png)

We can see above there are duplicated IPs in `src_ip`, which might be suspicious, so let’s see how many times those IPs appeared.

We will use `stats latest(_time) as _time count by src_ip src_mac`.

`stats` aggregates data based on the specified fields.

`latest(_time) as _time` finds the latest `_time` for each combination of `src_ip` and `src_mac`.

`count by src_ip src_mac` counts the number of events for each combination of `src_ip` and `src_mac`.

![splunk_search_4](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_search_4.png)

Let’s sort them according to their count and display the country of each IP address.

The final search query:

```
index=botsv3 earliest=0 sourcetype="stream:ip" (src_mac=* AND dest_mac=*) 
| stats latest(_time) as _time count by src_ip src_mac
| table _time dest_ip dest_mac src_ip src_mac count
| iplocation src_ip 
| sort - count 
| search Country=* 
```

![splunk_search_5](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_search_5.png)

We can see above that the IP `104.128.69.207` has a high count compared to the rest of the IPs, which is very suspicious.

So we can create an alert for a particular search. Let’s say that if the count is greater than 100, this is considered suspicious activity.

We will add `search count > 100` and click on `Save As`, then choose

![splunk_alert_0](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_alert_0.png)

We can type a title for the alert and specify what action should be taken.

![splunk_alert_1](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_alert_1.png)

We set up an email as an action. If the alert is triggered, an email will be sent to the analyst with a message, so they can take action and block this IP.

![splunk_alert_2](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_alert_2.png)

![splunk_alert_3](/assets/images/tutorials/SIEM_Tutorial/Part_2/splunk_alert_3.png)

By now, we have learned how to install Splunk, forward logs, search logs, create dashboards, and create alerts.

In the final part, we will connect forwarders from different machines, perform an attack, and monitor logs using Splunk.

Thanks for reading.