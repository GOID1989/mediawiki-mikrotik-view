# mediawiki-mikrotik-view

Mediawiki Tag Extension for import NAT rules from Mikrotik.

## Prerequisites
 - PHP API for Mikrotik API https://github.com/BenMenking/routeros-api
 - Enable Mikrotik API Services (IP->Services) and allow access to ports 8728-8729
 - Put extension php-file to "extensions\mikrotikView" folder of Mediawiki
 - Add line `require_once( "$IP/extensions/mikrotikView/mikrotikView.php" );` into MW's LocalSettings.php
 
## How to use
On Wiki article page in edit mode add `<mikrotik ip="192.168.88.1" login="admin" password="tooooooo_long_pwd"/>` (three attributes REQUIRED!). Save changes.