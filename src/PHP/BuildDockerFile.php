<?php
namespace Dpp\PHP;

class BuildDockerFile extends \Dpp\BuildDockerFile
{
    protected function handle()
    {
        $version = $this->getVersion();
        
        if ( (int) $version == 70) {
            $version = 7;
        }

        // !!! A partir do phpdockerio !!!
        $this->add("FROM phpdockerio/php{$version}-fpm:latest");
        
        /*
        // !!! A partir do zero !!!
        $this->add('FROM ubuntu:bionic');    
        $this->add('ENV TERM=linux'); // Corrige alguns problemas estranhos do terminal, como falhas desobstruídas / CTRL+L 
        $this->add('ENV DEBIAN_FRONTEND=noninteractive'); // Certifique-se de que o apt não faça perguntas ao instalar coisas 
        $this->addSeparator('Repositórios');
        $this->add('RUN apt-get update;');  
        $this->add('RUN apt-get install -y --no-install-recommends gnupg;'); 
        $this->add('RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu bionic main" > /etc/apt/sources.list.d/ondrej-php.list;');  
        $this->add('RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C;');  
        */

        $this->add('RUN apt-get update;');  
        $this->add('RUN apt-get -y --no-install-recommends install ca-certificates curl unzip;');  

        $this->addSeparator('PHP');
        $packages = implode(' ', $this->getPackages());
        $this->add("RUN apt-get -y --no-install-recommends install $packages;");  

        $this->addSeparator('Ferramentas adicionais');
        $this->insertTools();

        $this->add('RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*;');

        $this->addSeparator('Configurações finais');
        $this->copyFiles($this->getProjectDir());

        /*
        // !!! A partir do zero !!!
        // Os pacotes PHP-FPM precisam de um empurrão para torná-los compatíveis com o docker    
        $versionString = $this->getVersionString();
        $this->add("COPY php-overrides.conf /etc/php/{$versionString}/fpm/pool.d/z-overrides.conf");
        $this->add('STOPSIGNAL SIGQUIT');
        $this->add("CMD [\"/usr/sbin/php-fpm{$versionString}\", \"-O\" ]"); 
        $this->add('EXPOSE 9000'); 
        */
    }

    protected function copyFiles($destiny)
    {
        // !!! A partir do zero !!!
        // $overrides = __DIR__ . DIRECTORY_SEPARATOR . 'php-overrides.conf';
        // copy($overrides, $destiny . DIRECTORY_SEPARATOR . 'php-overrides.conf');

        $ini = __DIR__ . DIRECTORY_SEPARATOR . 'php.ini';
        copy($ini, $destiny . DIRECTORY_SEPARATOR . 'php.ini');
    }

    public function getVersionString()
    {
        $version = $this->getVersion();

        switch($version){
            case 56: 
                $prefix = '5.6'; 
                break;
            case 70: 
                $prefix = '7.0'; 
                break;
            case 71: 
                $prefix = '7.1'; 
                break;
            case 72: 
                $prefix = '7.2'; 
                break;
            case 73: 
                $prefix = '7.3'; 
                break;
        }

        return $prefix;
    }

    public function getPackages()
    {
        $packages         = [];
        $version          = $this->getVersion();
        $configExtensions = parent::getExtensions();

        switch($version){
            case 56: 
                // $prefix = 'php5'; 
                $extensions = $this->getPhp56Extensions();
                break;
            case 70: 
                // $prefix = 'php7.0'; 
                $extensions = $this->getPhp70Extensions();
                break;
            case 71: 
                // $prefix = 'php7.1'; 
                $extensions = $this->getPhp71Extensions();
                break;
            case 72: 
                // $prefix = 'php7.2'; 
                $extensions = $this->getPhp72Extensions();
                break;
            case 73: 
                // $prefix = 'php7.3'; 
                $extensions = $this->getPhp73Extensions();
                break;
        }

        // !!! A partir do zero !!!
        // $packages[] = "${prefix}-cli";
        // $packages[] = "${prefix}-fpm";

        $all = false;
        foreach($configExtensions as $index => $name) {
            if ($name == 'all') {
                $all = true;
                unset($configExtensions[$index]);
                break;
            }
        }

        if ($all === true) {
            foreach($extensions as $index => $name) {
                $packages[] = $name;
            }
            return $packages;
        } 

        foreach($configExtensions as $index => $name) {
            if (! isset($extensions[$name])) {
                $this->warn("A extensão {$name} não está disponível para esta versão do PHP!");
            }
            $packages[] = $extensions[$name];
        }

        return $packages;
    }

