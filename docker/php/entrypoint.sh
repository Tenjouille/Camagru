#!/bin/bash
chown -R www-data:www-data /var/www/html/public/uploads
chmod -R 775 /var/www/html/public/uploads
chown -R www-data:www-data /var/www/html/public/assets/stickers
chmod -R 775 /var/www/html/public/assets/stickers
exec apache2-foreground