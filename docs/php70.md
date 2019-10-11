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

* [Parâmetros Globais](globais.md)
* [PHP 5.6](php56.md)
* [PHP 7.0](php70.md)
* [PHP 7.1](php71.md)
* [PHP 7.2](php72.md)
* [PHP 7.3](php73.md)
* [MySQL](mysql.md)
* [Nginx](nginx.md)