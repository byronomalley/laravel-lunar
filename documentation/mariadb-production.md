## Performance Tuning in MariaDB

- To enhance the performance of MariaDB, several areas need attention, including hardware optimization, memory utilization, disk configuration, and CPU performance.
- Memory
  - Efficient memory usage is crucial for optimal performance in MariaDB, as it heavily relies on memory for query processing and data caching.
  - Furthermore, dedicated hardware for the database can significantly improve performance. By using dedicated resources, MariaDB can operate independently without sharing them with other applications, resulting in better performance.
  - Allocating an adequate amount of memory to the system is essential. However, it is crucial to strike a balance between the available memory and other concurrent applications running on the server to prevent excessive disk I/O.
- disks
  - Disks play a critical role in the performance of MariaDB; one way to optimize disks is to use a Redundant Array of Independent Disks (RAID) configuration to improve data redundancy and increase disk I/O performance. RAID configurations allow data to be stored across multiple disks so that the data can be reconstructed from the remaining disks if one disk fails.
  - Another strategy for optimizing disks for MariaDB is to ensure that the disks are properly aligned. Disk alignment refers to how data is written to the disk; improper alignment can result in slower performance.
  - To optimize disk alignment for MariaDB, it is recommended to use partition alignment tools or to manually set the partition alignment during installation. Proper disk alignment, along with the use of RAID configurations and SSDs, can significantly improve the performance of MariaDB by reducing disk I/O bottlenecks and minimizing the risk of data loss due to disk failure.
## Dockerfile packages

- **ntp** - Sync server’s time.
- **pv** - Monitor data through a pipeline, can also be used for throttling.
- **socat** - Data streaming tool, good for streaming backup.
- **htop** - Host monitoring tool.
- **innotop** - MySQL monitoring tool.
- **vim** - Text editor with syntax highlighting (or any preferred text editor).
- **easy_install** -
- **mailutils** - MTA client.
- **bind**-utils -
- **sysstat** -
- **net**-tools -
- **telnet** -
- **openssl** - Toolkit for the Transport Layer Security (TLS) and Secure Sockets Layer (SSL) protocols.
- **lm_sensors** -
- **ipmitool**
- **lm-sensors** - analysing temperatures of hardware


## Configuring the Open Files Limit: ulimits



To ensure good server performance, the total number of client connections, database files,
and log files must not exceed the maximum file descriptor limit on the operating system (ulimit -n).
Linux systems limit the number of file descriptors that any one process may open to 1,024 per process.
On active database servers (especially production ones) it can easily reach the default system limit.

Linux systems typically limit the number of file descriptors to 1,024 per process.
To increase this limit for active database servers, follow these steps:

Edit /etc/security/limits.conf: Open the file using a text editor and specify or add the following lines:

```
mysql soft nofile 65535
mysql hard nofile 65535
```

Restart the system: After modifying the limits, restart the system to apply the changes.

Verify the changes: Confirm that the new limits are in effect by running the following commands:

```php
$ ulimit -Sn
65535
$ ulimit -Hn
65535
```


Optionally, you can set this via mysqld_safe if you are starting the mysqld process thru mysqld_safe,

```
[mysqld_safe]
open_files_limit=4294967295
```


## Setting Swappiness on MariaDB on Linux

Linux Swap plays a big role in database systems. It acts like your spare tire in your vehicle,
when nasty memory leaks interfere with your work, the machine will slow down… but in most cases will
still be usable to finish its assigned task.

To adjust the swappiness parameter, which affects how the Linux kernel handles swapping, perform the following steps:

Modify swappiness dynamically: Use the following command to apply the changes immediately without requiring a server reboot:

`sysctl -w vm.swappiness=1`

Make the change persistent: Edit the `/etc/sysctl.conf` file and add the following line:

`vm.swappiness=1`

By setting the swappiness value to 1, you prioritize keeping data in memory rather than swapping it to disk.

