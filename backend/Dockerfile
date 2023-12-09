# Use a imagem oficial do PHP
FROM php:8.2.10-fpm

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Copie os arquivos do projeto para o contêiner
COPY . .

# Instalação de dependências do PHP e do Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalação das dependências do Composer
RUN composer install

# Expõe a porta em que o servidor web PHP estará em execução
EXPOSE 80

# Comando para iniciar o servidor web PHP
CMD ["php", "-S", "0.0.0.0:80", "-t", "."]
