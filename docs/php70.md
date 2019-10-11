# Docker PHP Project - PHP 7.0

## 1. Extensões do PHP 7.0

De forma muito fácil, é possível instalar/desinstalar extensões do PHP através das 
configurações no arquivo docker.php.

Por exemplo:

```php
php('7.0')
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
* geoip
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
* tidy
* xdebug
* xmlrpc
* xsl
* zip