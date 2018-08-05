# mediawiki-mikrotik-view

Mediawiki Tag Extension for import NAT rules from Mikrotik.
![alt_text](https://github.com/GOID1989/mediawiki-mikrotik-view/blob/master/example.PNG)

## Prerequisites
 - Enable Mikrotik API Services (IP->Services) and allow access to ports 8728-8729
 - Put extension php-file and [PHP API](https://github.com/BenMenking/routeros-api) to "extensions\mikrotikView" folder of Mediawiki
 - Add line `require_once( "$IP/extensions/mikrotikView/mikrotikView.php" );` into MW's LocalSettings.php
 
## How to use
On Wiki article page in edit mode add `<mikrotik ip="192.168.88.1" login="admin" password="tooooooo_long_pwd"/>` (three attributes REQUIRED!). Save changes.

## ToDo
**Features planned:**
 - [ ] FILTER table support
 - [ ] Support fields\columns selection (default\*(all used in rule)\specified)
 - [ ] Russian symbols compatibility
 