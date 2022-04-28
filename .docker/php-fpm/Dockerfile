FROM php:8.0-fpm-buster
ARG TIMEZONE

ENV ACCEPT_EULA=Y

COPY mirth-pool-1.conf /usr/local/etc/php-fpm.d/mirth-pool-1.conf
COPY mirth-pool-2.conf /usr/local/etc/php-fpm.d/mirth-pool-2.conf

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

COPY php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN apt-get update && apt-get install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev \
    libcurl4 \
    libcurl4-openssl-dev \
    acl \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc

    # Install prerequisites required for tools and extensions installed later on.
RUN apt-get install -y apt-transport-https gnupg2 libpng-dev libzip-dev unzip \
    && rm -rf /var/lib/apt/lists/*

    # Install prerequisites for the sqlsrv and pdo_sqlsrv PHP extensions.
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && apt-get install -y msodbcsql17 mssql-tools unixodbc-dev \
    && rm -rf /var/lib/apt/lists/*

    # Retrieve the script used to install PHP extensions from the source container.
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions

# Install required PHP extensions and all their prerequisites available via apt.
RUN chmod uga+x /usr/bin/install-php-extensions \
    && sync \
    && install-php-extensions bcmath ds exif gd intl opcache pcntl pdo_sqlsrv redis sqlsrv zip

RUN pear config-set php_ini ${PHP_ROOT_DIR}/php/php.ini

RUN pecl install pdo_sqlsrv-5.10.0 sqlsrv-5.10.0 \
    && docker-php-ext-enable pdo_sqlsrv sqlsrv

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY xdebug.ini /usr/local/etc/php/conf.d/

RUN docker-php-ext-configure gd --with-jpeg --with-freetype 

RUN docker-php-ext-install \
    curl pdo pdo_mysql xsl mbstring sockets

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh
ENTRYPOINT [ "/docker-entrypoint.sh" ]

# CMD composer update --no-plugins ; php-fpm