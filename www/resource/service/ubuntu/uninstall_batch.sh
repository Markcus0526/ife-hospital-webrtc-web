#!/bin/bash

service teleclinic_batch stop

update-rc.d -f teleclinic_batch remove

rm -rf /etc/init.d/teleclinic_batch
rm -rf /usr/sbin/teleclinic_batch.ini
rm -rf /usr/sbin/teleclinic_batch.sh
