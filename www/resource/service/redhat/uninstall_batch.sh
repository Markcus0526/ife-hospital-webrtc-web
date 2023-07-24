#!/bin/bash

service teleclinic_batch stop

chkconfig teleclinic_batch off

rm -rf /etc/init.d/teleclinic_batch
rm -rf /usr/sbin/teleclinic_batch.ini
rm -rf /usr/sbin/teleclinic_batch.sh
