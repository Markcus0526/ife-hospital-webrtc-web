#!/bin/bash

CURDIR=`dirname ${0}`
if [ "${1}" = "1" ]
then
	INI="${CURDIR}/scs.ini"
	ROOT_PATH=`sed -n '1,1p' ${INI}`

	cd $ROOT_PATH
	while [ true ]
	do
		ym=`date +%Y%m`
		node server.js >> "/var/log/scs-${ym}.log"
		sleep 2
	done
else
	"${CURDIR}/scs.sh" 1 &
fi
exit 0
