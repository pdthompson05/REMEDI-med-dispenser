# Cronjob created to run this weekly on sunday at 2am
# crontab -e
ENV_PATH="/var/www/it313communityprojects.website/section-three/center/.env"
export $(grep -v '^#' $ENV_PATH | xargs)
cd /var/www/it313communityprojects.website/section-three/center/backup/ || exit 1
BACKUP_FILE="./db_remidi_backup_$(date +%F).sql"
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"
# zip backup
if [[ -f "$BACKUP_FILE" ]]; then
    gzip "$BACKUP_FILE"
else
    echo "Error: Backup failed."
    exit 1
fi