# External Libraries

## Database Abstraction Layer

doctrine/dbal - The Doctrine database abstraction and access layer (DBAL) offers a lightweight and thin runtime layer 
around a PDO-like API and a lot of additional, horizontal features like database schema introspection and manipulation 
through an OO API.
                
The fact that the Doctrine DBAL abstracts the concrete PDO API away through the use of interfaces that closely resemble 
the existing PDO API makes it possible to implement custom drivers that may use existing native or self-made APIs. 
For example, the DBAL ships with a driver for Oracle databases that uses the oci8 extension under the hood.
                
The following database vendors are currently supported:
                
  - MySQL
  - Oracle
  - Microsoft SQL Server
  - PostgreSQL
  - SAP Sybase SQL Anywhere
  - SQLite
  - Drizzle

See full documentation at [http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/)

## Filesystem Abstraction Layer

league/flysystem - Flysystem is a filesystem abstraction which allows you to easily swap out a local filesystem for a 
remote one. Technical debt is reduced as is the chance of vendor lock-in. Available adapters are 

  - Local
  - Azure
  - AWS S3 V2
  - AWS S3 V3
  - Copy.com
  - Dropbox
  - FTP
  - GridFS
  - Memory
  - Null / Test
  - Rackspace
  - ReplicateAdapter
  - SFTP
  - WebDAV
  - PHPCR
  - ZipArchive

