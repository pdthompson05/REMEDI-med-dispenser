# Cronjob created to run every 3 hours
# crontab -e
cd /var/www/it313communityprojects.website/section-three/ || exit 1
git fetch origin main

if ! git diff --quiet HEAD origin/main; then
    echo "Changes detected. Pulling updates..."
    git reset --hard origin/main
    git clean -fd -e center/php/.env -e center/backup/
    git pull origin main --force
else
    echo "No changes detected. Skipping pull."
fi