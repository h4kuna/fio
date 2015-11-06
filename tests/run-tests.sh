#!/bin/bash

DIR=`dirname $0`;
TEMP_DIR=$DIR/temp


rm -rf $TEMP_DIR/*

cd $DIR/..
composer install --no-interaction --prefer-source

$DIR/../vendor/bin/tester -p php $DIR -s -j 5 --colors 1 -c $DIR/data/php_unix.ini
