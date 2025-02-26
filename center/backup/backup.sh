# Cronjob created to run this weekly on sunday at 2am
# crontab -e

ENV_PATH="../.env"
# Load environment variables
export $(grep -v '^#' $ENV_PATH | xargs)

mkdir -p .
BACKUP_FILE="./db_remidi_backup_$(date +%F).sql"

# Run mysqldump using credentials from .env
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"

if [[ -f "$BACKUP_FILE" ]]; then
    gzip "$BACKUP_FILE"
else
    echo "Error: Backup failed."
    exit 1
fi