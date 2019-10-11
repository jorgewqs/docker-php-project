# Docker PHP Project - PHP 5.6

## 1. Extensões do PHP 5.6

De forma muito fácil, é possível instalar/desinstalar extensões do PHP através das 
configurações no arquivo docker.php.

Por exemplo:

```php
php('5.6')
    ->param('name', 'app')
    ->extension('mysql')
    ->extension('gd')
```

As seguintes extensões estão disponíveis para instalação:

* adodb
* curl       
* enchant    
* exactimage 
* gd         
* gearman    
* geos       
* gmp        
* geoip      
* gnupg      
* gdcm       
* igbinary   
* imap       
* imagick    
* interbase  
* intl       
* lasso      
* ldap       
* librdf     
* memcache   
* memcached  
* mongodb    
* mysql      
* msgpack    
* oauth      
* odbc       
* peclhttp'  
* pgsql      
* phpdbg     
* pinba      
* pspell     
* raphf      
* radius     
* redis      
* rrd        
* recode     
* remctl     
* sasl       
* solr       
* stomp      
* svn        
* sqlite3    
* ssh2       
* sybase     
* tidy       
* twig       
* vtkgdcm    
* xdebug     
* xhprof     
* xmlrpc     
* xsl        
* zmq        

* [Parâmetros Globais](globais.md)
* [PHP 5.6](php56.md)
* [PHP 7.0](php70.md)
* [PHP 7.1](php71.md)
* [PHP 7.2](php72.md)
* [PHP 7.3](php73.md)
* [MySQL](mysql.md)
* [Nginx](nginx.md)

