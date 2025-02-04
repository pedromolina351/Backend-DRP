# Usa la imagen oficial de PHP 8.2-fpm basada en Ubuntu 22.04
FROM php:8.2-fpm

# Configurar DEBIAN_FRONTEND como noninteractive para evitar la necesidad de entrada del usuario
ENV DEBIAN_FRONTEND=noninteractive
ENV PORT 8080

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    gnupg2 \
    openssl \
    unixodbc \
    unixodbc-dev \
    libgssapi-krb5-2 \
    curl \
    lsb-release \
    apt-transport-https \
    libssl-dev \
    ca-certificates \
    build-essential \
    autoconf \
    tzdata \
    zip \
    unzip \
    git \
    nginx \
    libodbc1 \
    libzip-dev

# Instalar la extensión zip para PHP
RUN docker-php-ext-configure zip && \
docker-php-ext-install zip

# Instalar y habilitar la extensión GD
# RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
#     docker-php-ext-install gd

# Crear directorio necesario para el socket de PHP-FPM
RUN mkdir -p /var/run/php && \
    chown www-data:www-data /var/run/php

# Configurar PHP para cargar un archivo php.ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/php.ini && \
    echo "max_execution_time = 120" >> /usr/local/etc/php/php.ini 
    
# Agregar clave y repositorio de Microsoft
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/ubuntu/22.04/prod.list -o /etc/apt/sources.list.d/mssql-release.list

# Actualizar repositorios e instalar drivers de SQL Server
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18

# Instalar extensiones de PHP necesarias usando herramientas oficiales
RUN docker-php-ext-install -j$(nproc) mysqli pdo pdo_mysql && \
    pecl install sqlsrv pdo_sqlsrv && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv

# Configurar certificados SSL
RUN curl -o /usr/local/etc/php/conf.d/ca-certificates.crt https://curl.se/ca/cacert.pem && \
    echo 'openssl.cafile=/usr/local/etc/php/conf.d/ca-certificates.crt' > /usr/local/etc/php/conf.d/openssl.ini

# Configurar el directorio de trabajo
WORKDIR /var/www

# Copiar el proyecto al contenedor
COPY . .

# Copiar el archivo de configuración de Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Configurar permisos para los logs de Nginx y Configurar permisos para Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 777 /var/log/nginx
RUN chmod -R 777 /var/www/storage/app

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Instalar Node.js y dependencias de npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs
RUN npm ci

# Construir el proyecto con npm
RUN npm run build

# Verificar configuración de PHP-FPM
RUN php-fpm --test || echo "Error en configuración de PHP-FPM"
RUN php -m | grep zip
#RUN php -m | grep gd

# Agregar script de verificación del socket y logs al inicio
RUN echo '#!/bin/bash\n' \
         'php-fpm -F &\n' \
         'sleep 5\n' \
         'if [ ! -S /var/run/php/php8.2-fpm.sock ]; then\n' \
         '  echo "Socket no creado. Verifica configuración de PHP-FPM" >> /var/log/php-fpm-check.log\n' \
         'fi\n' \
         'nginx -g "daemon off;"\n' > /start.sh && chmod +x /start.sh

# Exponer el puerto configurado
EXPOSE $PORT

# Comando de inicio
CMD ["/start.sh"]
