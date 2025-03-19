#!/bin/bash
# Deployment script for Joomla Extension Update
# Author Angus Fox
# Copyright (C) 2025 - Multizone Limited
# License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

# Check required environment variables are set
REQUIRED_VARS=("PLUGIN_NAME" "PLUGIN_ELEMENT" "PLUGIN_VERSION" "PLUGIN_DESCRIPTION" "PLUGIN_DIR" "OUTPUT_DIR" "UPDATE_SERVER" "SSH_USER" "SSH_HOST" "REMOTE_PATH")
for VAR in "${REQUIRED_VARS[@]}"; do
  if [ -z "${!VAR}" ]; then
    echo "Error: $VAR is not set."
    exit 1
  fi
done
# Remove .DS_Store files from current folder recursively because I use a Mac computer and Finder puts them in folders.
echo "Removing .DS_Store files from current folder recursively..."
find . -type f -name '*.DS_Store' -ls -delete

# Ensure output directory exists
mkdir -p "$OUTPUT_DIR"

# Define filenames
ZIP_FILE="${OUTPUT_DIR}/${PLUGIN_ELEMENT}-${PLUGIN_VERSION}.zip"
ZIP_FILE2="${OUTPUT_DIR}/${PLUGIN_ELEMENT}-latest.zip"

XML_FILE="${OUTPUT_DIR}/${PLUGIN_ELEMENT}.xml"

# Create zip archive
echo "Zipping plugin..."
cd  "$PLUGIN_DIR"
zip -r "$ZIP_FILE" *
cp "$ZIP_FILE" "$ZIP_FILE2"

# Generate hashes
SHA256=$(sha256sum "$ZIP_FILE" | awk '{print $1}')
SHA384=$(sha384sum "$ZIP_FILE" | awk '{print $1}')
SHA512=$(sha512sum "$ZIP_FILE" | awk '{print $1}')

# Generate update XML for your Joomla Update Server
echo "Generating update XML..."
cat > "$XML_FILE" <<EOL
<?xml version="1.0" encoding="utf-8"?>
<updates>
  <update>
    <name>${PLUGIN_NAME}</name>
    <element>${PLUGIN_ELEMENT}</element>
    <type>plugin</type>
    <folder>content</folder>
    <version>${PLUGIN_VERSION}</version>
    <description>${PLUGIN_DESCRIPTION}</description>
    <client>site</client>
    <sha256>${SHA256}</sha256>
    <sha384>${SHA384}</sha384>
    <sha512>${SHA512}</sha512>
    <downloads>
      <downloadurl type="full">${UPDATE_SERVER}/${PLUGIN_ELEMENT}-${PLUGIN_VERSION}.zip</downloadurl>
    </downloads>
    <infourl>${UPDATE_SERVER}/${PLUGIN_ELEMENT}-readme.html</infourl>
    <targetplatform name="joomla" version="((4\.4)|(5\.(0|1|2|3|4|5|6|7|8|9)))"/>
    <tags>
      <tag>stable</tag>
    </tags>
  </update>
</updates>
EOL

# Upload files to update server
echo "Uploading files to ${SSH_HOST}..."
scp "$ZIP_FILE" "$ZIP_FILE2" "$XML_FILE" "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/"

echo "Deployment complete!"
