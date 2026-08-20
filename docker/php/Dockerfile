FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    ssmtp \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install pdo pdo_mysql mysqli gd

RUN apt-get update && apt-get install -y ssmtp

RUN echo "sendmail_path=/usr/sbin/ssmtp -t" >> /usr/local/etc/php/php.ini
RUN echo "display_errors = Off" >> /usr/local/etc/php/php.ini
RUN echo "log_errors = On" >> /usr/local/etc/php/php.ini
RUN echo "error_log = /var/log/php_errors.log" >> /usr/local/etc/php/php.ini


# expose to the browser /var/www/html/public instead of /var/www/html
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf
#s => substitute
#1st !___! => text to find
#2nd !___! => text to replace with
#g => global 

#Enable apache module mod_rewrite which handles the app routingn system
RUN a2enmod rewrite

COPY ssmtp.conf /etc/ssmtp/ssmtp.conf

WORKDIR /var/www/html

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
CMD ["/entrypoint.sh"]