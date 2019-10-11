# Docker PHP Project - Parâmetros Globais

## 1. Usuário do GIT

Para configurar o usuário responsável pelos commits no projeto, basta setar os parâmetros abaixo:

```php
set('git-commiter-name', 'Fulano de Tal');
set('git-commiter-email', 'fulano@email.com.br');
```

Todas as vezes que o projeto for executado com **php-project up**, essas informações serão verificadas.

## 2. Tarefas

Além de configurar e executar automaticamente os containers, é possível 
atribuir tarefas para o projeto. Todas as vezes que o projeto for executado, essas tarefas serão executadas:

```php
task('permissoes')
    ->run('chmod -Rf 755 /bootstrap/cache');
    ->run('chmod -Rf 755 /storage')
    ->run('php artisan migrate');
```

* [Parâmetros Globais](docs/globais.md)
* [PHP 5.6](docs/php56.md)
* [PHP 7.0](docs/php70.md)
* [PHP 7.1](docs/php71.md)
* [PHP 7.2](docs/php72.md)
* [PHP 7.3](docs/php73.md)
* [MySQL](docs/mysql.md)
* [Nginx](docs/nginx.md)
