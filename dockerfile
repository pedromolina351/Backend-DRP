# Usar una imagen base de PHP 8.2
FROM php:8.2-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    gnupg2 \
    unixodbc \
    unixodbc-dev \
    libgssapi-krb5-2 \
    curl \
    && apt-get clean

# Agregar el repositorio de Microsoft para los drivers de SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/$(lsb_release -rs)/prod.list > /etc/apt/sources.list.d/mssql-release.list

# Instalar herramientas y drivers de SQL Server
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y \
    msodbcsql18 \
    mssql-tools18 \
    && echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc \
    && apt-get install -y unixodbc-dev

# Instalar extensiones PDO_SQLSRV y SQLSRV usando PECL
RUN pecl install pdo_sqlsrv sqlsrv \
    && docker-php-ext-enable pdo_sqlsrv sqlsrv

# Instalar extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Copiar los archivos de la aplicación
WORKDIR /var/www/html
COPY . .

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Instalar Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Configuración final
EXPOSE 9000
CMD ["php-fpm"]
