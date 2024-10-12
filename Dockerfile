# Use a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Instalar as dependências do sistema e extensões PHP necessárias em um único comando
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpng-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install zip pdo pdo_mysql mysqli gd \
    && rm -rf /var/lib/apt/lists/*

# Configurar o DocumentRoot para /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Copiar o conteúdo do diretório atual para o diretório raiz do Apache
COPY . /var/www/html/

# Ajustar permissões para a pasta de storage e logs do Laravel
RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# Configurar permissões para as pastas de upload e images
RUN mkdir -p /var/www/html/images /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/images /var/www/html/uploads \
    && chmod -R 755 /var/www/html/images /var/www/html/uploads

# Habilitar o módulo de reescrita do Apache
RUN a2enmod rewrite

# Configurações adicionais do PHP para aumentar o timeout
RUN echo "allow_url_fopen=On" >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo "short_open_tag=On" >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo "max_execution_time=600" >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo "max_input_time=600" >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo "default_socket_timeout=600" >> /usr/local/etc/php/conf.d/docker-php.ini

# Instalar o composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar as dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Configurar arquivos de log
RUN mkdir -p /var/log/apache2 /var/log/php \
    && touch /var/log/apache2/error.log /var/log/php/error.log \
    && chown -R www-data:www-data /var/log/apache2 /var/log/php \
    && chmod -R 755 /var/log/apache2 /var/log/php

# Expor a porta 80
EXPOSE 80

# Definir o comando padrão a ser executado quando o contêiner iniciar
CMD ["apache2-foreground"]