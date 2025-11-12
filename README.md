
# AutoMeta Package - Automatic Meta Description

A complete Joomla extension package that automatically generates meta descriptions for articles. The package includes both a plugin for automatic generation on save and a component for bulk regeneration and management.

**Features:**
- **Plugin**: Automatically generates meta descriptions on article save
- **Component**: Bulk regeneration of meta descriptions for all articles
- **Analytics Dashboard** (Subscription Required): Track regeneration history and article statistics
- **Subscription Conversion Card**: Promote standard subscription tier with analytics access
- **Unified Package**: Single installation for both extensions
- Configurable character limit (default 160, optimal for SEO)
- Smart truncation at word boundaries
- Configurable separator between title and content
- Option to include/exclude title or content
- Option to overwrite existing descriptions
- Statistics dashboard showing meta description coverage
- Regeneration history tracking with user, timestamp, and success rates
- Article-level analytics showing regeneration counts and hit tracking
- Configurable data retention for analytics (default 365 days)
- Batch processing to handle large sites efficiently
- CSRF protection and permission checks
- Error handling and logging
- Automatic updates through Joomla update system

The build script generates a unified package that installs both the component and plugin from a single zip file using standard Joomla package mechanisms.




## License

[![GPLv3 License](https://img.shields.io/badge/License-GPL%20v3-yellow.svg)](https://opensource.org/licenses/)

[GNU/GPLv3](https://www.gnu.org/licenses/gpl-3.0.html)


## Documentation

[Documentation](https://www.multizone.co.uk/extensions/joomla-content-plugin-automatic-meta-description-ezone.html)


## Screenshots

![App Screenshot](https://www.multizone.co.uk/images/extensions/autometa-demo-publishing.png)


## Support

[Support](https://www.multizone.co.uk/extensions/joomla-content-plugin-automatic-meta-description-ezone.html)


## Demo

[Demo](https://www.multizone.co.uk/extensions/joomla-content-plugin-automatic-meta-description-ezone.html)

## Building

Build the complete package locally:

```bash
./build.sh
```

This creates distribution files in `dist/`:
- **Package** (recommended):
  - `pkg_autometa-{version}.zip` - Complete package with component and plugin
  - `pkg_autometa-latest.zip` - Latest package version
  - `pkg_autometa.xml` - Package update server XML with hashes
  - `pkg_autometa-changelog.xml` - Package changelog
  - `pkg_autometa-readme.html` - Package changelog in HTML format
- **Individual Extensions** (also built):
  - `autometa-{version}.zip` - Plugin only
  - `com_autometa-{version}.zip` - Component only
  - Update XMLs and changelogs for each

Version is automatically read from `plg_content_autometa/autometa.xml` and synchronized across all extensions.

### Optional: Upload to Update Server

Set environment variables to enable automatic upload:

```bash
export UPDATE_SERVER="https://your-update-server.com/updates"
export SSH_USER="youruser"
export SSH_HOST="yourserver.com"
export REMOTE_PATH="/var/www/html/updates"

./build.sh
```

Or use in GitHub Actions with secrets - see `.github/workflows/build.yml.example`


## Roadmap

- ~Implement namespaces (PSR-4) for Joomla 4/5 best practices~
- ~Modernize component to use current Joomla MVC patterns~
- ~Service provider for dependency injection~
- ~Centralize version management~
- ~Create CHANGELOG.md~
- ~Add basic analytics tracking~
- Integrate subscription system for analytics access
- Enhanced analytics with charts and trends
- Export analytics data
- Add unit tests
- Activate CI/CD pipeline (example workflow ready at `.github/workflows/build.yml.example`)

## Development Notes

Code improvements and refactoring assisted by Claude (Anthropic).

