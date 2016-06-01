# zabbix-maintenance-php

PHP script to add/remove Maintenance mode in Zabbix (using a maintenance group).


## Requirements

* PHP 5.3.9 and up.

That's all!

## Installation

To install zabbix-maintenance-php download [zabbixmaintenance.phar](https://github.com/artberri/zabbix-maintenance-php/releases/download/v0.0.2/zabbixmaintenance.phar) archive and move zabbixmaintenance.phar to your bin directory and make it executable.

``` sh
curl -LO https://github.com/artberri/zabbix-maintenance-php/releases/download/v0.0.2/zabbixmaintenance.phar
sudo mv zabbixmaintenance.phar /usr/local/bin/zabbixmaintenance
sudo chmod +x /usr/local/bin/zabbixmaintenance
```

Or via composer:

``` sh
composer require berriart/zabbix-maintenance
```


## Documentation

See the command options running:

``` sh
zabbixmaintenance -h
```

Or if you installed it via composer:

``` sh
./vendor/bin/zabbixmaintenance -h
```

## License

Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

