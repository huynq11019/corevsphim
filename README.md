# 🎬 CoreVsPhim - Website Xem Phim

## 🚀 QUICK START với Docker (KHUYẾN NGHỊ)

### Yêu cầu:
- Docker & Docker Compose
- Git

### Cài đặt nhanh:
```bash
# Clone project
git clone <repository-url>
cd corevsphim

# Run setup script
./docker-setup.sh
```

### URLs sau khi setup:
- **Website**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081 (user: root, pass: root_password)

---

## 📦 SETUP THỦ CÔNG (Traditional)

# Có sẵn Crawl
# Installation:
1. Extension PHP: fileinfo, exif, mbstring , ionCube
2. Disable function PHP: putenv, proc_open, shell_exec, symlink

3. Import file sqltheme.sql vào database 
4. Cấu hình file .env

5. Create new user by command: `php artisan ophim:user`
6. Run php artisan storage:link
7. Run `php artisan optimize:clear`

# Command:
- Generate menu categories & regions: `php artisan ophim:menu:generate`

# Reset view counter:
- Setup crontab, add this entry:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🐳 DOCKER COMMANDS

### Quản lý containers:
```bash
# Start all services
cd deploy && docker-compose up -d

# Stop all services
cd deploy && docker-compose down

# View logs
cd deploy && docker-compose logs -f app

# Restart specific service
cd deploy && docker-compose restart app

# Access app container
cd deploy && docker-compose exec app bash

# Access database
cd deploy && docker-compose exec db mysql -u root -p
```

### Laravel commands trong Docker:
```bash
# Artisan commands
cd deploy && docker-compose exec app php artisan migrate
cd deploy && docker-compose exec app php artisan ophim:user
cd deploy && docker-compose exec app php artisan ophim:menu:generate

# Composer commands
cd deploy && docker-compose exec app composer install
cd deploy && docker-compose exec app composer update

# NPM commands
cd deploy && docker-compose exec app npm install
cd deploy && docker-compose exec app npm run dev
```

### Backup & Restore:
```bash
# Backup database
cd deploy && docker-compose exec db mysqldump -u root -p corevsphim > backup.sql

# Restore database
cd deploy && docker-compose exec -i db mysql -u root -p corevsphim < backup.sql
```

## 🛠️ DOCKER TROUBLESHOOTING

### Port conflicts:
```bash
# Change ports in deploy/docker-compose.yml if needed:
# webserver ports: "8080:80" -> "8090:80"
# phpmyadmin ports: "8081:80" -> "8091:80"
```

### Reset everything:
```bash
# Remove all containers and volumes
cd deploy && docker-compose down -v
docker system prune -a

# Start fresh
./docker-setup.sh
```
