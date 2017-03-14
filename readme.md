# KeyCDN Craft Plugin

Automatically clear KeyCDN's cache for an Asset when it's updated in Craft, and provide the option to nuke the _entire_ KeyCDN cache from the control panel. The plugin will only try to clear caches for Assets being updated or deleted.

## Installation and Setup

Drop the `keycdn` folder in your `craft/plugins` directory, then visit Settings → Plugins and install the KeyCDN plugin.

Add your KeyCDN API key and zone details to the plugin settings, and you should be done. Be sure to test, because testing is always a fabulous idea.

## Nuking Your Zone

Visit `/admin/actions/keycdn/clearZone` with a sense of adventure. This endpoint is the most user-friendly mechanism right now, but something fancier—like a button—may well exist in the future.

## Troubleshooting

If a given cache doesn't seem to be cleared, make sure `devMode` is enabled and check /craft/storage/runtime/logs/keycdn.log. You should find brief traces that identify cache-clearing attempts and summarize responses from the KeyCDN API. Please be prepared to share these logs if you're looking for help.

Submit an issue here or email hello@workingconcept.com if you run into any issues, and I'll make my best effort to respond in a timely fashion. I appreciate any feedback at all, and appreciate your patience since this is a free-time project.

## Limitations

- Supports only one zone.
- Doesn't know or care which Asset sources feed KeyCDN, just tries clearing URLs in update+delete conditions.
- May not be suitable for sizeable bulk operations; if you replace or delete a massive number of files, it could result in the same number of hits to the KeyCDN API.