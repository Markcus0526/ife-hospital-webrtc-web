#!/bin/bash

service recording stop

chkconfig recording off

rm -rf /etc/init.d/recording
rm -rf /usr/sbin/recording.ini
rm -rf /usr/sbin/recording.sh
