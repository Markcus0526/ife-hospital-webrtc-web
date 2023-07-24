#!/bin/bash

source ~/.bashrc
CURDIR=`dirname ${0}`
if [ "${1}" = "1" ]
then
	INI="${CURDIR}/recording.ini"
	ROOT_PATH=`sed -n '1,1p' ${INI}`

	cd $ROOT_PATH
	while [ true ]
	do
		ym=`date +%Y%m`
		php batch.php >> "/var/log/recording-${ym}.log"
		sleep 2
	done
else
	"${CURDIR}/recording.sh" 1 &
fi
exit 0
