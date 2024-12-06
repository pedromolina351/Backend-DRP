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
    libodbc1

# Crear directorio necesario para el socket de PHP-FPM
RUN mkdir -p /var/run/php && \
    chown www-data:www-data /var/run/php

# Configurar PHP-FPM para usar el socket
RUN sed -i 's|^listen =.*|listen = /var/run/php/php8.2-fpm.sock|' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's|^user =.*|user = www-data|' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's|^group =.*|group = www-data|' /usr/local/etc/php-fpm.d/www.conf

# Copiar y configurar un archivo php.ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/php.ini && \
    echo "max_execution_time = 120" >> /usr/local/etc/php/php.ini

# Configurar el directorio de trabajo
WORKDIR /var/www

# Copiar el proyecto al contenedor
COPY . .

# Copiar el archivo de configuración de Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Configurar permisos para los logs de Nginx
RUN chmod -R 777 /var/log/nginx

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Instalar Node.js y dependencias de npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs
RUN npm ci

# Construir el proyecto con npm
RUN npm run build

# Verificar configuración de PHP y socket
RUN php --ini
RUN ls -la /var/run/php/php8.2-fpm.sock || echo "Socket no creado"

# Exponer el puerto configurado
EXPOSE $PORT

# Comando de inicio
CMD php-fpm -F & nginx -g "daemon off;"