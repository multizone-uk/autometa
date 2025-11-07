#!/bin/bash
# Merge component modernization branch to main
# Run this script from your local machine

set -e

echo "Fetching latest from origin..."
git fetch origin

echo "Checking out main..."
git checkout main

echo "Pulling latest main..."
git pull origin main

echo "Merging component modernization branch..."
git merge claude/component-modernization-011CUqTHE4van52R8VYeFqqj -m "Merge component modernization v1.2.1"

echo "Pushing to origin/main..."
git push origin main

echo ""
echo "✓ Merge complete!"
echo ""
echo "You can now build from main:"
echo "  bash build.sh"
