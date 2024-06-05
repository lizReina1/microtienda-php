# Usa una imagen base de PHP
FROM php:8.1-cli

# Instala dependencias de Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libpng-dev \
    && docker-php-ext-install zip pdo_mysql gd

# Copia los archivos de la aplicación en el contenedor
COPY . /app

# Establece el directorio de trabajo
WORKDIR /app

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala las dependencias de la aplicación
RUN composer install

# Copia wait-for-it.sh y establece permisos
COPY wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

# Expone el puerto 8000
EXPOSE 8000

# Ejecuta el servidor de desarrollo de Laravel
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
