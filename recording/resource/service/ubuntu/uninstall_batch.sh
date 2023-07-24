#!/bin/bash

service recording stop

update-rc.d -f recording remove

rm -rf /etc/init.d/recording
rm -rf /usr/sbin/recording.ini
rm -rf /usr/sbin/recording.sh
