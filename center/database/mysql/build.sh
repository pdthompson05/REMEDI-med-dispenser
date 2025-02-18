# For WSL
# ssh -f -N -L 3306:138.197.45.197:3306 student3@it313communityprojects.website

# End execution if anything fails
set -e

export CMD='mysql -u section3 -p -h 127.0.0.1 -P 3306'

for file in drop.sql create.sql comment.sql ref.sql; do
    if [ -f "$file" ]; then
        echo "Executing $file..."
        $MYSQL_CMD < "$file"
        echo "$file executed successfully."
    else
        echo "Error: $file not found."
        exit 1
    fi
done

echo "Database build completed."