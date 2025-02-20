# Cronjob created to run this weekly on sunday at 2am
# crontab -e
# Load environment variables from .env
export $(grep -v '^#' /var/www/it313communityprojects.website/section-three/.env | xargs)

# Run mysqldump using credentials from .env
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "/var/www/it313communityprojects/section-three/center/database/backup/db_remidi_backup_$(date +%F).sql"

# Compress file
gzip "/var/www/it313communityprojects/section-three/center/database/backup/db_remidi_backup_$(date +%F).sql"