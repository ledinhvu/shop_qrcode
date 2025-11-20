# Laravel QR Code Project

Yêu cầu: PHP >= 8.1, Composer, MySQL hoặc database tương thích, Node.js & npm/yarn nếu project có front-end.

Cài đặt dự án:

```bash
# Clone repository
git clone https://github.com/ledinhvu/shop_qrcode.git
cd shop_qrcode

# Cài các package PHP
composer install

# Tạo file .env từ mẫu
cp .env.example .env

# Chỉnh cấu hình database trong .env
# Mở file .env và sửa:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database_name
# DB_USERNAME=your_db_user
# DB_PASSWORD=your_db_password

# Generate application key
php artisan key:generate

# Chạy migration
php artisan migrate

# Seed dữ liệu mặc định
php artisan db:seed

# Chạy server local
php artisan serve
