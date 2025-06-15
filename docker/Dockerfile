FROM ubuntu:24.04

### https://chatgpt.com/c/68431a74-5190-8006-b0ef-0c768a067c8c
LABEL maintainer="Andrii Moroz"

ARG WWWGROUP=1000
ARG NODE_VERSION=22
ARG MYSQL_ROOT_PASSWORD=gptsdk
ARG MYSQL_CLIENT="mysql-client"

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="sail"


ENV APP_NAME=GptSdk
ENV APP_ENV=production
ENV APP_KEY=base64:u/KEN0+rtafipBfS9pekQUCrKqXGLKOaJU8gUI9EW5c=
ENV APP_TIMEZONE=UTC
ENV APP_URL=http://localhost:80
ENV APP_PORT=80
ENV APP_AUTH_GUARD=session

ENV DB_CONNECTION=mysql
ENV DB_HOST=127.0.0.1
ENV DB_PORT=3306
ENV DB_DATABASE=gptsdk
ENV DB_USERNAME=root
ENV DB_PASSWORD=gptsdk

# Timezone setup
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Performance & proxy fix
RUN echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::BrokenProxy true;" >> /etc/apt/apt.conf.d/99custom

# System dependencies and PHP
RUN apt-get update && apt-get upgrade -y && \
    mkdir -p /etc/apt/keyrings && \
    apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php8.3-cli php8.3-dev php8.3-mbstring php8.3-xml php8.3-mysql php8.3-curl php8.3-bcmath php8.3-zip php8.3-gd php8.3-pdo php8.3-tokenizer php8.3-readline \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Node & build tools
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list && \
    apt-get update && \
    apt-get install -y nodejs && \
    npm install -g npm && \
    npm install -g pnpm bun yarn

# MySQL & PostgreSQL clients
RUN apt-get install -y $MYSQL_CLIENT && \
    curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null && \
    echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt noble-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
    apt-get update && \
    apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configure MySQL and create data directory
RUN apt-get update && apt-get install -y mysql-server && \
    mkdir -p /var/lib/mysql /var/run/mysqld

# PHP bind permissions
RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.3

# Create sail user
RUN groupadd --force -g $WWWGROUP sail && \
    useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

# Clone the Laravel + Vue project
RUN git clone https://github.com/AndriiMz/gptsdk-ui /var/www/html

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN service mysql start && \
    mysql -u root -e "FLUSH PRIVILEGES;" && \
    mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$MYSQL_ROOT_PASSWORD';" && \
    mysql -u root -p$MYSQL_ROOT_PASSWORD -e "CREATE DATABASE IF NOT EXISTS gptsdk;" && \
    mysql -u root -p$MYSQL_ROOT_PASSWORD -e "CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY '$MYSQL_ROOT_PASSWORD';" && \
    mysql -u root -p$MYSQL_ROOT_PASSWORD -e "GRANT ALL PRIVILEGES ON gptsdk.* TO 'root'@'%';" && \
    mysql -u root -p$MYSQL_ROOT_PASSWORD -e "FLUSH PRIVILEGES;" && \
    service mysql stop

# Install JS dependencies and build
RUN npm install && npm run build

# Add default files for container startup
RUN echo '#!/usr/bin/env bash\n\
if [ "$SUPERVISOR_PHP_USER" != "root" ] && [ "$SUPERVISOR_PHP_USER" != "sail" ]; then\n\
    echo "You should set SUPERVISOR_PHP_USER to either '\''sail'\'' or '\''root'\''." \n\
    exit 1\n\
fi\n\
\n\
if [ ! -z "$WWWUSER" ]; then\n\
    usermod -u $WWWUSER sail\n\
fi\n\
\n\
if [ ! -d /.composer ]; then\n\
    mkdir /.composer\n\
fi\n\
\n\
chmod -R ugo+rw /.composer\n\
\n\
if [ $# -gt 0 ]; then\n\
    if [ "$SUPERVISOR_PHP_USER" = "root" ]; then\n\
        exec "$@"\n\
    else\n\
        exec gosu $WWWUSER "$@"\n\
    fi\n\
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
environment=LARAVEL_SAIL="1"\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0' > /etc/supervisor/conf.d/supervisord.conf
RUN echo '[PHP]\npost_max_size = 100M\nupload_max_filesize = 100M\nvariables_order = EGPCS\npcov.directory = .' > /etc/php/8.3/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

# Update supervisord.conf to include MySQL
RUN echo "[program:mysql]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command=/usr/bin/mysqld_safe" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "autostart=true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "autorestart=true" >> /etc/supervisor/conf.d/supervisord.conf

RUN echo '[mysqld]\nbind-address=0.0.0.0\nport=3306\ndatadir=/var/lib/mysql' > /etc/mysql/my.cnf

RUN chown -R sail:$WWWGROUP /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R ug+rw /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R sail:$WWWGROUP /var/lib/mysql /var/run/mysqld && \
    chmod -R 777 /var/lib/mysql /var/run/mysqld

# Run migrations
RUN service mysql start &&  \
    php artisan migrate --force && \
    php artisan config:clear && \
    php artisan cache:clear

EXPOSE 80 3306

VOLUME ["/var/lib/mysql"]

ENTRYPOINT ["start-container"]
