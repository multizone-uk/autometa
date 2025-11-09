#!/bin/bash
# Merge plugin display error fix branch to main
# Run this script from your local machine

set -e

echo "Fetching latest from origin..."
git fetch origin

echo "Checking out main..."
git checkout main

echo "Pulling latest main..."
git pull origin main

echo "Merging plugin display error fix branch..."
git merge claude/fix-plugin-display-error-011CUxjRd5P1p88iMizwST6A -m "Merge plugin display error fix v1.2.3"

echo "Pushing to origin/main..."
git push origin main

echo ""
echo "✓ Merge complete!"
echo ""
echo "You can now build from main:"
echo "  bash build.sh"
