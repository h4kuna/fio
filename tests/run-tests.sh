#!/bin/bash

DIR=`dirname $0`;

rm -rf temp/*

$DIR/../vendor/bin/tester -p php $DIR -s -j 2 --colors 1 -c $DIR/data/php_unix.ini