You can optimize the disk I/O scheduler, adjust the open files limit, and fine-tune the swappiness parameter to enhance the performance of MariaDB on your Linux system by following these configuration steps.

---

## Memory

Efficient memory usage is crucial for optimal performance in MariaDB, as it heavily relies on memory for query processing and data caching.

Allocating an adequate amount of memory to the system is essential. However, it is crucial to strike a balance between the available memory and other concurrent applications running on the server to prevent excessive disk I/O.

Furthermore, dedicated hardware for the database can significantly improve performance. By using dedicated resources, MariaDB can operate independently without sharing them with other applications, resulting in better performance.

Consideration should also be given to using Solid State Drives (SSDs) instead of traditional hard disk drives. SSDs offer faster access times and higher data transfer rates, improving database performance.

---

## Disks

Disks play a critical role in the performance of MariaDB; one way to optimize disks is to use a Redundant Array of Independent Disks (RAID) configuration to improve data redundancy and increase disk I/O performance. RAID configurations allow data to be stored across multiple disks so that the data can be reconstructed from the remaining disks if one disk fails.

Another strategy for optimizing disks for MariaDB is to ensure that the disks are properly aligned. Disk alignment refers to how data is written to the disk; improper alignment can result in slower performance.

To optimize disk alignment for MariaDB, it is recommended to use partition alignment tools or to manually set the partition alignment during installation. Proper disk alignment, along with the use of RAID configurations and SSDs, can significantly improve the performance of MariaDB by reducing disk I/O bottlenecks and minimizing the risk of data loss due to disk failure.

---

## CPU

CPU performance plays a critical role in the overall performance of MariaDB, as it handles query processing and data manipulation. To optimize CPU performance, consider the following strategies:

- Utilize multicore processors: Multicore processors enable parallel query processing, improving performance, especially for complex queries.
- Increase CPU clock speed: Enhancing the CPU clock speed can result in faster query processing and data manipulation.
- Avoid CPU sharing: To prevent performance degradation, ensure the CPU is not shared with other resource-intensive applications. Dedicate hardware for MariaDB or limit the number of applications running on the same server to allocate sufficient processing power for MariaDB’s needs.

By focusing on hardware optimization, memory utilization, disk configuration, and CPU performance, you can enhance the overall performance of MariaDB and achieve better query processing and data manipulation capabilities.

---

## Optimising MariaDB’s Memory Utilization:

By optimizing the buffer pool size, join buffer size and large memory size settings in MariaDB, you can efficiently utilize memory resources and improve the performance of your database. Remember to restart the server after making these changes for them to take effect.

### Buffer Pool Size

- Open the `my.cnf` configuration file for your MariaDB installation using a text editor. Typically, this file is usually located in the `/etc/mysql` or `/etc/mysql/mariadb.conf.d` directory.
- Locate the innodb_buffer_pool_size parameter in the `my.cnf` file and adjust its value to the desired amount. For example:
  - `innodb_buffer_pool_size=4G`
- Save the changes to the `my.cnf` file and close the text editor.
- Restart the MariaDB server to apply the new configuration changes. On most Linux systems, you can accomplish this by running the following command:
  - `sudo systemctl restart mariadb`
- The `innodb_buffer_pool_size` is generally the single highest-impact parameter.
  - Setting it to 70 to 80 percent of available RAM on a dedicated database server significantly improves caching efficiency and reduces disk I/O.
  - The `innodb_buffer_pool_instances` setting divides the pool into separate regions, which reduces contention on multi-core systems. Use one instance per gigabyte, up to eight.
- To check your current buffer pool hit ratio, run this SQL command: `SHOW GLOBAL STATUS LIKE 'Innodb_buffer_pool%';`
  - Look at `Innodb_buffer_pool_reads` vs `Innodb_buffer_pool_read_requests`. If more than 1–2% of reads are going to disk, you have room to benefit from a larger pool.****
