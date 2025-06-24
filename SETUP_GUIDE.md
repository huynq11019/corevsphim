# 🎬 HƯỚNG DẪN SETUP DỰ ÁN COREVSPHIM

## 📋 YÊU CẦU HỆ THỐNG

### PHP Extensions cần thiết:
- ✅ fileinfo
- ✅ exif  
- ✅ mbstring
- ✅ ionCube (nếu có mã hóa)

### PHP Functions cần disable:
- ❌ putenv
- ❌ proc_open
- ❌ shell_exec
- ❌ symlink

### Công nghệ cần cài đặt:
- **PHP 8.2.0+** (QUAN TRỌNG: Dự án yêu cầu PHP >= 8.2.0)
- MySQL/MariaDB
- Composer
- Node.js & NPM

## 🛠️ HƯỚNG DẪN CÀI ĐẶT

### ⚠️ **BƯỚC QUAN TRỌNG: Upgrade PHP**
```bash
# Kiểm tra phiên bản PHP hiện tại
php -v

# Trên macOS với Homebrew:
# 1. Cài đặt PHP 8.2
brew install php@8.2

# 2. Unlink các phiên bản PHP cũ
brew unlink php@7.4 php@8.1  # Nếu có
brew unlink php@8.2 && brew link --force php@8.2

# 3. Cập nhật PATH (QUAN TRỌNG!)
echo 'export PATH="/usr/local/opt/php@8.2/bin:$PATH"' >> ~/.zshrc
echo 'export PATH="/usr/local/opt/php@8.2/sbin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# 4. Verify PHP version (PHẢI RESTART TERMINAL)
php -v  # Phải là 8.2.x trở lên
```

### Bước 1: Clone và cài đặt dependencies
```bash
# Cài đặt PHP dependencies (có thể gặp lỗi theme)
composer install

# Nếu gặp lỗi "NQT\ThemeThemPho\ThemeThemPhoServiceProvider not found":
composer remove nqt/theme-thempho
composer install

# Cài đặt Node.js dependencies  
npm install
```

### Bước 2: Cấu hình môi trường
```bash
# Copy file environment
cp .env.example .env

# Generate app key
php artisan key:generate
```

### Bước 3: Cấu hình database
```bash
# Tạo database trong MySQL
CREATE DATABASE corevsphim;

# Import dữ liệu mẫu (KHUYẾN NGHỊ)
mysql -u username -p corevsphim < sqltheme.sql

# HOẶC chạy migrations (nếu không có sqltheme.sql)
php artisan migrate

# Seed dữ liệu (nếu có)
php artisan db:seed
```

### Bước 4: Tạo user admin
```bash
php artisan ophim:user
```

### Bước 5: Generate menu và optimize
```bash
# Tạo menu categories & regions
php artisan ophim:menu:generate

# Clear cache
php artisan optimize:clear
```

### Bước 6: Build assets
```bash
# Development
npm run dev

# Production
npm run production

# Watch mode (development)
npm run watch
```

### Bước 7: Chạy server
```bash
# Laravel development server
php artisan serve

# Server sẽ chạy tại: http://127.0.0.1:8000 hoặc http://localhost:8000
# Mở trình duyệt và truy cập URL trên để test giao diện

# Hoặc sử dụng với Apache/Nginx (production)
```

## 🌐 URL TEST GIAO DIỆN

### Development URLs:
- **Trang chủ**: http://127.0.0.1:8000
- **Admin panel**: http://127.0.0.1:8000/admin (nếu có)
- **API docs**: http://127.0.0.1:8000/api (nếu có)

### Các trang test quan trọng:
- Trang danh sách phim
- Trang chi tiết phim  
- Trang player xem phim
- Trang tìm kiếm
- Responsive test (mobile/tablet)

## ⚙️ CẤU HÌNH PRODUCTION

### Crontab cho reset view counter:
```bash
# Thêm vào crontab: crontab -e
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Web Server Configuration (Nginx):
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/project/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🎨 TÙYCH CHỈNH GIAO DIỆN

### Thay đổi theme:
- Theme hiện tại: `motchill` và `thempho`
- File cấu hình theme trong: `config/themes/`
- CSS custom: `public/themes/[theme-name]/css/custom.css`

### Màu sắc chủ đạo:
- **motchill**: Tông đỏ Netflix (#ff9601, #e74c3c)
- **thempho**: Tông cam đồng (#da966e, #c58560)

## 🔧 LỆNH ARTISAN HỮU ÍCH

```bash
# Ophim commands
php artisan ophim:user              # Tạo user
php artisan ophim:menu:generate     # Generate menu
php artisan ophim:crawler           # Crawl phim

# Laravel standard commands
php artisan cache:clear            # Clear cache
php artisan config:cache           # Cache config
php artisan route:cache            # Cache routes
php artisan view:cache             # Cache views
```

## 📱 RESPONSIVE & SEO

Dự án đã được tối ưu:
- ✅ Responsive design cho mobile/tablet
- ✅ SEO-friendly URLs
- ✅ Sitemap tự động
- ✅ Meta tags động
- ✅ Schema markup cho phim
- ✅ CDN ready

## 🚨 LƯU Ý BẢO MẬT

1. **Production**:
   - Đặt `APP_DEBUG=false`
   - Sử dụng HTTPS
   - Backup database định kỳ

2. **Performance**:
   - Enable caching
   - Optimize images
   - Use CDN cho static files

3. **SEO**:
   - Cấu hình robots.txt
   - Submit sitemap lên Google
   - Monitor với Google Analytics

## 🔥 TROUBLESHOOTING - XỬ LÝ LỖI THƯỜNG GẶP

### ❌ Lỗi: "PHP version >= 8.2.0 required"
```bash
# Cài đặt PHP 8.2 trên macOS
brew install php@8.2
brew link php@8.2 --force --overwrite

# Cài đặt PHP 8.2 trên Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml

# Kiểm tra lại
php -v
```

### ❌ Lỗi Migration
```bash
# Nếu migrate không chạy được, thử:
1. Import file sqltheme.sql trước (KHUYẾN NGHỊ)
2. Hoặc tạo database trống và chạy:
   php artisan migrate:fresh --seed
```

### ❌ Lỗi "Class not found" 
```bash
# Clear autoload và regenerate
composer dump-autoload
php artisan clear-compiled
php artisan optimize:clear
```

### ❌ Lỗi Permission
```bash
# Fix permissions trên Linux/macOS
sudo chown -R www-data:www-data storage/ bootstrap/cache/
sudo chmod -R 775 storage/ bootstrap/cache/
```

### ❌ Lỗi "Key too long" (MySQL)
```sql
-- Thêm vào file migration hoặc chạy SQL:
ALTER DATABASE corevsphim CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
