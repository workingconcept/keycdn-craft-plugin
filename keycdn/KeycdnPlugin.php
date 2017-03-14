<?php

namespace Craft;

class KeycdnPlugin extends BasePlugin
{
	public function getName()
	{
		return Craft::t('KeyCDN');
	}

	public function getVersion()
	{
		return '0.0.1';
	}

	public function getDeveloper()
	{
		return 'Working Concept';
	}

	public function getDeveloperUrl()
	{
		return 'https://workingconcept.com';
	}

	public function hasCpSection()
	{
		return true;
	}

	public function init()
	{
		craft()->on('assets.onSaveAsset', function (Event $event) {

			if ($event->params['isNewAsset'] === false)
			{
				$asset = $event->params['asset'];

				// TODO: limit potential action to Asset sources that rely on KeyCDN
				craft()->keycdn->clearUrls(array($asset->url));
			}
		});
	}
}
