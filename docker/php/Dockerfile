FROM surnet/alpine-wkhtmltopdf:3.16.2-0.12.6-full as wkhtmltopdf
FROM php:8.3-fpm-alpine3.17 as prod
ENV APP_ENV=prod
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN apk add --no-cache \
        bash \
		acl \
		fcgi \
		file \
		gettext \
		git \
        yarn \
        libstdc++ \
        libx11 \
        libxrender \
        libxext \
        libssl1.1 \
        ca-certificates \
        fontconfig \
        freetype \
        ttf-droid \
        ttf-freefont \
        ttf-liberation \
	;
RUN set -eux; \
    install-php-extensions \
        	apcu \
        	intl \
    		opcache \
        	zip \
            redis \
            pdo_sqlite \
            pdo_pgsql\
            amqp \
            ldap \
            xsl \
            ;

COPY --from=wkhtmltopdf /bin/wkhtmltopdf /bin/libwkhtmltox.so /bin/

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/symfony
COPY ./app/composer.* ./app/symfony.* ./
RUN set -eux; \
    if [ -f composer.json ]; then \
		composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
		composer clear-cache; \
    fi
COPY ./app ./
RUN set -eux; \
	mkdir -p var/cache var/log; \
    if [ -f composer.json ]; then \
		composer dump-autoload --classmap-authoritative --no-dev; \
		composer dump-env prod; \
		composer run-script --no-dev post-install-cmd; \
		chmod +x bin/console; sync; \
    fi


FROM prod as dev

ENV APP_ENV=dev














