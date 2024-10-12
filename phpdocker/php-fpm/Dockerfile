FROM phpdockerio/php:8.0-fpm
WORKDIR "/application"

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        git \
        php8.0-mcrypt \
        php8.0-mysql \
        php8.0-gd \
        php8.0-xdebug; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN echo "zend_extension=$(find /usr/lib/php/20200930/ -name xdebug.so)" > /etc/php/8.0/mods-available/xdebug.ini \
    && echo "xdebug.mode=debug" >> /etc/php/8.0/mods-available/xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /etc/php/8.0/mods-available/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /etc/php/8.0/mods-available/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /etc/php/8.0/mods-available/xdebug.ini \
    && echo "xdebug.idekey=docker" >> /etc/php/8.0/mods-available/xdebug.ini \
    && echo "xdebug.log=/var/log/xdebug.log" >> /etc/php/8.0/mods-available/xdebug.ini
