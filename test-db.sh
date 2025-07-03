#!/bin/bash

# Test database connection script
echo "🔍 Testing database connection..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Check if we're in the right directory
if [ ! -f "deploy/docker-compose.yml" ]; then
    echo -e "${RED}❌ Please run this script from the project root directory${NC}"
    exit 1
fi

# Check if containers are running
if ! cd deploy && docker-compose ps app | grep -q "Up"; then
    echo -e "${RED}❌ App container is not running. Please start containers first:${NC}"
    echo -e "${YELLOW}   make up${NC}"
    exit 1
fi

echo -e "${BLUE}Testing database connection...${NC}"

# Test database connection
if cd deploy && docker-compose exec -T app php artisan migrate:status > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connection successful!${NC}"

    echo -e "${BLUE}📊 Database status:${NC}"
    cd deploy && docker-compose exec -T app php artisan migrate:status

    echo -e "${BLUE}📋 Database info:${NC}"
    cd deploy && docker-compose exec -T app php -r "
        try {
            \$pdo = new PDO('mysql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT'), env('DB_USERNAME'), env('DB_PASSWORD'));
            echo 'Host: ' . env('DB_HOST') . ':' . env('DB_PORT') . PHP_EOL;
            echo 'Database: ' . env('DB_DATABASE') . PHP_EOL;
            echo 'Username: ' . env('DB_USERNAME') . PHP_EOL;
            echo 'Connection: Success' . PHP_EOL;

            \$result = \$pdo->query('SELECT VERSION() as version');
            if (\$result) {
                \$row = \$result->fetch();
                echo 'MySQL Version: ' . \$row['version'] . PHP_EOL;
            }
        } catch (Exception \$e) {
            echo 'Error: ' . \$e->getMessage() . PHP_EOL;
        }
    " && cd ..

else
    echo -e "${RED}❌ Database connection failed!${NC}"
    echo -e "${YELLOW}Troubleshooting steps:${NC}"
    echo -e "${YELLOW}1. Check your database credentials in .env file${NC}"
    echo -e "${YELLOW}2. Ensure your external database is running and accessible${NC}"
    echo -e "${YELLOW}3. Check firewall settings${NC}"
    echo -e "${YELLOW}4. Verify database host and port${NC}"
    echo ""
    echo -e "${BLUE}Current database settings:${NC}"
    cd deploy && docker-compose exec -T app php -r "
        echo 'Host: ' . env('DB_HOST', 'not set') . PHP_EOL;
        echo 'Port: ' . env('DB_PORT', 'not set') . PHP_EOL;
        echo 'Database: ' . env('DB_DATABASE', 'not set') . PHP_EOL;
        echo 'Username: ' . env('DB_USERNAME', 'not set') . PHP_EOL;
        echo 'Password: ' . (env('DB_PASSWORD') ? '[SET]' : '[NOT SET]') . PHP_EOL;
    " && cd ..
fi

# Test Redis connection
echo ""
echo -e "${BLUE}Testing Redis connection...${NC}"
if cd deploy && docker-compose exec -T redis redis-cli -a "$(grep REDIS_PASSWORD .env.docker | cut -d'=' -f2 | tr -d '"')" ping > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Redis connection successful!${NC}"
else
    echo -e "${RED}❌ Redis connection failed!${NC}"
fi

echo ""
echo -e "${BLUE}📊 Container health status:${NC}"
cd deploy && docker-compose ps && cd ..