See full documentation at [http://flysystem.thephpleague.com/](http://flysystem.thephpleague.com/)

## Command Bus

league/tactitian - Tactician is a command bus library. It tries to make using the command pattern in your application 
easy and flexible.

See full documentation at [http://tactician.thephpleague.com/](http://tactician.thephpleague.com/)

## PSR-3: Logging

monolog/monolog - Monolog sends your logs to files, sockets, inboxes, databases and various web services. 
Special handlers allow you to build advanced logging strategies.
                  
This library implements the PSR-3 interface that you can type-hint against in your own libraries to keep a maximum of 
interoperability. You can also use it in your applications to make sure you can always use another compatible logger 
at a later time. Available handlers are

Log to files and syslog

  - StreamHandler: Logs records into any PHP stream, use this for log files.
  - RotatingFileHandler: Logs records to a file and creates one logfile per day. It will also delete files older than $maxFiles. You should use logrotate for high profile setups though, this is just meant as a quick and dirty solution.
  - SyslogHandler: Logs records to the syslog.
  - ErrorLogHandler: Logs records to PHP's error_log() function.

Send alerts and emails

  - NativeMailerHandler: Sends emails using PHP's mail() function.
  - SwiftMailerHandler: Sends emails using a Swift_Mailer instance.
  - PushoverHandler: Sends mobile notifications via the Pushover API.
  - HipChatHandler: Logs records to a HipChat chat room using its API.
  - FlowdockHandler: Logs records to a Flowdock account.
  - SlackHandler: Logs records to a Slack account.
  - MandrillHandler: Sends emails via the Mandrill API using a Swift_Message instance.
  - FleepHookHandler: Logs records to a Fleep conversation using Webhooks.
  - IFTTTHandler: Notifies an IFTTT trigger with the log channel, level name and message.

Log specific servers and networked logging

  - SocketHandler: Logs records to sockets, use this for UNIX and TCP sockets. See an example.
  - AmqpHandler: Logs records to an amqp compatible server. Requires the php-amqp extension (1.0+).
  - GelfHandler: Logs records to a Graylog2 server.
  - CubeHandler: Logs records to a Cube server.
  - RavenHandler: Logs records to a Sentry server using raven.
  - ZendMonitorHandler: Logs records to the Zend Monitor present in Zend Server.
  - NewRelicHandler: Logs records to a NewRelic application.
  - LogglyHandler: Logs records to a Loggly account.
  - RollbarHandler: Logs records to a Rollbar account.
  - SyslogUdpHandler: Logs records to a remote Syslogd server.
  - LogEntriesHandler: Logs records to a LogEntries account.

Logging in development

  - FirePHPHandler: Handler for FirePHP, providing inline console messages within FireBug.
  - ChromePHPHandler: Handler for ChromePHP, providing inline console messages within Chrome.
  - BrowserConsoleHandler: Handler to send logs to browser's Javascript console with no browser extension required. Most browsers supporting console API are supported.
  - PHPConsoleHandler: Handler for PHP Console, providing inline console and notification popup messages within Chrome.

Log to databases

  - RedisHandler: Logs records to a redis server.
  - MongoDBHandler: Handler to write records in MongoDB via a Mongo extension connection.
  - CouchDBHandler: Logs records to a CouchDB server.
  - DoctrineCouchDBHandler: Logs records to a CouchDB server via the Doctrine CouchDB ODM.
  - ElasticSearchHandler: Logs records to an Elastic Search server.
  - DynamoDbHandler: Logs records to a DynamoDB table with the AWS SDK.

Wrappers / Special Handlers

  - FingersCrossedHandler: A very interesting wrapper. It takes a logger as parameter and will accumulate log records of all levels until a record exceeds the defined severity level. At which point it delivers all records, including those of lower severity, to the handler it wraps. This means that until an error actually happens you will not see anything in your logs, but when it happens you will have the full information, including debug and info records. This provides you with all the information you need, but only when you need it.
  - WhatFailureGroupHandler: This handler extends the GroupHandler ignoring exceptions raised by each child handler. This allows you to ignore issues where a remote tcp connection may have died but you do not want your entire application to crash and may wish to continue to log to other handlers.
  - BufferHandler: This handler will buffer all the log records it receives until close() is called at which point it will call handleBatch() on the handler it wraps with all the log messages at once. This is very useful to send an email with all records at once for example instead of having one mail for every log record.
  - GroupHandler: This handler groups other handlers. Every record received is sent to all the handlers it is configured with.
  - FilterHandler: This handler only lets records of the given levels through to the wrapped handler.
  - SamplingHandler: Wraps around another handler and lets you sample records if you only want to store some of them.
  - NullHandler: Any record it can handle will be thrown away. This can be used to put on top of an existing handler stack to disable it temporarily.
  - PsrHandler: Can be used to forward log records to an existing PSR-3 logger
  - TestHandler: Used for testing, it records everything that is sent to it and has accessors to read out the information.

Formatters

  - LineFormatter: Formats a log record into a one-line string.
  - HtmlFormatter: Used to format log records into a human readable html table, mainly suitable for emails.
  - NormalizerFormatter: Normalizes objects/resources down to strings so a record can easily be serialized/encoded.
  - ScalarFormatter: Used to format log records into an associative array of scalar values.
  - JsonFormatter: Encodes a log record into json.
  - WildfireFormatter: Used to format log records into the Wildfire/FirePHP protocol, only useful for the FirePHPHandler.
  - ChromePHPFormatter: Used to format log records into the ChromePHP format, only useful for the ChromePHPHandler.
  - GelfMessageFormatter: Used to format log records into Gelf message instances, only useful for the GelfHandler.
  - LogstashFormatter: Used to format log records into logstash event json, useful for any handler listed under inputs here.
  - ElasticaFormatter: Used to format log records into an Elastica\Document object, only useful for the ElasticSearchHandler.
  - LogglyFormatter: Used to format log records into Loggly messages, only useful for the LogglyHandler.
  - FlowdockFormatter: Used to format log records into Flowdock messages, only useful for the FlowdockHandler.
  - MongoDBFormatter: Converts \DateTime instances to \MongoDate and objects recursively to arrays, only useful with the MongoDBHandler.

Processors

  - PsrLogMessageProcessor: Processes a log record's message according to PSR-3 rules, replacing {foo} with the value from $context['foo'].
  - IntrospectionProcessor: Adds the line/file/class/method from which the log call originated.
  - WebProcessor: Adds the current request URI, request method and client IP to a log record.
  - MemoryUsageProcessor: Adds the current memory usage to a log record.
  - MemoryPeakUsageProcessor: Adds the peak memory usage to a log record.
  - ProcessIdProcessor: Adds the process id to a log record.
  - UidProcessor: Adds a unique identifier to a log record.
  - GitProcessor: Adds the current git branch and commit to a log record.
  - TagProcessor: Adds an array of predefined tags to a log record.

See full documentation at [https://github.com/Seldaek/monolog](https://github.com/Seldaek/monolog)

## PSR-6: Caching

matthiasmullie/scrapbook - Scrapbook is a caching environment for PHP, with adapters for different storage engines
and additional capabilities (e.g. transactions, stampede protection) built on top. Supported adapters are

  - Memcached
  - Redis
  - Couchbase
  - APC
  - MySQL
  - SQLite
  - PostgreSQL
  - Flysystem
  - Memory

See full documentation at [http://www.scrapbook.cash](http://www.scrapbook.cash)

## PSR-7: HTTP

guzzlehttp/guzzle - Guzzle is a PHP HTTP client that makes it easy to send HTTP requests and trivial to integrate with 
web services.

Simple interface for building query strings, POST requests, streaming large uploads, streaming large downloads, using 
HTTP cookies, uploading JSON data, etc...
Can send both synchronous and asynchronous requests using the same interface.
Uses PSR-7 interfaces for requests, responses, and streams. This allows you to utilize other PSR-7 compatible libraries 
with Guzzle.
Abstracts away the underlying HTTP transport, allowing you to write environment and transport agnostic code; i.e., 
no hard dependency on cURL, PHP streams, sockets, or non-blocking event loops.
Middleware system allows you to augment and compose client behavior.

See full documentation at [http://docs.guzzlephp.org/en/latest/](http://docs.guzzlephp.org/en/latest/)
