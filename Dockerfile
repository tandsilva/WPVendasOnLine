# Usa a imagem oficial do WordPress
FROM wordpress:php8.2-apache

# Copia WP local para dentro do container (se você já possui arquivos)
COPY . /var/www/html

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expõe porta (Railway injeta $PORT automaticamente)
EXPOSE 80
