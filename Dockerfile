FROM docker.io/composer:1.7.2 as dependencies
WORKDIR /app

# Dependencies definitions
COPY composer.json composer.lock ./
# Code is required to complete PHP autoloader
COPY . .

# Ignoring platform requirements because if not disabled, composer checks for php
# extension packages in the system which will be installed in the final stage
RUN composer validate \
    && composer install \
        --no-dev \
        --no-scripts --optimize-autoloader \
        --ignore-platform-reqs --no-interaction --no-progress --ansi 

# Bases for production image
FROM docker.io/php:7.2-cli
WORKDIR /app

# Production dependency files
COPY --from=dependencies /app/vendor/ vendor/
COPY --from=dependencies /app/composer.json /app/composer.lock ./

# PHP runtime extensions
RUN docker-php-source extract \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && docker-php-source delete

# Source code
COPY . .

# Laravel optimizations
RUN php artisan clear-compiled \
    && php artisan optimize \
    && php artisan package:discover \
    && php artisan vendor:publish

# Default runtime configuration
ENV APP_ENV=production APP_DEBUG=false \
    APP_KEY=base64:Q0hBTkdFX01FX1BMRUFTRQ== \
    DATABASE_URL=mysql://root:secret@database:3306/backend_laravel \
    WAIT_HOSTS=database:3306

## Add the wait script to the image
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.6.0/wait /bin/wait
RUN chmod +x /bin/wait

# Convenient entrypoint script
COPY docker-entrypoint.sh /bin
RUN chmod a+x /bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 8080
CMD php artisan serve --host 0.0.0.0 --port 8080
