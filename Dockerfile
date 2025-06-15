FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    libzip-dev \
    supervisor \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build  # Build frontend assets

# Add default files for container startup
RUN echo '#!/usr/bin/env bash\n\
if [ ! -d /.composer ]; then\n\
    mkdir /.composer\n\
fi\n\
\n\
chmod -R ugo+rw /.composer\n\
\n\
if [ $# -gt 0 ]; then\n\
    exec "$@"\n\
else\n\
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf\n\
fi\n' > /usr/local/bin/start-container && chmod +x /usr/local/bin/start-container

RUN echo '[supervisord]\n\
nodaemon=true\n\
user=root\n\
logfile=/var/log/supervisor/supervisord.log\n\
pidfile=/var/run/supervisord.pid\n\
\n\
[program:php]\n\
command=%(ENV_SUPERVISOR_PHP_COMMAND)s\n\
user=%(ENV_SUPERVISOR_PHP_USER)s\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0' > /etc/supervisor/conf.d/supervisord.conf
RUN echo '[PHP]\npost_max_size = 100M\nupload_max_filesize = 100M\nvariables_order = EGPCS\npcov.directory = .' > /etc/php/8.3/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80

ENTRYPOINT ["start-container"]
