FROM php:7.4-apache
RUN docker-php-ext-install mysqli
COPY highscore.php index.php level2.php /var/www/html/
COPY images /var/www/html/images
EXPOSE 80