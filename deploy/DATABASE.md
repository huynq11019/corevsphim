# 🗄️ Database Configuration Guide

## External Database Setup

Vì dự án sử dụng database external, bạn cần cấu hình kết nối database trong file `.env`:

### 1. Cập nhật thông tin database:
```env
DB_CONNECTION=mysql
DB_HOST=your-database-host.com
DB_PORT=3306
DB_DATABASE=corevsphim
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### 2. Import database schema:
```bash
# Nếu chưa có database, tạo database trước:
mysql -h your-host -u your-username -p -e "CREATE DATABASE corevsphim CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema từ file SQL:
mysql -h your-host -u your-username -p corevsphim < sqltheme.sql
```

### 3. Database providers khuyến nghị:

#### AWS RDS:
- Tự động backup
- High availability
- Monitoring tích hợp

#### Google Cloud SQL:
- Easy scaling
- Automatic updates
- Security tích hợp

#### Azure Database:
- Global distribution
- AI-powered performance
- Comprehensive security

#### DigitalOcean Managed Database:
- Cost-effective
- Simple setup
- Good performance

### 4. Security considerations:

```env
# Sử dụng SSL connection
DB_SSLMODE=require

# Restrict database access
# Chỉ cho phép IP của server Docker kết nối
```

### 5. Performance optimization:

```sql
-- Optimize MySQL configuration
SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL max_connections = 200;
```

### 6. Backup strategy:

```bash
# Daily backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -h your-host -u your-username -p corevsphim > backup_$DATE.sql

# Compress backup
gzip backup_$DATE.sql

# Keep only last 7 days of backups
find . -name "backup_*.sql.gz" -mtime +7 -delete
```

### 7. Monitoring:

```bash
# Check database connection from container
make shell
php artisan tinker
DB::connection()->getPdo();
```
