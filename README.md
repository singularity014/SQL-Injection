
@Prafull Sharma(IBM Research), 2016
# Introduction
SQL Injection is a technique which uses bad programming practices in a program to
execute arbitrary SQL queries. SQL injection prevention techniques aim to eliminate or reduce
the probability of using an SQL injection. Our solution is designed such that a developer may
reduce the probability of an SQL injection with minimal amount of changes to the solution.

## Approach
SQL Injection is possible because of the user inputs. Our solution analyzes the input
and checks it for any malicious strings. It modifies these strings from the SQL query.
The user input can come in many forms:
* POST and GET parameters
* HTTP headers
* Request Payload
* File Uploads

Our solution uses the sql escape string function on each of these. If the escaped string is
different from the real string, it might be an SQL injection string. To check this is true, the
security layer looks for the request data as substrings of the SQL query. If there’s a match, the
security layer escapes the input where it is used in the query.
Another issue is second order SQL injection prevention. Second order SQL injection
prevention uses an SQL injection string already stored inside the database. Since all SQL
queries have to pass through our security layer, it maintains a list of all the fields that are read
from the database. The strategy used of request inputs can be reused for these field. This
means that all sql fields are matched for being used as substrings in a subsequent query. If
that’s true, the field is escaped using mysqli’s built in sql escape string function.

## How to use the security layer
The security layer consists of database configuration variables and a
function(secure_query). The following variables should be populated to their correct values in
sql_secure.php :
```
$sql_server_address
$sql_port
$sql_username
$sql_password
$sql_database
```

The php file making the query should have access to the secure_query function:
`<?php include_once("sql_secure.php"); ?>`
Finally, the query can simply be executed using:
`secure_query($query)`
Where $query is the final query string.
My solution is tested to be working with PHP 7. The test file and security layer are attached in
my submission.

## References
This project was written during our participation in HackerEarth, Bangalore competition (2016).
Result - We scored 89% favourable views, and came 12th/16000 approx submissions.

