#!/bin/bash

folder=`dirname ${0}`

cp -f "${folder}/recording" /etc/init.d
cp -f "${folder}/batch.ini" /usr/sbin/recording.ini
cp -f "${folder}/recording.sh" /usr/sbin/recording.sh

chkconfig recording on

service recording condrestart
