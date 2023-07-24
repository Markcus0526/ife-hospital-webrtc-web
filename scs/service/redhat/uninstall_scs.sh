#!/bin/bash

service scs stop

chkconfig scs off

rm -rf /etc/init.d/scs
rm -rf /usr/sbin/scs.ini
rm -rf /usr/sbin/scs.sh
