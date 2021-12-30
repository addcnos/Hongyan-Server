FROM phpswoole/swoole:php7.3-alpine

RUN docker-php-ext-install pcntl

COPY ./ /www

RUN cd /www && composer install

WORKDIR /www

EXPOSE 80 443 22 1215 1216

CMD ["sh", "/www/start.sh"]
