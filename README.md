
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

## Environment Variables

To run this project, you will need to add the following environment variables to your file.

 `PLUGIN_NAME` 
 `PLUGIN_ELEMENT`
 `PLUGIN_VERSION`
 `PLUGIN_DESCRIPTION`
  `PLUGIN_DIR`
 `OUTPUT_DIR`
 `UPDATE_SERVER`
 `SSH_USER`
 `SSH_HOST`
 `REMOTE_PATH`

Consider using direnv to set it for you via a .envrc file. There is an example in the project.


## Roadmap

- Implement namespaces (PSR-4) for Joomla 4/5 best practices
- Modernize component to use current Joomla MVC patterns
- Add service provider for dependency injection
- Centralize version management
- Add unit tests
- Set up CI/CD pipeline
- Create CHANGELOG.md

## Development Notes

Code improvements and refactoring assisted by Claude (Anthropic).

