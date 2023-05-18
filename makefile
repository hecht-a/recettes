.SILENT:

# Executables (local)
DOCKER_COMP = docker compose

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs

## ———————————————————————— 🎵 🐳 Makefile 🐳 🎵 ————————————————————————
help: ## Outputs this help screen
	grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	$(DOCKER_COMP) logs --tail=0 --follow

## —— Style 🐳 ————————————————————————————————————————————————————————————————
format: ## Format the code
	vendor/bin/php-cs-fixer --config=.php-cs-fixer.dist.php fix

lint: ## Analyse the code
	vendor/bin/phpstan analyse -c phpstan.neon

grum: ## Execute grumphp
	vendor/bin/grumphp run --config=grumphp.yml