- Preserving cache between shutdown and startup
  - `innodb_buffer_pool_dump_at_shutdown = ON`
  - `innodb_buffer_pool_load_at_startup = ON`
  - This saves the buffer pool state on shutdown and restores it on startup, so you don’t spend the first hour after a restart with a cold cache.

### Join Buffer

join_buffer_size sets the per-connection memory MySQL can use for joins that cannot use an index. Raising it can help specific full-scan joins, but because it can be allocated per connection, an oversized value can create memory risk under concurrency.

- Open the my.cnf configuration file for your MariaDB installation using a text editor. This file is usually located in the /etc/mysql or /etc/mysql/mariadb.conf.d directory. 
- Find the join_buffer_size parameter in the my.cnf file and modify its value as needed. For example:
  - `join_buffer_size=2M`
- Save the changes to the my.cnf file and close the text editor.
- Restart the MariaDB server to apply the new configuration changes. On most Linux systems, you can do this by running the following command:
  - `sudo systemctl restart mariadb`

## Large Memory Size

- Open the my.cnf configuration file for your MariaDB installation using a text editor. This file is usually located in the /etc/mysql or /etc/mysql/mariadb.conf.d directory.
- Locate the innodb_log_file_size parameter in the my.cnf file and set it to the desired value. For instance:
  - `innodb_log_file_size=1G`
- Save the changes to the `my.cnf` file and close the text editor.
- Restart the MariaDB server to apply the new configuration changes. You can do this on most Linux systems using the following command:
  - `sudo systemctl restart mariadb`

---

## Optimising Data Storage

The innodb_file_per_table setting controls whether InnoDB stores each table's data and indexes in its own .ibd file or in the shared system tablespace.
File-per-table mode is the default since MySQL 5.6 and is recommended for most workloads.

When innodb_file_per_table=ON, each table gets a dedicated .ibd file in the schema directory:

```sql
-- Check current setting
SHOW VARIABLES LIKE 'innodb_file_per_table';

-- Enable file-per-table mode at runtime
SET GLOBAL innodb_file_per_table = ON;
    
-- Check the file path for a specific table
SELECT FILE_NAME, TABLESPACE_NAME, FILE_TYPE
FROM information_schema.FILES
WHERE FILE_NAME LIKE '%orders%';

-- Or check the tablespace for a table
SELECT NAME, SPACE_TYPE
FROM information_schema.INNODB_TABLES
WHERE NAME = 'mydb/orders';
```

The primary advantage is reclaiming disk space. When you DROP TABLE or TRUNCATE TABLE, MySQL removes the entire .ibd file,
immediately returning space to the OS. With the shared system tablespace, space is never returned to the OS even after large deletions.

Other benefits include easier individual table backups with tools like Xtrabackup, the ability to place specific tables on different storage devices, and per-table encryption.

**Possible drawbacks**

With many tables, each having its own file increases the OS file descriptor count. Very large numbers of tables (tens of thousands) can create file system overhead. In such cases, consider general tablespaces to group related tables.

---

## MariaDB Database Performance Metrics

Monitoring various performance metrics ensures your MariaDB database’s optimal performance. Here are some key metrics to consider:

- Query Throughput: This metric measures the number of queries the MariaDB server can handle within a given period. It is essential to ensure that the query throughput is sufficiently high to meet your application’s demands.
- Query Latency: Query latency refers to the time taken by a query to execute and return results. Lower query latency indicates faster query execution, which is desirable for efficient database performance.
- CPU Usage: CPU usage measures the processing power the MariaDB server utilizes. High CPU usage can indicate server stress and may suggest the need for additional resources or optimization to handle increased loads effectively.
- Memory Usage: Memory usage indicates the amount of memory utilized by the MariaDB server. High memory usage can indicate potential memory constraints and the need for additional resources to prevent performance degradation.
- Disk I/O: Disk I/O measures the data read from and written to the disk by the MariaDB server. High disk I/O can indicate stress on the server and may benefit from faster or more efficient storage solutions.
- Connection Count: Connection count measures the number of active database connections to the MariaDB server. High connection counts may indicate the need to scale up the server to handle additional connections efficiently.
- Lock Contention: Lock contention measures the time queries are blocked while waiting for database locks to be released. High lock contention suggests that the database schema or query design may need optimization to reduce contention and improve performance.

