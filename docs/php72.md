# Docker PHP Project - PHP 7.2

## 1. Extensões do PHP 7.2

De forma muito fácil, é possível instalar/desinstalar extensões do PHP através das 
configurações no arquivo docker.php.

Por exemplo:

```php
php('7.2')
    ->param('name', 'app')
    ->extension('mysql')
    ->extension('gd')
```

As seguintes extensões estão disponíveis para instalação:

* bcmath
* bz2
* calendar
* curl
* dba
* enchant
* exif
* gd
* gearman
* gmp
* igbinary
* imagick
* imap
* interbase
* intl
* ldap
* memcached
* mysql
* mongodb
* msgpack
* odbc
* pgsql
* phpdbg
* pspell
* raphf
* redis
* recode
* soap
* sqlite3
* ssh2
* sybase
* tideways
* tidy
* xdebug
* xmlrpc
* xsl
* yaml
* zmq
* zip