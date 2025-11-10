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
PLUGIN_MANIFEST="${PLUGIN_DIR}/autometa.xml"
if [ ! -f "$PLUGIN_MANIFEST" ]; then
    echo -e "${RED}Error: Plugin manifest not found: $PLUGIN_MANIFEST${NC}"
    exit 1
fi

VERSION=$(grep '<version>' "$PLUGIN_MANIFEST" | sed 's/.*<version>\(.*\)<\/version>.*/\1/' | head -n1)
PLUGIN_NAME=$(grep '<name>' "$PLUGIN_MANIFEST" | sed 's/.*<name>\(.*\)<\/name>.*/\1/' | head -n1)

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

# Build component
echo "Building component..."
COMPONENT_MANIFEST="${COMPONENT_DIR}/manifest.xml"
if [ -f "$COMPONENT_MANIFEST" ]; then
    COMPONENT_VERSION=$(grep '<version>' "$COMPONENT_MANIFEST" | sed 's/.*<version>\(.*\)<\/version>.*/\1/' | head -n1)
    COMPONENT_ZIP="${OUTPUT_DIR}/com_autometa-${COMPONENT_VERSION}.zip"
    cd "$COMPONENT_DIR"
    zip -r "../${COMPONENT_ZIP}" * -x "*.DS_Store"
    cd ..

    # Create latest symlink/copy
    cp "$COMPONENT_ZIP" "${OUTPUT_DIR}/com_autometa-latest.zip"

    echo -e "${GREEN}✓ Component built: ${COMPONENT_ZIP}${NC}"
else
    echo -e "${YELLOW}⚠ Component manifest not found, skipping component build${NC}"
fi

# Generate plugin hashes
echo "Generating plugin checksums..."
if command -v shasum &> /dev/null; then
    PLUGIN_SHA256=$(shasum -a 256 "$PLUGIN_ZIP" | awk '{print $1}')
    PLUGIN_SHA384=$(shasum -a 384 "$PLUGIN_ZIP" | awk '{print $1}')
    PLUGIN_SHA512=$(shasum -a 512 "$PLUGIN_ZIP" | awk '{print $1}')
else
    PLUGIN_SHA256=$(sha256sum "$PLUGIN_ZIP" | awk '{print $1}')
    PLUGIN_SHA384=$(sha384sum "$PLUGIN_ZIP" | awk '{print $1}')
    PLUGIN_SHA512=$(sha512sum "$PLUGIN_ZIP" | awk '{print $1}')
fi

# Generate plugin update XML
PLUGIN_UPDATE_XML="${OUTPUT_DIR}/autometa.xml"
cat > "$PLUGIN_UPDATE_XML" <<EOF
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
    <sha256>${PLUGIN_SHA256}</sha256>
    <sha384>${PLUGIN_SHA384}</sha384>
    <sha512>${PLUGIN_SHA512}</sha512>
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

echo -e "${GREEN}✓ Plugin update XML generated: ${PLUGIN_UPDATE_XML}${NC}"

# Copy plugin changelog
PLUGIN_CHANGELOG="${OUTPUT_DIR}/autometa-changelog.xml"
if [ -f "${PLUGIN_DIR}/changelog.xml" ]; then
    cp "${PLUGIN_DIR}/changelog.xml" "$PLUGIN_CHANGELOG"
    echo -e "${GREEN}✓ Plugin changelog copied: ${PLUGIN_CHANGELOG}${NC}"
fi

# Generate HTML changelog/readme
echo "Generating HTML changelog..."
HTML_README="${OUTPUT_DIR}/autometa-readme.html"
if command -v python3 &> /dev/null; then
    if python3 generate_changelog_html.py; then
        echo -e "${GREEN}✓ HTML changelog generated: ${HTML_README}${NC}"
    else
        echo -e "${YELLOW}⚠ Failed to generate HTML changelog${NC}"
    fi
else
    echo -e "${YELLOW}⚠ Python3 not found, skipping HTML changelog generation${NC}"
fi