    protected function getPhp56Extensions()
    {
        return [
            'adodb'      => 'php5-adodb',
            'curl'       => 'php5-curl',
            'enchant'    => 'php5-enchant',
            'exactimage' => 'php5-exactimage',
            'gd'         => 'php5-gd',
            'gearman'    => 'php5-gearman',
            'geos'       => 'php5-geos',
            'gmp'        => 'php5-gmp',
            'geoip'      => 'php5-geoip',
            'gnupg'      => 'php5-gnupg',
            'gdcm'       => 'php5-gdcm',
            'igbinary'   => 'php5-igbinary',
            'imap'       => 'php5-imap',
            'imagick'    => 'php5-imagick',
            'interbase'  => 'php5-interbase',
            'intl'       => 'php5-intl',
            'lasso'      => 'php5-lasso',
            'ldap'       => 'php5-ldap',
            'librdf'     => 'php5-librdf',
            'memcache'   => 'php5-memcache',
            'memcached'  => 'php5-memcached',
            'mongodb'    => 'php5-mongo',
            'mysql'      => 'php5-mysql',
            'msgpack'    => 'php5-msgpack',
            'oauth'      => 'php5-oauth',
            'odbc'       => 'php5-odbc',
            'pecl-http'  => 'php5-pecl-http',
            'pgsql'      => 'php5-pgsql',
            'phpdbg'     => 'php5-phpdbg',
            'pinba'      => 'php5-pinba',
            'pspell'     => 'php5-pspell',
            'raphf'      => 'php5-raphf',
            'radius'     => 'php5-radius',
            'redis'      => 'php5-redis',
            'rrd'        => 'php5-rrd',
            'recode'     => 'php5-recode',
            'remctl'     => 'php5-remctl',
            'sasl'       => 'php5-sasl',
            // 'snmp'       => 'php5-snmp',
            'solr'       => 'php5-solr',
            'stomp'      => 'php5-stomp',
            'svn'        => 'php5-svn',
            'sqlite3'    => 'php5-sqlite',
            'ssh2'       => 'php5-ssh2',
            'sybase'     => 'php5-sybase',
            'tidy'       => 'php5-tidy',
            'twig'       => 'php5-twig',
            'vtkgdcm'    => 'php5-vtkgdcm',
            'xdebug'     => 'php5-xdebug',
            'xhprof'     => 'php5-xhprof',
            'xmlrpc'     => 'php5-xmlrpc',
            'xsl'        => 'php5-xsl',
            'zmq'        => 'php5-zmq',
        ];
    }

    protected function getPhp70Extensions()
    {
        return [
            'bcmath'    => 'php-bcmath',
            'bz2'       => 'php-bz2',
            'calendar'  => 'php-calendar',
            'curl'      => 'php-curl',
            'dba'       => 'php-dba',
            'enchant'   => 'php-enchant',
            'exif'      => 'php-exif',
            'gd'        => 'php-gd',
            'geoip'     => 'php-geoip',
            'gmp'       => 'php-gmp',
            'igbinary'  => 'php-igbinary',
            'imagick'   => 'php-imagick',
            'imap'      => 'php-imap',
            'interbase' => 'php-interbase',
            'intl'      => 'php-intl',
            'ldap'      => 'php-ldap',
            'memcached' => 'php-memcached',
            'mysql'     => 'php-mysql',
            'mongodb'   => 'php-mongodb',
            'msgpack'   => 'php-msgpack',
            'odbc'      => 'php-odbc',
            'pgsql'     => 'php-pgsql',
            'phpdbg'    => 'php-phpdbg',
            'pspell'    => 'php-pspell',
            'raphf'     => 'php-raphf',
            'redis'     => 'php-redis',
            'recode'    => 'php-recode',
            // 'snmp'      => 'php-snmp',
            'soap'      => 'php-soap',
            'sqlite3'   => 'php-sqlite3',
            'ssh2'      => 'php-ssh2',
            'sybase'    => 'php-sybase',
            'tidy'      => 'php-tidy',
            'xdebug'    => 'php-xdebug',
            'xmlrpc'    => 'php-xmlrpc',
            'xsl'       => 'php-xsl',
            'zip'       => 'php-zip'
        ];
    }

    protected function getPhp71Extensions()
    {
        return [
            'bcmath'    => 'php7.1-bcmath',
            'bz2'       => 'php7.1-bz2',
            'calendar'  => 'php7.1-calendar',
            'curl'      => 'php7.1-curl',
            'dba'       => 'php7.1-dba',
            'enchant'   => 'php7.1-enchant',
            'exif'      => 'php7.1-exif',
            'gd'        => 'php7.1-gd',
            'gmp'       => 'php7.1-gmp',
            'igbinary'  => 'php-igbinary',
            'imagick'   => 'php-imagick',
            'imap'      => 'php7.1-imap',
            'interbase' => 'php7.1-interbase',
            'intl'      => 'php7.1-intl',
            'ldap'      => 'php7.1-ldap',
            'memcached' => 'php-memcached',
            'mysql'     => 'php7.1-mysql',
            'mongodb'   => 'php-mongodb',
            'msgpack'   => 'php-msgpack',
            'odbc'      => 'php7.1-odbc',
            'pgsql'     => 'php7.1-pgsql',
            'phpdbg'    => 'php7.1-phpdbg',
            'pspell'    => 'php7.1-pspell',
            'raphf'     => 'php-raphf',
            'redis'     => 'php-redis',
            'recode'    => 'php7.1-recode',
            // 'snmp'      => 'php7.1-snmp',
            'soap'      => 'php7.1-soap',
            'sqlite3'   => 'php7.1-sqlite3',
            'ssh2'      => 'php-ssh2',
            'sybase'    => 'php7.1-sybase',
            'tideways'  => 'php-tideways',
            'tidy'      => 'php7.1-tidy',
            'xdebug'    => 'php-xdebug',
            'xmlrpc'    => 'php7.1-xmlrpc',
            'xsl'       => 'php7.1-xsl',
            'yaml'      => 'php-yaml',
            'zmq'       => 'php-zmq',
            'zip'       => 'php7.1-zip'
        ];
    }

