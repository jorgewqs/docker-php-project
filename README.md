# Docker PHP Project

Um template para criação de projetos PHP com Docker


# Vários projetos paralelos

Caso seja a intenção usar vários containers Docker paralelamente, 
será necessário fazer algumas configurações adicionais no arquivo docker-compose.yml:

## Nomes dos containers

Personalize os nomes dos containers para definirem seu projeto. Por exemplo:

```
    container_name: database

    para 

    container_name: database_webflix
```

## Portas

Configure portas diferentes para cada projeto. Por exemplo:

```
    - "3306:3306"

    para 

    - "4001:3306"
```
