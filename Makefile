.PHONY: help
.DEFAULT_GOAL := help

help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

init: down build up install-backend ## Initialize environment

build: ## Build services.
	docker-compose -f docker-compose.yml build $(c)

up: ## Create and start services.
	docker-compose -f docker-compose.yml up -d $(c)

stop: ## Stop services.
	docker-compose -f docker-compose.yml stop $(c)

down: ## Stop and remove containers and volumes.
	docker-compose -f docker-compose.yml down -v $(c)

ps: ## Show started services.
	docker-compose ps

logs: ## Display logs.
	docker-compose -f docker-compose.yml logs --tail=100 -f $(c)

console-web: ## Login in webserver console.
	docker-compose -f docker-compose.yml exec web /bin/sh

console-backend: ## Login in backend console.
	docker-compose -f docker-compose.yml exec backend /bin/bash

install-backend: ## Install dependencies.
	docker-compose -f docker-compose.yml exec backend cp -n ./.env.example ./.env
	docker-compose -f docker-compose.yml exec backend composer install
	docker-compose -f docker-compose.yml exec backend yarn install
