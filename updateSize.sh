#!/bin/env bash
while [ true ]; do
	sleep 20
	
	# Run the script
	cd /var/www/aw0/Storio && php storio.cron.php
done