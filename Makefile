current_dir := $(shell pwd)
# COLORS
RED  := $(shell tput -Txterm setaf 1)
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
BLUE   := $(shell tput -Txterm setaf 4)
EOL  := $(shell tput -Txterm sgr0)

build: down_all _build
_build:
	@echo "${GREEN}>>> building container${EOL}"
	cd docker/ && docker-compose build --no-cache

## Start docker
up: _start_docker
_start_docker:
	cd docker/ && docker-compose up -d

## Stop docker
stop: down_all
down_all:
	@docker stop $$(docker ps -a -q)

## Composer update
composer-update:
	@echo "${GREEN}>>> Composer update${EOL}"
	docker exec -it elasticsearch-app composer update

phpstan: _phpstan
_phpstan:
	@echo "${BLUE}>>> Run PHPstan${EOL}"
	@docker run -it --rm --network=host -v ${current_dir}:/var/www/Elasticsearch -w /var/www/Elasticsearch docker_elasticsearch-app /bin/bash -c "./vendor/bin/phpstan --configuration=phpstan.neon"

phpunit: _phpunit
_phpunit:
	@echo "${BLUE}>>> Run PHPunit${EOL}"
	@docker run -it --rm --network=host -v ${current_dir}:/var/www/Elasticsearch -w /var/www/Elasticsearch docker_elasticsearch-app /bin/bash -c "./vendor/bin/phpunit src/Elasticsearch"

bash:
	@docker exec -it elasticsearch-app /bin/bash