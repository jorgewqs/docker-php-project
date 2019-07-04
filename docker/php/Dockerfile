FROM php:7.3-fpm


##############################################
# Preparação
##############################################
RUN apt-get update;

# Muda o UID e o GID do usuario www-data para obter os privilégios do host
RUN usermod -u 999 www-data && groupmod -g 999 www-data;

##############################################
# Módulos do PHP
##############################################


# BCMATH
RUN docker-php-ext-install -j$(nproc) bcmath;


# GD
RUN apt-get install -y libfreetype6-dev;
RUN apt-get install -y libjpeg62-turbo-dev;
RUN apt-get install -y libpng-dev;
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/;
RUN docker-php-ext-install -j$(nproc) gd;


# PDO MYSQL
RUN docker-php-ext-install -j$(nproc) pdo_mysql;


##############################################
# Ferramentas adicionais
##############################################


# COMPOSER
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"


# MYSQL CLIENT
RUN apt-get install -y mysql-client;


# NODEJS
RUN apt-get install -y nodejs --no-install-recommends;


##############################################
# Limpeza final
##############################################

RUN rm -rf /var/lib/apt/lists/*;
RUN apt-get autoremove -y;
ENV LANG C.UTF-8
