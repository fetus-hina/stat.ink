#!/bin/sh

set -eu

# /run may be a fresh tmpfs, so create the runtime directories on every start
install -d -o postgres -g postgres -m 2775 /run/postgresql
install -d -m 0755 /run/h2o /run/php-fpm

exec /usr/bin/supervisord --nodaemon --configuration /etc/supervisord.conf
