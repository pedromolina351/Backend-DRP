# Usa la imagen oficial de PHP 8.2-fpm basada en Ubuntu 22.04
FROM php:8.2-fpm

# Configurar DEBIAN_FRONTEND como noninteractive para evitar la necesidad de entrada del usuario
ENV DEBIAN_FRONTEND=noninteractive

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
    git

# Agregar clave y repositorio de Microsoft
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/ubuntu/22.04/prod.list -o /etc/apt/sources.list.d/mssql-release.list

# Actualizar repositorios e instalar drivers de SQL Server
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18

# Instalar PEAR manualmente
RUN curl -O https://pear.php.net/go-pear.phar && \
    php go-pear.phar

# Instalar las extensiones de PHP necesarias
RUN pecl install sqlsrv-5.12.0 pdo_sqlsrv-5.12.0

# Crear un directorio para configuraciones adicionales
RUN mkdir -p /usr/local/etc/php/conf.d

# Habilitar extensiones en php.ini manualmente
RUN echo 'extension=sqlsrv.so' > /usr/local/etc/php/conf.d/docker-php-ext-sqlsrv.ini && \
    echo 'extension=pdo_sqlsrv.so' > /usr/local/etc/php/conf.d/docker-php-ext-pdo_sqlsrv.ini

# Configurar OpenSSL para usar el certificado descargado
RUN curl -o /usr/local/etc/php/conf.d/ca-certificates.crt https://curl.se/ca/cacert.pem && \
    echo 'openssl.cafile=/usr/local/etc/php/conf.d/ca-certificates.crt' > /usr/local/etc/php/conf.d/openssl.ini

# Configurar el directorio de trabajo
WORKDIR /var/www

# Copiar el proyecto al contenedor
COPY . .

# Instalar dependencias de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instalar dependencias de npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \ 
    apt-get install -y nodejs
RUN npm ci

RUN php --ini
RUN php -i | grep openssl
RUN php -i | grep sqlsrv

# Exponer el puerto
EXPOSE 9000
# Comando de inicio 
CMD ["php-fpm"]