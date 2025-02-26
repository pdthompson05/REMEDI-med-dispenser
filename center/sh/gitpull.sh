cd /../.. || exit 1
git checkout main
git fetch origin
git reset --hard origin/main
git clean -fd -e center/.env -e center/backup/
git pull origin main --force