[ClamAV](http://www.clamav.net/) REST proxy.

# What is it?

## The technical details

This is a REST proxy server with support for basic INSTREAM scanning and PING command. 

Clamd protocol is explained here:
http://linux.die.net/man/8/clamd

Clamd protocol contains command such as shutdown so exposing clamd directly to external services is not a feasible option. Accessing clamd directly is fine if you are running single application and it's on the localhost. 

# Usage

```
  mvn package

  java -jar target/clamav-rest-1.0.2.jar
```

# Testing the REST service

You can use [curl](http://curl.haxx.se/) as it's REST. Here's an example test session:

```
curl localhost:8080

curl -F "name=blabla" -F "file=@./eicar.txt" localhost:8080/scan
```

EICAR is a test file which is recognized as a virus by scanners even though it's not really a virus. Read more [EICAR information here](http://www.eicar.org/86-0-Intended-use.html).

# License

Copyright © 2014 [Solita](http://www.solita.fi)

Distributed under the GNU Lesser General Public License, either version 2.1 of the License, or 
(at your option) any later version.

