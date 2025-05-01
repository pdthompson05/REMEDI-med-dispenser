#!/bin/bash

# Store the current branch name
current_branch=$(git branch --show-current)

# Ensure we're on main branch
if [ "$current_branch" != "main" ]; then
    echo "Switching to main branch..."
    git checkout main
fi

# Fetch all changes from remote
echo "Fetching changes from remote..."
git fetch origin main

# Force reset to origin/main
echo "Force resetting to origin/main..."
git reset --hard origin/main

echo "Successfully synchronized with remote main branch!"
