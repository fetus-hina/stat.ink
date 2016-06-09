#!/bin/bash

set -eu

BASEDIR=$(dirname $0)
cd "$BASEDIR"

./composer.phar update -vvv
make vendor-archive
