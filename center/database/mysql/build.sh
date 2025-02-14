# Run this command to create a login path for storing the password:
# mysql_config_editor set --login-path=section3 -u section3 -p

export CMD='mysql --login-path=section3 --local-infile -s -h 127.0.0.1 -P 3306 --ssl-mode=REQUIRED section3'

$CMD < drop.sql
echo Tables Dropped.

$CMD < create.sql
echo Tables Created.

$CMD < comment.sql
echo Comments Added.

$CMD < ref.sql
echo References Added.

# $CMD < views.sql
# echo Views Created.