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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }}
        .header {{
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }}
        .header h1 {{
            margin: 0 0 10px 0;
            font-size: 2.5em;
        }}
        .header p {{
            margin: 0;
            opacity: 0.9;
            font-size: 1.1em;
        }}
        .section {{
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }}
        .section h2 {{
            margin-top: 0;
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }}
        .version {{
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 4px;
        }}
        .version h3 {{
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.3em;
        }}
        .version-badge {{
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            margin-right: 10px;
        }}
        .type-badge {{
            display: inline-block;
            background: #764ba2;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: normal;
        }}
        .version ul {{
            margin: 15px 0 0 0;
            padding-left: 25px;
        }}
        .version li {{
            margin-bottom: 8px;
            line-height: 1.5;
        }}
        .footer {{
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #666;
            font-size: 0.9em;
        }}
        .footer a {{
            color: #667eea;
            text-decoration: none;
        }}
        .footer a:hover {{
            text-decoration: underline;
        }}
        @media (max-width: 600px) {{
            body {{
                padding: 10px;
            }}
            .header {{
                padding: 20px;
            }}
            .header h1 {{
                font-size: 1.8em;
            }}
            .section {{
                padding: 15px;
            }}
        }}
    </style>
</head>
<body>
    <div class="header">
        <h1>🚀 AutoMeta</h1>
        <p>Automatic Meta Description for Joomla - Release History</p>
    </div>
"""

    # Plugin changelog section
    if plugin_entries:
        html_content += """
    <div class="section">
        <h2>Plugin Changelog</h2>
"""
        for entry in plugin_entries:
            html_content += f"""
        <div class="version">
            <h3>
                <span class="version-badge">v{entry['version']}</span>
                <span class="type-badge">{entry['type']}</span>
            </h3>
"""
            if entry['items']:
                html_content += "            <ul>\n"
                for item in entry['items']:
                    html_content += f"                <li>{item}</li>\n"
                html_content += "            </ul>\n"

            html_content += "        </div>\n"

        html_content += "    </div>\n"

    # Component changelog section
    if component_entries:
        html_content += """
    <div class="section">
        <h2>Component Changelog</h2>
"""
        for entry in component_entries:
            html_content += f"""
        <div class="version">
            <h3>
                <span class="version-badge">v{entry['version']}</span>
                <span class="type-badge">{entry['type']}</span>
            </h3>
"""
            if entry['items']:
                html_content += "            <ul>\n"
                for item in entry['items']:
                    html_content += f"                <li>{item}</li>\n"
                html_content += "            </ul>\n"

            html_content += "        </div>\n"

        html_content += "    </div>\n"

    # Footer
    current_year = datetime.now().year
    html_content += f"""
    <div class="footer">
        <p>
            <strong>AutoMeta</strong> - Automatic Meta Description for Joomla<br>
            Copyright &copy; {current_year} <a href="https://www.multizone.co.uk" target="_blank">Multizone Limited</a><br>
            Licensed under <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPLv3</a>
        </p>
        <p>
            <a href="https://www.multizone.co.uk/extensions/joomla-content-plugin-automatic-meta-description-ezone.html" target="_blank">Documentation & Support</a>
        </p>
        <p style="margin-top: 10px; font-size: 0.85em; color: #999;">
            Generated on {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
        </p>
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
