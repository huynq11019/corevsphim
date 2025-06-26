# 🐳 Docker Deployment for CoreVsPhim

Thư mục này chứa tất cả các file cần thiết để deploy CoreVsPhim bằng Docker với External Database.

## 📁 Cấu trúc thư mục:

```
deploy/
├── docker-compose.yml     # Main orchestration file (External DB)
├── Dockerfile            # Laravel app container
├── .env.docker          # Environment config for Docker
├── .dockerignore        # Files to ignore when building
├── docker/              # Docker configurations
│   └── nginx/           # Nginx web server config
│       └── default.conf # Nginx virtual host
├── DATABASE.md          # External database setup guide
└── README.md           # This file
```

## 🚀 Cách sử dụng:

### Bước 1: Chuẩn bị External Database
Đọc file [DATABASE.md](DATABASE.md) để cấu hình database external.

### Bước 2: Chạy Docker
```bash
# Từ thư mục gốc của project
./docker-setup.sh

# Hoặc dùng Makefile
make setup
make up
```

### Từ thư mục deploy:
```bash
cd deploy

# Cập nhật .env.docker với thông tin DB của bạn
cp .env.docker .env.docker.local
# Sửa DB_HOST, DB_USERNAME, DB_PASSWORD...

# Build containers
docker-compose build --no-cache

# Start services
docker-compose up -d

# Check health
docker-compose ps
```

## 🛠️ Services:

### Core Services:
- **app**: Laravel PHP 8.2 application với FPM
  - Port: không expose trực tiếp
  - Health check: `/health` endpoint
  - Volumes: code, storage, cache
  
- **webserver**: Nginx 1.21 Alpine
  - Port: `NGINX_PORT` (default: 8080)
  - Serves static files và proxy PHP requests
  
- **redis**: Redis 7.0 Alpine
  - Port: `REDIS_PORT` (default: 6380)
  - Password protected
  - Persistent data với volume
  
### Management Tools:
- **phpmyadmin**: phpMyAdmin 5.2
  - Port: `PHPMYADMIN_PORT` (default: 8081)
  - Kết nối với external database
  - Upload limit: 500MB
- **webserver**: Nginx web server  
- **redis**: Redis caching service
- **phpmyadmin**: Database management interface (for external DB)

## 🗄️ Database:

Dự án sử dụng **external database**. Không có database container local.

### Bước setup database:

1. **Cấu hình database** trong `.env.docker`:
```env
DB_HOST=your-external-db-host.com
DB_DATABASE=corevsphim
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

2. **Import schema**: 
```bash
mysql -h your-host -u your-user -p corevsphim < ../sqltheme.sql
```

3. **Hoặc dùng phpMyAdmin** để import `../sqltheme.sql`

📋 **Chi tiết:** Đọc file [DATABASE.md](DATABASE.md) để cấu hình database chuyên nghiệp.

## 🌐 Access URLs:

URLs sẽ sử dụng port được cấu hình trong `.env.docker`:

- **Website**: http://localhost:${NGINX_PORT} (default: 8080)
- **phpMyAdmin**: http://localhost:${PHPMYADMIN_PORT} (default: 8081)  
- **Redis**: localhost:${REDIS_PORT} (default: 6380)

## � Monitoring & Health:

```bash
# Check services status
docker-compose ps

# Health check
make health

# View logs
docker-compose logs -f app
docker-compose logs -f webserver
docker-compose logs -f redis

# Resource monitoring
make monitor
```

## 🔧 Configuration:

### Environment Variables:
- **Primary**: `.env.docker` - Template configuration
- **Runtime**: Container sử dụng env vars từ docker-compose.yml
- **Ports**: Customizable thông qua .env.docker

### Volumes:
- `redis_data`: Redis persistent data
- `app_storage`: Laravel storage directory
- `app_cache`: Laravel bootstrap cache
- Code volume: Read-only mount từ parent directory

### Networks:
- `corevsphim_network`: Bridge network với custom subnet
- Internal communication giữa các containers

## ⚡ Performance Optimizations:

### Resource Limits:
- **App**: 1GB RAM, 80% CPU max
- **Redis**: 256MB RAM với LRU eviction
- **Nginx**: 128MB RAM
- **phpMyAdmin**: 512MB RAM

### Caching Strategy:
- **Redis**: Sessions, cache, và queues
- **Nginx**: Static file serving
- **Laravel**: Config, route, view caching

### Logging:
- **Rotation**: Automatic log rotation
- **Size limits**: 10MB per file, 3 files max
- **Level**: Error level cho production

## 🔒 Security Features:

- **Redis**: Password protected
- **Network**: Isolated bridge network  
- **Read-only**: Code volume mounted as read-only
- **No DB**: No local database exposure
- **Health checks**: Container health monitoring

## 📝 Best Practices:

- **Environment**: Sử dụng .env.docker cho configuration
- **Secrets**: Không commit passwords vào git
- **Backup**: Regular database backups của external DB
- **Monitoring**: Sử dụng `make health` và `make monitor`
- **Updates**: Update images regularly cho security patches
