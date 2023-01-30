#!/bin/bash

echo "Start Startup Install Shell";
yum-config-manager --setopt=sslverify=false --save
yum install -y coreutils libtool file gettext git 
yum install -y php-exif php-zip php-apcu php-bcmath php-curl php-intl php-imagick php-opcache php-mbstring php-memcached php-pgsql php-xdebug 
yum install -y rabbitmq-c-dev amqp php-amqp supervisor runuser
yum-config-manager --setopt=sslverify=true --save
cd /tmp
wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox-0.12.6-1.centos7.x86_64.rpm
yum install -y wkhtmltox-0.12.6-1.centos7.x86_64.rpm
echo "End Startup Install Shell";