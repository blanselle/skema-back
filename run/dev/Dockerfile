# versions definition
ARG PHP_VERSION=8.1
ARG NGINX_VERSION=1.21
ARG POSTGRES_VERSION=12

# "db" stage
FROM postgres:${POSTGRES_VERSION}-alpine AS app_db
RUN apk add tzdata
RUN cp /usr/share/zoneinfo/Europe/Paris  /etc/localtime
RUN echo "Europe/Paris" >  /etc/timezone


# "php" stage
FROM registry.pictime-groupe-integ.com/direction-technique/picv4-2/docker-base/php81-centos7:1.0.1 as app_php

USER root
# Surcharge configuration php et php fpm
COPY php/etc/* /etc/
COPY php-fpm.d/www.conf /etc/php-fpm.d/www.conf

COPY php-fpm.d/xdebug.ini /etc/php.d/15-xdebug.ini

ENV XDEBUG_MODE=coverage

# paramétrage de NGINX
COPY nginx/install.sh /tmp/install.sh
RUN chmod -R 755 /tmp/install.sh && /tmp/install.sh && rm /tmp/install.sh
COPY nginx /etc/nginx

# Installation Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:~/.config/composer/vendor/bin"

# Installation dépendance
COPY startup-install/install.sh /tmp/install.sh
RUN chmod -R 755 /tmp/install.sh && /tmp/install.sh && rm /tmp/install.sh

COPY configure_ownership.sh /usr/bin
COPY configure_templated_files.sh /usr/bin
RUN chmod -R 755 /usr/bin/configure_ownership.sh && chmod -R 755 /usr/bin/configure_templated_files.sh
#Configuration Chaperone
COPY chaperone_processes.conf /etc/chaperone.d/processes.conf

# PHP-CS-FIXER
RUN wget https://cs.symfony.com/download/php-cs-fixer-v3.phar -O /usr/local/bin/php-cs-fixer \
    && chmod a+x /usr/local/bin/php-cs-fixer

WORKDIR /srv/skema

COPY ./php/scripts/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# supervisor
COPY ./php/etc/supervisor.d/rabbitmq.ini /etc/supervisor.d/rabbitmq.ini
COPY ./php/etc/supervisord.conf /etc/supervisord.conf

#crontab
# desactivate in local env
#RUN yum -y install crontabs
#RUN sed -i -e '/pam_loginuid.so/s/^/#/' /etc/pam.d/crond
#ADD ./php/crontab/crontab.txt /etc/cron.d/skema_cron
#RUN chmod 0644 /etc/cron.d/skema_cron
#RUN crontab /etc/cron.d/skema_cron
#CMD crond

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]