# Generate component hashes and update XML if component was built
if [ -f "$COMPONENT_ZIP" ]; then
    echo "Generating component checksums..."
    if command -v shasum &> /dev/null; then
        COMPONENT_SHA256=$(shasum -a 256 "$COMPONENT_ZIP" | awk '{print $1}')
        COMPONENT_SHA384=$(shasum -a 384 "$COMPONENT_ZIP" | awk '{print $1}')
        COMPONENT_SHA512=$(shasum -a 512 "$COMPONENT_ZIP" | awk '{print $1}')
    else
        COMPONENT_SHA256=$(sha256sum "$COMPONENT_ZIP" | awk '{print $1}')
        COMPONENT_SHA384=$(sha384sum "$COMPONENT_ZIP" | awk '{print $1}')
        COMPONENT_SHA512=$(sha512sum "$COMPONENT_ZIP" | awk '{print $1}')
    fi

    COMPONENT_NAME=$(grep '<name>' "$COMPONENT_MANIFEST" | sed 's/.*<name>\(.*\)<\/name>.*/\1/' | head -n1)
    COMPONENT_UPDATE_XML="${OUTPUT_DIR}/com_autometa.xml"

    cat > "$COMPONENT_UPDATE_XML" <<EOF
<?xml version="1.0" encoding="utf-8"?>
<updates>
  <update>
    <name>${COMPONENT_NAME}</name>
    <element>com_autometa</element>
    <type>component</type>
    <version>${COMPONENT_VERSION}</version>
    <description>Automatically generates meta descriptions for Joomla articles.</description>
    <client>administrator</client>
    <sha256>${COMPONENT_SHA256}</sha256>
    <sha384>${COMPONENT_SHA384}</sha384>
    <sha512>${COMPONENT_SHA512}</sha512>
    <downloads>
      <downloadurl type="full">${UPDATE_SERVER}/com_autometa-${COMPONENT_VERSION}.zip</downloadurl>
    </downloads>
    <infourl>${UPDATE_SERVER}/com_autometa-readme.html</infourl>
    <targetplatform name="joomla" version="((4\.4)|(5\.(0|1|2|3|4|5|6|7|8|9)))"/>
    <tags>
      <tag>stable</tag>
    </tags>
  </update>
</updates>
EOF

    echo -e "${GREEN}✓ Component update XML generated: ${COMPONENT_UPDATE_XML}${NC}"

    # Copy component changelog
    COMPONENT_CHANGELOG="${OUTPUT_DIR}/com_autometa-changelog.xml"
    if [ -f "${COMPONENT_DIR}/changelog.xml" ]; then
        cp "${COMPONENT_DIR}/changelog.xml" "$COMPONENT_CHANGELOG"
        echo -e "${GREEN}✓ Component changelog copied: ${COMPONENT_CHANGELOG}${NC}"
    fi
fi

# Optional upload
if [ -n "$SSH_USER" ] && [ -n "$SSH_HOST" ] && [ -n "$REMOTE_PATH" ]; then
    echo -e "${YELLOW}Uploading to ${SSH_HOST}...${NC}"

    # Build upload file list
    UPLOAD_FILES="$PLUGIN_ZIP ${OUTPUT_DIR}/autometa-latest.zip $PLUGIN_UPDATE_XML"

    # Add plugin changelog if it exists
    if [ -f "$PLUGIN_CHANGELOG" ]; then
        UPLOAD_FILES="$UPLOAD_FILES $PLUGIN_CHANGELOG"
    fi

    # Add HTML readme/changelog if it exists
    if [ -f "$HTML_README" ]; then
        UPLOAD_FILES="$UPLOAD_FILES $HTML_README"
    fi

    # Add component files if they exist
    if [ -f "$COMPONENT_ZIP" ]; then
        UPLOAD_FILES="$UPLOAD_FILES $COMPONENT_ZIP ${OUTPUT_DIR}/com_autometa-latest.zip $COMPONENT_UPDATE_XML"

        # Add component changelog if it exists
        if [ -f "$COMPONENT_CHANGELOG" ]; then
            UPLOAD_FILES="$UPLOAD_FILES $COMPONENT_CHANGELOG"
        fi
    fi

    scp $UPLOAD_FILES "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/"
    echo -e "${GREEN}✓ Upload complete${NC}"
else
    echo -e "${YELLOW}⚠ Skipping upload (SSH_USER, SSH_HOST, or REMOTE_PATH not set)${NC}"
fi

echo ""
echo -e "${GREEN}Build complete!${NC}"
echo "Files in ${OUTPUT_DIR}:"
ls -lh "$OUTPUT_DIR"
