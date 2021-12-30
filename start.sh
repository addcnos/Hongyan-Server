#/bin/bash
php -v
php -m
cd /www/
cp ./.env.develop ./.env
php artisan gateway-worker:serve --register --register-bind=0.0.0.0:1215 &
php artisan gateway-worker:serve --gateway --register-address=127.0.0.1:1215 --gateway-bind=0.0.0.0:1216 --lan-ip=127.0.0.1 &
php artisan gateway-worker:serve --businessworker --register-address=127.0.0.1:1215 &
php ./bin/laravels start