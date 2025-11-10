
# Automatic Meta Description

This is a Joomla! plugin which automatically generates a meta description from the article title and content if it does not exist, when you save.

**Features:**
- Automatically generates meta descriptions on article save
- Configurable character limit (default 160, optimal for SEO)
- Smart truncation at word boundaries
- Configurable separator between title and content
- Option to include/exclude title or content
- Option to overwrite existing descriptions
- Component for bulk regeneration of all articles
- Batch processing to handle large sites efficiently
- CSRF protection and permission checks
- Error handling and logging

It includes a script to generate the plugin from the source and create the required XML and deploy the plugin to an update server.

It contains a component too for bulk regeneration but this is not published in the Joomla Extension Directory.




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

Build the plugin locally:

```bash
./build.sh
```

This creates distribution files in `dist/`:
- `autometa-{version}.zip` - Versioned plugin package
- `autometa-latest.zip` - Latest version copy
- `autometa.xml` - Update server XML with hashes

Version is automatically read from `plg_content_autometa/plg-autometa.xml`.

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
- Add service provider for dependency injection
- ~Centralize version management~
- Add unit tests
- Set up CI/CD pipeline
- ~Create CHANGELOG.md~

## Development Notes

Code improvements and refactoring assisted by Claude (Anthropic).

