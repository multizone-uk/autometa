#!/bin/bash
# Generic script to merge a feature branch to main
# Usage: ./merge-to-main.sh <branch-name> [commit-message]
# Run this script from your local machine

set -e

# Check if branch name is provided
if [ -z "$1" ]; then
    echo "Error: Branch name is required"
    echo ""
    echo "Usage: $0 <branch-name> [commit-message]"
    echo ""
    echo "Examples:"
    echo "  $0 claude/add-changelog-build-upload-011CUzJ9QdBJXHB4o2DdjSQc"
    echo "  $0 claude/add-changelog-build-upload-011CUzJ9QdBJXHB4o2DdjSQc 'Add changelog and build upload'"
    exit 1
fi

BRANCH_NAME="$1"
COMMIT_MESSAGE="${2:-Merge $BRANCH_NAME}"

echo "Branch to merge: $BRANCH_NAME"
echo "Commit message: $COMMIT_MESSAGE"
echo ""

echo "Fetching latest from origin..."
git fetch origin

echo "Checking out main..."
git checkout main

echo "Pulling latest main..."
git pull origin main

echo "Merging $BRANCH_NAME..."
git merge "$BRANCH_NAME" -m "$COMMIT_MESSAGE"

echo "Pushing to origin/main..."
git push origin main

echo ""
echo "✓ Merge complete!"
echo ""
echo "You can now build from main:"
echo "  bash build.sh"
