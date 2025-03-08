block_readspeaker_embhl
=======================

Moodle block which inserts a Listen button on pages that enable users to listen to content using ReadSpeaker text-to-speech functionality. The plugin is also available on the official Moodle Mobile App.

Requirements
------------

This plugin requires Moodle 3.0+.


Motivation for this plugin
--------------------------

The block_readspeaker_embhl can be used to insert a block with a Listen button into pages to enable the functionality of ReadSpeaker webReader for Moodle.


Installation
------------

Install the plugin to the folder
/blocks/readspeaker_embhl

See [https://docs.moodle.org/en/Installing_plugins](https://docs.moodle.org/en/Installing_plugins) for details on installing Moodle plugins

Usage & Settings
----------------

After installing the plugin, it needs to be configured with customers specific details that you received from ReadSpeaker after purchasing a license.
You can view installation instructions and customer specific details by logging in to the ReadSpeaker portal at [https://app.readspeaker.com/portal](https://app.readspeaker.com/portal) with the credentials you recieved from the ReadSpeaker support team.
If you do not remember your login or did not receive an email from the ReadSpeaker support team with login details, please contact your ReadSpeaker account manager or get in direct contact with the ReadSpeaker support team at: [mailto:support@readspeaker.com](mailto:support@readspeaker.com).


License / Subscription details
-------

This plugin requires a paid license with ReadSpeaker in order to use the webReader for Moodle and docReader functionality.
A license for using webReader for Moodle and docReader can be purchased by contacting ReadSpeaker: [https://www.readspeaker.com/contact/](https://www.readspeaker.com/contact/). Please note that the desktop (browser) version and the mobile app version are licensed separately and the mobile plugin must first be enabled by ReadSpeaker. 


Requirements
------------

This is a paid plugin that requires a license with ReadSpeaker in order to use.

Requirements for the functionality of webReader for Moodle and docReader are outlined in the specification documentation for respective products whne you purchase a license.

For docReader it is important to note that since the functionality for the product relies on the docReader server fetching and parsing documents, the Moodle platform used must be publically available to the docReader server and there is no firewall or other measurement preventing the docReader proxy component (located in the plugin) for making a request towards the Moodle platform in order to retrieve the document and return it to the docReader server.