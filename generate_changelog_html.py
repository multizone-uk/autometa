#!/usr/bin/env python3
"""
Generate HTML changelog from XML changelog files
Copyright (C) 2025 - Multizone Limited
License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
"""

import xml.etree.ElementTree as ET
import sys
from datetime import datetime
from pathlib import Path


def parse_changelog(xml_file):
    """Parse a changelog XML file and return entries."""
    try:
        tree = ET.parse(xml_file)
        root = tree.getroot()
        entries = []

        for changelog in root.findall('changelog'):
            version = changelog.find('version').text if changelog.find('version') is not None else 'Unknown'
            element = changelog.find('element').text if changelog.find('element') is not None else ''
            type_elem = changelog.find('type').text if changelog.find('type') is not None else ''

            items = []
            note = changelog.find('note')
            if note is not None:
                for item in note.findall('item'):
                    if item.text:
                        items.append(item.text.strip())

            entries.append({
                'version': version,
                'element': element,
                'type': type_elem,
                'items': items
            })

        return entries
    except Exception as e:
        print(f"Error parsing {xml_file}: {e}", file=sys.stderr)
        return []


def generate_html(plugin_entries, component_entries, output_file):
    """Generate HTML changelog from parsed entries."""

    html_content = f"""<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoMeta - Changelog</title>
    <style>
        body {{
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            color: #333;
        }}
        h1 {{
            color: #000;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }}
        h2 {{
            color: #000;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }}
        h3 {{
            color: #333;
            margin-top: 20px;
            margin-bottom: 10px;
        }}
        ul {{
            margin: 10px 0;
            padding-left: 25px;
        }}
        li {{
            margin-bottom: 5px;
        }}
        .footer {{
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }}
        .footer a {{
            color: #0066cc;
            text-decoration: none;
        }}
        .footer a:hover {{
            text-decoration: underline;
        }}
    </style>
</head>
<body>
    <h1>AutoMeta - Changelog</h1>
    <p>Automatic Meta Description for Joomla - Release History</p>
"""

    # Plugin changelog section
    if plugin_entries:
        html_content += """
    <h2>Plugin Changelog</h2>
"""
        for entry in plugin_entries:
            html_content += f"""
    <h3>Version {entry['version']}</h3>
"""
            if entry['items']:
                html_content += "    <ul>\n"
                for item in entry['items']:
                    html_content += f"        <li>{item}</li>\n"
                html_content += "    </ul>\n"

    # Component changelog section
    if component_entries:
        html_content += """
    <h2>Component Changelog</h2>
"""
        for entry in component_entries:
            html_content += f"""
    <h3>Version {entry['version']}</h3>
"""
            if entry['items']:
                html_content += "    <ul>\n"
                for item in entry['items']:
                    html_content += f"        <li>{item}</li>\n"
                html_content += "    </ul>\n"

    # Footer
    current_year = datetime.now().year
    html_content += f"""
    <div class="footer">
        <p><strong>AutoMeta</strong> - Automatic Meta Description for Joomla</p>
        <p>Copyright &copy; {current_year} <a href="https://www.multizone.co.uk" target="_blank">Multizone Limited</a> |
        Licensed under <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a></p>
        <p><a href="https://www.multizone.co.uk/extensions/joomla-content-plugin-automatic-meta-description-ezone.html" target="_blank">Documentation & Support</a></p>
    </div>
</body>
</html>
"""

    # Write to file
    try:
        Path(output_file).write_text(html_content, encoding='utf-8')
        print(f"✓ HTML changelog generated: {output_file}")
        return True
    except Exception as e:
        print(f"Error writing HTML file: {e}", file=sys.stderr)
        return False


def main():
    # Define paths
    plugin_changelog = Path('plg_content_autometa/changelog.xml')
    component_changelog = Path('com_content_autometa/changelog.xml')
    output_file = Path('dist/autometa-readme.html')

    # Parse changelogs
    plugin_entries = []
    component_entries = []

    if plugin_changelog.exists():
        plugin_entries = parse_changelog(plugin_changelog)
        print(f"✓ Parsed {len(plugin_entries)} plugin changelog entries")
    else:
        print(f"⚠ Plugin changelog not found: {plugin_changelog}", file=sys.stderr)

    if component_changelog.exists():
        component_entries = parse_changelog(component_changelog)
        print(f"✓ Parsed {len(component_entries)} component changelog entries")
    else:
        print(f"⚠ Component changelog not found: {component_changelog}", file=sys.stderr)

    if not plugin_entries and not component_entries:
        print("Error: No changelog entries found", file=sys.stderr)
        return 1

    # Ensure output directory exists
    output_file.parent.mkdir(parents=True, exist_ok=True)

    # Generate HTML
    if generate_html(plugin_entries, component_entries, output_file):
        return 0
    else:
        return 1


if __name__ == '__main__':
    sys.exit(main())
