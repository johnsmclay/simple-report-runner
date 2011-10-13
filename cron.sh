#!/bin/bash

WWW_ROOT=/srv/mil-bi/www/
INDEX_FILE=index.php
CRON_CONTROLLER=cronjobs
REPORTS_DIR=report_holder

php $WWW_ROOT$INDEX_FILE $CRON_CONTROLLER processscheduledreports

chown -R www-data:www-data $WWW_ROOT$REPORTS_DIR
echo $WWW_ROOT$REPORTS_DIR
echo 'DONE!'
