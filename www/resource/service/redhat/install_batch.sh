#!/bin/bash

folder=`dirname ${0}`

cp -f "${folder}/teleclinic_batch" /etc/init.d
cp -f "${folder}/batch.ini" /usr/sbin/teleclinic_batch.ini
cp -f "${folder}/teleclinic_batch.sh" /usr/sbin/teleclinic_batch.sh

chkconfig teleclinic_batch on

service teleclinic_batch condrestart
