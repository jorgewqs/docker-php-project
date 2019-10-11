# Docker PHP Project - Parâmetros Globais

## 1. Usuário do GIT

Para configurar o usuário responsável pelos commits no projeto, basta setar os parâmetros abaixo:

```php
set('git-commiter-name', 'Fulano de Tal');
set('git-commiter-email', 'fulano@email.com.br');
```

Todas as vezes que o projeto for executado com **php-project up**, essas informações serão verificadas.

## 2. Tarefas

Além de configurar e executar automaticamente os conteiners, é possível 
atribuir tarefas para o projeto. Todas as vezes que o projeto for executado, essas tarefas serão executadas:

```php
task('permissoes')
    ->run('chmod -Rf 755 /bootstrap/cache');
    ->run('chmod -Rf 755 /storage')
    ->run('php artisan migrate');
```

* [Parâmetros Globais](globais.md)
* [PHP 5.6](php56.md)
* [PHP 7.0](php70.md)
* [PHP 7.1](php71.md)
* [PHP 7.2](php72.md)
* [PHP 7.3](php73.md)
* [MySQL](mysql.md)
* [Nginx](nginx.md)
