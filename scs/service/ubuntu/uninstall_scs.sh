#!/bin/bash

service scs stop

update-rc.d -f scs remove

rm -rf /etc/init.d/scs
rm -rf /usr/sbin/scs.ini
rm -rf /usr/sbin/scs.sh
