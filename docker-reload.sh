#!/bin/bash

sudo docker-compose down
sudo docker system prune -a --volumes
# sudo docker image rm aiguilles-dealers_boot
# sudo docker image rm aiguilles-dealers_php
sudo docker-compose up
