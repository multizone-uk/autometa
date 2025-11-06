#!/bin/bash
# Build script for Joomla Extension
# Copyright (C) 2025 - Multizone Limited
# License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

set -e

# Configuration
PLUGIN_DIR="plg_content_autometa"
COMPONENT_DIR="com_content_autometa"
OUTPUT_DIR="dist"
UPDATE_SERVER="${UPDATE_SERVER:-https://www.multizone.co.uk/updates}"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Extract version from plugin manifest
PLUGIN_MANIFEST="${PLUGIN_DIR}/plg-autometa.xml"
if [ ! -f "$PLUGIN_MANIFEST" ]; then
    echo -e "${RED}Error: Plugin manifest not found: $PLUGIN_MANIFEST${NC}"
    exit 1
fi

VERSION=$(grep -oP '<version>\K[^<]+' "$PLUGIN_MANIFEST")
PLUGIN_NAME=$(grep -oP '<name>\K[^<]+' "$PLUGIN_MANIFEST")

echo -e "${GREEN}Building ${PLUGIN_NAME} v${VERSION}${NC}"

# Create output directory
mkdir -p "$OUTPUT_DIR"

# Clean .DS_Store files (macOS)
find . -type f -name '.DS_Store' -delete 2>/dev/null || true

# Build plugin
echo "Building plugin..."
PLUGIN_ZIP="${OUTPUT_DIR}/autometa-${VERSION}.zip"
cd "$PLUGIN_DIR"
zip -r "../${PLUGIN_ZIP}" * -x "*.DS_Store"
cd ..

# Create latest symlink/copy
cp "$PLUGIN_ZIP" "${OUTPUT_DIR}/autometa-latest.zip"

echo -e "${GREEN}✓ Plugin built: ${PLUGIN_ZIP}${NC}"

# Generate hashes
SHA256=$(sha256sum "$PLUGIN_ZIP" | awk '{print $1}')
SHA384=$(sha384sum "$PLUGIN_ZIP" | awk '{print $1}')
SHA512=$(sha512sum "$PLUGIN_ZIP" | awk '{print $1}')

# Generate update XML
UPDATE_XML="${OUTPUT_DIR}/autometa.xml"
cat > "$UPDATE_XML" <<EOF
<?xml version="1.0" encoding="utf-8"?>
<updates>
  <update>
    <name>${PLUGIN_NAME}</name>
    <element>autometa</element>
    <type>plugin</type>
    <folder>content</folder>
    <version>${VERSION}</version>
    <description>Automatically generates a meta description from the article title and lead content if it does not exist, when you save.</description>
    <client>site</client>
    <sha256>${SHA256}</sha256>
    <sha384>${SHA384}</sha384>
    <sha512>${SHA512}</sha512>
    <downloads>
      <downloadurl type="full">${UPDATE_SERVER}/autometa-${VERSION}.zip</downloadurl>
    </downloads>
    <infourl>${UPDATE_SERVER}/autometa-readme.html</infourl>
    <targetplatform name="joomla" version="((4\.4)|(5\.(0|1|2|3|4|5|6|7|8|9)))"/>
    <tags>
      <tag>stable</tag>
    </tags>
  </update>
</updates>
EOF

echo -e "${GREEN}✓ Update XML generated: ${UPDATE_XML}${NC}"

# Optional upload
if [ -n "$SSH_USER" ] && [ -n "$SSH_HOST" ] && [ -n "$REMOTE_PATH" ]; then
    echo -e "${YELLOW}Uploading to ${SSH_HOST}...${NC}"
    scp "$PLUGIN_ZIP" "${OUTPUT_DIR}/autometa-latest.zip" "$UPDATE_XML" \
        "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/"
    echo -e "${GREEN}✓ Upload complete${NC}"
else
    echo -e "${YELLOW}⚠ Skipping upload (SSH_USER, SSH_HOST, or REMOTE_PATH not set)${NC}"
fi

echo ""
echo -e "${GREEN}Build complete!${NC}"
echo "Files in ${OUTPUT_DIR}:"
ls -lh "$OUTPUT_DIR"
