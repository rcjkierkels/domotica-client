[![HitCount](http://hits.dwyl.io/rcjkierkels/domotica-client.svg)](http://hits.dwyl.io/rcjkierkels/domotica-client)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/dwyl/esta/issues)
[![Known Vulnerabilities](https://snyk.io/test/github/rcjkierkels/domotica-client/badge.svg?targetFile=package.json)](https://snyk.io/test/github/rcjkierkels/domotica-client?targetFile=package.json)

# About Domotica Client
The domotica client is designed to run on a Raspberry Pi, Arduino or any other lightweight micro computer configured with an Apache webserver. The domotica client works together with the [domotica server](https://github.com/rcjkierkels/domotica-server) and the [domotica app](https://github.com/rcjkierkels/domotica-app). If configured correctly the domotica client will report itself to the server directly after is goes only. It also updates itself whenever new releases are pushed to the master branch. The client is designed in such way that it restarts itself when it hangs or has stopped working.

An overview of how the client communicates with the server is shown in the diagram below. For now the client communicates with the server through a mysql database. In the future this will be an API.

![Schematic](https://roland.kierkels.net/wp-content/uploads/2019/02/domotica-diagram-1.png)

More info: https://roland.kierkels.net/2019/02/selfmade-domotica-system/

# Requirements
* PHP >= 7.2.0
* Apache2 or higher
* Crontab
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* JSON PHP Extension
* PCNTL extension for Async operations
* Git for auto-updating
* Composer for auto-updating

# Installation
```
crontab -e
# Add the following line to your crontab
* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1
*/5 * * * * php /path/to/project/artisan client:update >> /dev/null 2>&1
*/5 * * * * php /path/to/project/artisan client:run >> /dev/null 2>&1

cp .env-example .env
# Add the correct information to your .env file
```

# Security
If you discover any security related issues, please email roland.kierkels@noveesoft.com instead of using the issue tracker.

# License
This application is open-source software licensed under the MIT license.