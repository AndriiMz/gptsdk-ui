FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk update \
    && apk install -y php8.3-cli php8.3-dev \
       php8.3-pgsql php8.3-sqlite3 php8.3-gd \
       php8.3-curl php8.3-mongodb \
       php8.3-imap php8.3-mysql php8.3-mbstring \
       php8.3-xml php8.3-zip php8.3-bcmath php8.3-soap \
       php8.3-intl php8.3-readline \
       php8.3-ldap \
       php8.3-msgpack php8.3-igbinary php8.3-redis \
       php8.3-memcached php8.3-pcov php8.3-imagick php8.3-xdebug php8.3-swoole

RUN composer install
RUN composer install && \
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apk install -y nodejs && \
    npm install && \
    npm run build

RUN echo "Caching config..." && \
    php artisan config:cache && \
    echo "Caching routes..." && \
    php artisan route:cache && \
    echo "Running migrations..." && \
    php artisan migrate --force

CMD ["/start.sh"]