    protected function getPhp72Extensions()
    {
        return [
            'bcmath'    => 'php7.2-bcmath',
            'bz2'       => 'php7.2-bz2',
            'calendar'  => 'php7.2-calendar',
            'curl'      => 'php7.2-curl',
            'dba'       => 'php7.2-dba',
            'enchant'   => 'php7.2-enchant',
            'exif'      => 'php7.2-exif',
            'gd'        => 'php7.2-gd',
            'gearman'   => 'php-gearman',
            'gmp'       => 'php7.2-gmp',
            'igbinary'  => 'php-igbinary',
            'imagick'   => 'php-imagick',
            'imap'      => 'php7.2-imap',
            'interbase' => 'php7.2-interbase',
            'intl'      => 'php7.2-intl',
            'ldap'      => 'php7.2-ldap',
            'memcached' => 'php-memcached',
            'mysql'     => 'php7.2-mysql',
            'mongodb'   => 'php-mongodb',
            'msgpack'   => 'php-msgpack',
            'odbc'      => 'php7.2-odbc',
            'pgsql'     => 'php7.2-pgsql',
            'phpdbg'    => 'php7.2-phpdbg',
            'pspell'    => 'php7.2-pspell',
            'raphf'     => 'php-raphf',
            'redis'     => 'php-redis',
            'recode'    => 'php7.2-recode',
            //'snmp'      => 'php7.2-snmp',
            'soap'      => 'php7.2-soap',
            'sqlite3'   => 'php7.2-sqlite3',
            'ssh2'      => 'php-ssh2',
            'sybase'    => 'php7.2-sybase',
            'tideways'  => 'php-tideways',
            'tidy'      => 'php7.2-tidy',
            'xdebug'    => 'php-xdebug',
            'xmlrpc'    => 'php7.2-xmlrpc',
            'xsl'       => 'php7.2-xsl',
            'yaml'      => 'php-yaml',
            'zmq'       => 'php-zmq',
            'zip'       => 'php7.2-zip'
        ];
    }

    protected function getPhp73Extensions()
    {
        return [
            'bcmath'    => 'php7.3-bcmath',
            'bz2'       => 'php7.3-bz2',
            'calendar'  => 'php7.3-calendar',
            'curl'      => 'php7.3-curl',
            'dba'       => 'php7.3-dba',
            'enchant'   => 'php7.3-enchant',
            'exif'      => 'php7.3-exif',
            'gd'        => 'php7.3-gd',
            'gearman'   => 'php-gearman',
            'gmp'       => 'php7.3-gmp',
            'igbinary'  => 'php-igbinary',
            'imagick'   => 'php-imagick',
            'imap'      => 'php7.3-imap',
            'interbase' => 'php7.3-interbase',
            'intl'      => 'php7.3-intl',
            'ldap'      => 'php7.3-ldap',
            'memcached' => 'php-memcached',
            'mysql'     => 'php7.3-mysql',
            'mongodb'   => 'php-mongodb',
            'msgpack'   => 'php-msgpack',
            'odbc'      => 'php7.3-odbc',
            'pgsql'     => 'php7.3-pgsql',
            'phpdbg'    => 'php7.3-phpdbg',
            'pspell'    => 'php7.3-pspell',
            'raphf'     => 'php-raphf',
            'redis'     => 'php-redis',
            'recode'    => 'php7.3-recode',
            // 'snmp'      => 'php7.3-snmp',
            'soap'      => 'php7.3-soap',
            'sqlite3'   => 'php7.3-sqlite3',
            'ssh2'      => 'php-ssh2',
            'sybase'    => 'php7.3-sybase',
            'tideways'  => 'php-tideways',
            'tidy'      => 'php7.3-tidy',
            'xdebug'    => 'php-xdebug',
            'xmlrpc'    => 'php7.3-xmlrpc',
            'xsl'       => 'php7.3-xsl',
            'yaml'      => 'php-yaml',
            'zmq'       => 'php-zmq',
            'zip'       => 'php7.3-zip'
        ];
    }
}