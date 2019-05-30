#!/bin/bash

sudo docker-compose down
# sudo docker system prune -a --volumes
# sudo docker image rm aiguilles-dealers_boot
# sudo docker container prune
sudo docker image rm php
sudo docker image rm docker-php-project_php 
sudo docker-compose up
