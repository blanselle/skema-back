#!/bin/bash -e
groupmod -g ${GROUP_ID} ftpgrp
usermod -u ${USER_ID} -s /bin/bash web
