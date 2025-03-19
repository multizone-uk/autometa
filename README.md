
# Automatic Meta Description

This is a Joomla! plugin which automatically generates a meta description from the article title and and first 140 characters of content if it does not exist, when you save.

It includes a script to generate the plugin from the source and create the required XML and deploy the plugin to an update server.

It contains a content plugin too but this is not publshed in the Joomla Extension Directory because it is quite invasive - regenerating the Meta Description for all articles.




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

- Improve the logic add an AI generator perhaps
- Some of the XML is hard coded for it specifically that it is a site content plugin.
- Some of the environment variables should probably be somewhere else to avoid duplication.
- Version number, Date etc is in three or four places in the code which is tedious.
- Provide a changelog

