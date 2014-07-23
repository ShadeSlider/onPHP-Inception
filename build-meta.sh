#!/bin/sh

cd `pwd`

echo `pwd`

php ./externals/onphp-extended/meta/bin/build.php --create-tables --run-alter-table-queries --sql-log-file=db/sql/onphp_log_`date +%Y_%m_%d`.sql ./config/config.inc.php ./meta/meta.xml