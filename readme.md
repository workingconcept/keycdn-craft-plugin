# KeyCDN Craft Plugin

Automatically clear KeyCDN's cache for an Asset when it's updated in Craft, and provide the option to nuke the _entire_ KeyCDN cache from the control panel.

## Installation and Setup

Drop the `keycdn` folder in your `craft/plugins` directory, then visit Settings → Plugins and install the KeyCDN plugin.

Add your KeyCDN API key and zone details to the plugin settings, and you should be done. Just be sure to test, because that's always a fabulous idea.

## Limitations

- The plugin assumes you're using a single zone.