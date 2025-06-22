# Gunakan image PHP-FPM resmi sebagai base
FROM php:8.2-fpm-alpine

# Instal dependency sistem yang dibutuhkan oleh PHP extensions
# Termasuk mysql-client untuk tools database jika diperlukan
RUN apk add --no-cache \
    mysql-client \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    icu-dev \
    zlib-dev # Tambahkan ini untuk zip extension

# Instal ekstensi PHP yang dibutuhkan untuk aplikasi Anda
# mysqli untuk koneksi MySQL, gd untuk manipulasi gambar, zip untuk kompresi/dekompresi
RUN docker-php-ext-install -j$(nproc) pdo_mysql mysqli gd zip intl

# Konfigurasi PHP FPM (opsional, sesuaikan jika Anda punya kebutuhan khusus)
# Jika Anda punya php.ini khusus, Anda bisa membuat file php.ini di root proyek
# dan meng-copy-nya seperti ini: COPY php.ini /usr/local/etc/php/php.ini

# Atur working directory di dalam container
WORKDIR /var/www/html

# Salin semua file aplikasi dari host ke container
COPY . /var/www/html

# Atur owner file ke www-data (user yang digunakan oleh PHP-FPM)
RUN chown -R www-data:www-data /var/www/html

# Ekspos port 9000 (port default PHP-FPM)
EXPOSE 9000

# Perintah default ketika container dijalankan
CMD ["php-fpm"]