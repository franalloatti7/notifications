# imagen base
FROM php:8.1-fpm-alpine
 
# definición de los argumentos
ARG USERID
ARG USERNAME

ADD ./config/install_sql_driver.sh /tmp
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN chmod +x /tmp/install_sql_driver.sh && \
    /tmp/install_sql_driver.sh && \
    install-php-extensions sqlsrv pdo_sqlsrv xlswriter

# instalación de composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
RUN set -ex; \
    addgroup -g $USERID $USERNAME &&\
    adduser -G $USERNAME -g $USERNAME -u $USERID -s /bin/sh -D $USERNAME; \
    mkdir -p /app &&\
    chown $USERNAME:$USERNAME /app; \
    # dependencias
    apk add --no-cache $PHPIZE_DEPS \
        libpng \
        libpng-dev \
        zlib \
        zlib-dev \
        zip \
        git \
        curl \
        libxml2-dev \
        libzip-dev; \
    docker-php-ext-install \
        pdo \
        pdo_mysql \
        exif \
        pcntl \
        bcmath \
        gd \
        xml \
        soap \
        zip; \
    rm -rf /tmp/pear;

RUN apk add --update nodejs npm less

ARG XDEBUG
ARG ENVIRONMENT

# Si XDEBUG esta seteado en 1 lo instalo
RUN \
if [ ${XDEBUG} -eq 1 ]; \
    then pecl install -o -f xdebug-3.1.5 \
    && docker-php-ext-enable xdebug; \
else \
echo ***xdebug was not install*** ; \
fi


COPY ./config/php$ENVIRONMENT.ini /usr/local/etc/php/php.ini
COPY ./config/fpm.conf /usr/local/etc/php-fpm.d/www.conf

# directorio de trabajo
WORKDIR /app/web

USER $USERNAME

# sudo chmod 666 /var/run/docker.sock