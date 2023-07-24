#!/bin/bash

folder=`dirname ${0}`

cp -f "${folder}/scs" /etc/init.d
cp -f "${folder}/scs.ini" /usr/sbin
cp -f "${folder}/scs.sh" /usr/sbin

chkconfig scs on

service transcard condrestart
