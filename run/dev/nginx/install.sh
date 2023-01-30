#!/bin/bash

echo "Start Startup Install Nginx Shell";
# Install NGINX 
yum clean all
yum-config-manager --setopt=sslverify=false --save
yum install -y nginx
yum-config-manager --setopt=sslverify=true --save

yum clean all

## allow template and www-data to substitute var and docker user/bind-mount to add file
chmod 755 /etc/nginx
mkdir -p /etc/nginx/include/rewrite/
chmod 755 /etc/nginx/include/rewrite/
chmod 777 /etc/nginx/nginx-log.conf /etc/nginx/nginx-maps_size.conf /etc/nginx/nginx-x_req_id.conf

# To output on stdout accesslog we need to create link to /dev/stdout
# and configure nginx accesslog to output this link file.
# Nginx only support output of error to /dev/stderr when error_log set to "stderr"
mkdir -p /var/log/nginx/
chmod 777 /var/log/nginx/
echo ",777,/var/log/nginx/">>${FILES_TO_CHOWN_AT_RUNTIME}

mkfifo /var/log/nginx/access.log
chmod 666 /var/log/nginx/access.log

mkdir -p /var/lib/nginx/tmp
chmod 777 /var/lib/nginx
chmod 777 /var/lib/nginx/tmp

echo "R,,/var/www/html/">>${FILES_TO_CHOWN_AT_RUNTIME}

touch /run/nginx.pid
chmod 777 /run/nginx.pid

echo "End Startup Install Nginx Shell";
