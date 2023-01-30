#!/usr/bin/env bash

runuser -u web -- php bin/console d:m:m -n
runuser -u web -- php bin/console lexik:jwt:generate-keypair --overwrite
runuser -u web -- composer install
runuser -u web -- php bin/console ca:cl -n
runuser -u web -- php bin/console assets:install public
runuser -u web -- php bin/console  messenger:setup-transports

/usr/bin/supervisord -c /etc/supervisord.conf

/usr/local/bin/chaperone