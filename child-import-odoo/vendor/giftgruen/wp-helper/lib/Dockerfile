FROM alpine:edge

MAINTAINER Lukas Breuer <lukas.breuer@outlook.com>

RUN apk add --update \
    wget lftp php5 php5-openssl php5-json php5-phar php5-zlib zlib-dev ruby ruby-dev

RUN apk add alpine-sdk
RUN gem install --verbose --no-ri --no-rdoc github-markdown github-markup redcarpet
RUN apk del alpine-sdk

RUN wget --no-check-certificate 'https://getcomposer.org/installer' -O composer-setup.php
RUN php composer-setup.php -- --install-dir=/usr/bin
RUN php -r "unlink('composer-setup.php');"
RUN mv /usr/bin/composer.phar /usr/bin/composer
RUN chmod +x /usr/bin/composer

RUN wget http://apigen.org/apigen.phar
RUN mv apigen.phar /usr/bin/apigen
RUN chmod +x /usr/bin/apigen

RUN rm -rf /var/cache/apk/*
