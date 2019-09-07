# Docker PHP Project

By [Ricardo Pereira Dias](http://www.ricardopdias.com.br) ©

Este é um pacote de software para distribuições Linux baseadas em Debian que permite a criação de projetos PHP com Docker de forma flexível e rápida.

Para usar:

1. Faça o download e instale a última versão do pacote [docker-php-project_2.1.0_all.deb](https://github.com/ricardopedias/docker-php-project/raw/master/dist/docker-php-project_2.1.0_all.deb);
2. Abra o terminal e execute o comando "docker-php-project" em qualquer lugar para gerar projetos do Docker :)

# 1. Objetivo

O objetivo inicial desta ferramenta é possibilitar a execução de qualquer projeto PHP sem a necessidade de instalar a infraestrutura (web server, linguagem e banco de dados) no computador do desenvolvedor.

Até o presente momento, este pacote não provê quaisquer abordagens de segurança para que possa ser executado em produção. 
Isso não significa que não possa ser usado como ponto de partida para uma configuração mais minuciosa de forma a adequar o projeto 
para as respectivas necessidades de produção.

Como esta ferramenta está em evolução, usada para fins reais de trabalho, novidades poderão surgir e novas funcionalidades poderão 
ser adicionadas para facilitar ainda mais o processo de configuração.

# 2. Funcionamento

## Comandos básicos

Para executar a infraestrutura do projeto, é necessário existir um arquivo chamado "docker.php" no diretório atual.
Para criá-lo basta executar:

```
$ php-project init
```

Para gerar os arquivos do Docker (docker-compose.yml, .docker-project/*) e subir so containers do projeto:

```
$ php-project up
```


