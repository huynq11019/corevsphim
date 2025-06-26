# Makefile for CoreVsPhim Docker Management

.PHONY: help build up down restart logs shell setup clean health

# Docker compose command with proper path
DOCKER_COMPOSE = cd deploy && docker-compose

# Colors for help text
BLUE=\033[0;34m
GREEN=\033[0;32m
YELLOW=\033[1;33m
RED=\033[0;31m
NC=\033[0m # No Color

help: ## Show this help message
	@echo "$(BLUE)CoreVsPhim Docker Management$(NC)"
	@echo "$(YELLOW)Available commands:$(NC)"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}' $(MAKEFILE_LIST)

setup: ## Initial setup with Docker
	@echo "$(BLUE)Setting up CoreVsPhim with Docker...$(NC)"
	./docker-setup.sh

build: ## Build Docker containers
	@echo "$(BLUE)Building containers...$(NC)"
	$(DOCKER_COMPOSE) build --no-cache

up: ## Start all services
	@echo "$(BLUE)Starting services...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)Services started!$(NC)"
	@echo "Website: http://localhost:$$(cd deploy && grep NGINX_PORT .env.docker | cut -d'=' -f2 | tr -d '"' || echo '8080')"
	@echo "phpMyAdmin: http://localhost:$$(cd deploy && grep PHPMYADMIN_PORT .env.docker | cut -d'=' -f2 | tr -d '"' || echo '8081')"
	@echo "Redis: localhost:$$(cd deploy && grep REDIS_PORT .env.docker | cut -d'=' -f2 | tr -d '"' || echo '6380')"

down: ## Stop all services
	@echo "$(YELLOW)Stopping services...$(NC)"
	$(DOCKER_COMPOSE) down

restart: ## Restart all services
	@echo "$(BLUE)Restarting services...$(NC)"
	$(DOCKER_COMPOSE) restart

logs: ## View application logs
	$(DOCKER_COMPOSE) logs -f app

logs-all: ## View all services logs
	$(DOCKER_COMPOSE) logs -f

logs-nginx: ## View nginx logs
	$(DOCKER_COMPOSE) logs -f webserver

logs-redis: ## View redis logs
	$(DOCKER_COMPOSE) logs -f redis

shell: ## Access application container shell
	$(DOCKER_COMPOSE) exec app bash

nginx-shell: ## Access nginx container shell
	$(DOCKER_COMPOSE) exec webserver sh

redis-cli: ## Access Redis CLI
	$(DOCKER_COMPOSE) exec redis redis-cli -a $$(cd deploy && grep REDIS_PASSWORD .env.docker | cut -d'=' -f2 | tr -d '"')

status: ## Show containers status
	$(DOCKER_COMPOSE) ps

health: ## Check services health
	@echo "$(BLUE)Checking services health...$(NC)"
	@echo "App container:"
	@$(DOCKER_COMPOSE) exec app curl -f http://localhost/health || echo "$(RED)App health check failed$(NC)"
	@echo "Redis:"
	@$(DOCKER_COMPOSE) exec redis redis-cli ping || echo "$(RED)Redis health check failed$(NC)"
	@echo "Nginx:"
	@$(DOCKER_COMPOSE) exec webserver nginx -t || echo "$(RED)Nginx config test failed$(NC)"

install: ## Install PHP dependencies
	$(DOCKER_COMPOSE) exec app composer install --no-dev --optimize-autoloader

install-dev: ## Install PHP dependencies (with dev)
	$(DOCKER_COMPOSE) exec app composer install

npm-install: ## Install Node.js dependencies
	$(DOCKER_COMPOSE) exec app npm install

npm-dev: ## Build assets for development
	$(DOCKER_COMPOSE) exec app npm run dev

npm-prod: ## Build assets for production
	$(DOCKER_COMPOSE) exec app npm run production

npm-watch: ## Watch assets for changes
	$(DOCKER_COMPOSE) exec app npm run watch

artisan: ## Run artisan command (usage: make artisan cmd="migrate")
	$(DOCKER_COMPOSE) exec app php artisan $(cmd)

create-user: ## Create admin user
	$(DOCKER_COMPOSE) exec app php artisan ophim:user

migrate: ## Run database migrations
	@echo "$(BLUE)Running migrations on external database...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan migrate

migrate-fresh: ## Fresh migration with seed
	@echo "$(YELLOW)Warning: This will drop all tables on external database!$(NC)"
	@read -p "Are you sure? (y/N): " confirm && [ "$$confirm" = "y" ] && \
	$(DOCKER_COMPOSE) exec app php artisan migrate:fresh --seed

optimize: ## Optimize Laravel application
	$(DOCKER_COMPOSE) exec app php artisan optimize:clear
	$(DOCKER_COMPOSE) exec app php artisan config:cache
	$(DOCKER_COMPOSE) exec app php artisan route:cache
	$(DOCKER_COMPOSE) exec app php artisan view:cache

cache-clear: ## Clear all caches
	$(DOCKER_COMPOSE) exec app php artisan cache:clear
	$(DOCKER_COMPOSE) exec app php artisan config:clear
	$(DOCKER_COMPOSE) exec app php artisan route:clear
	$(DOCKER_COMPOSE) exec app php artisan view:clear

permissions: ## Fix storage permissions
	$(DOCKER_COMPOSE) exec app chown -R www-data:www-data /var/www/html/storage
	$(DOCKER_COMPOSE) exec app chown -R www-data:www-data /var/www/html/bootstrap/cache

backup-db: ## Backup external database
	@echo "$(BLUE)Creating database backup...$(NC)"
	@echo "Please run this manually with your external DB credentials:"
	@echo "mysqldump -h YOUR_HOST -u YOUR_USER -p YOUR_DATABASE > backup_\$$(date +%Y%m%d_%H%M%S).sql"

restore-db: ## Restore to external database
	@echo "$(BLUE)Restoring database...$(NC)"
	@echo "Please run this manually with your external DB credentials:"
	@echo "mysql -h YOUR_HOST -u YOUR_USER -p YOUR_DATABASE < $(file)"

clean: ## Clean up containers and volumes
	@echo "$(YELLOW)Cleaning up...$(NC)"
	$(DOCKER_COMPOSE) down -v --remove-orphans
	cd .. && docker system prune -f
	@echo "$(GREEN)Cleanup completed!$(NC)"

clean-volumes: ## Clean up only volumes
	@echo "$(YELLOW)Cleaning up volumes...$(NC)"
	$(DOCKER_COMPOSE) down
	cd .. && docker volume rm corevsphim_redis_data corevsphim_app_storage corevsphim_app_cache 2>/dev/null || true

reset: clean setup ## Full reset - clean and setup again
	@echo "$(GREEN)Full reset completed!$(NC)"

# Development shortcuts
dev: up npm-dev ## Start development environment
prod: up npm-prod optimize ## Start production environment

test-db: ## Test database connection
	@echo "$(BLUE)Testing database and services...$(NC)"
	./test-db.sh

# Monitoring
monitor: ## Monitor resource usage
	@echo "$(BLUE)Container resource usage:$(NC)"
	cd .. && docker stats --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}\t{{.NetIO}}\t{{.BlockIO}}"