- By monitoring these performance metrics regularly, you can identify potential bottlenecks or areas for optimization in your MariaDB database and take proactive measures to enhance its performance and responsiveness.

### Monitoring and Benchmarking MariaDB Performance

There are several tools and techniques available to monitor and benchmark MariaDB’s performance. Some of the most commonly used methods are:

- System Monitoring: It is important to monitor the system-level resources such as CPU, memory, disk usage, and network activity to ensure that the database server has adequate resources to function optimally.
- Query Profiling: Query profiling involves analyzing the execution plan of queries to identify inefficiencies and bottlenecks in the database’s performance. This can be done using the MariaDB slow query log or other third-party query profiling tools.
- Index Optimization: Indexes play a crucial role in the performance of MariaDB databases. By ensuring that the appropriate indexes are in place, query execution times can be significantly reduced.
- Load Testing: Load testing involves simulating a heavy load on the database to identify performance issues and bottlenecks. This can be done using tools such as Apache JMeter or Sysbench.
- Replication Monitoring: MariaDB offers several replication methods for high availability and scalability. It’s important to monitor replication performance to ensure that data is being replicated correctly and efficiently.
- Benchmarking: Benchmarking involves running a set of standardized tests to compare the performance of different database configurations. This can be useful for identifying the best hardware and software configurations for optimal performance

**Tools**

- MariaDB Monitor
- Percona Toolkit
- Tuning Primer

**Identify slow queries**

- Enable the slow query log by setting `slow_query_log=1` and `long_query_time` to a threshold (such as 1 second) in your `my.cnf` file. MariaDB will log all queries that exceed that threshold, giving you a clear list of candidates for optimisation.

## Example Config: `/etc/mysql/my.cnf`


```
[mysqld]

innodb_buffer_pool_instances = 4      # Use 1 instance per 1GB of InnoDB pool size - max is 64
innodb_buffer_pool_size = 4GB           # 6GB RAM Available on dedicated DB server
innodb_file_per_table = 1          # Ensure each table gets its own file for better storage management

innodb_log_file_size=500MB

inndb_strict_mode=ON

slow_query_log = 1
slow_query_log_file = /var/lib/mysql/mysql_slow.log
long_query_time = 2

bind_address = 127.0.0.1 # Change to 0.0.0.0 to allow remote connections
```

---

## Resources

- [Preparing mysql or Mariadb for Production](https://severalnines.com/blog/preparing-mysql-or-mariadb-server-production-part-one/)
- [MariaDB Docker Setup](https://medium.com/@ngza5tqf/mariadb-docker-setup-running-mariadb-in-docker-containers-complete-guide-998d3d7b1547)
- [Docker Compose Ulimits](https://oneuptime.com/blog/post/2026-02-08-how-to-use-docker-compose-ulimits-configuration/view)
- [Cloudways - MariaDB Performance Tuning](https://www.cloudways.com/blog/mariadb-performance-tuning/)
- [Techniques for faster queries](https://medium.com/@x0goe/5-mariadb-performance-tuning-techniques-for-faster-queries-3f4e43df29b8)
- [How to Use InnoDB File-Per-Table Tablespaces in MySQL](https://oneuptime.com/blog/post/2026-03-31-mysql-innodb-file-per-table-tablespace/view)
- [My MariaDB Config - Example (Git)](https://gist.github.com/fevangelou/fb72f36bbe333e059b66)
- [MariaDB Performance Tuning Guide](https://releem.com/blog/mariadb-performance-tuning-guide)
