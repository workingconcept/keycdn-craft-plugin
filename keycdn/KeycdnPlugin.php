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
		return false;
	}

	public function init()
	{
		// TODO: limit potential action to Asset sources that rely on KeyCDN

		craft()->on('assets.onSaveAsset', function (Event $event) {
			if ($event->params['isNewAsset'] === false)
			{
				$asset = $event->params['asset'];
				$response = craft()->keycdn->clearUrls(array($asset->url));
				KeycdnPlugin::log(print_r($response, true), LogLevel::Info);
			}
		});

		craft()->on('assets.onDeleteAsset', function (Event $event) {
			$asset = $event->params['asset'];
			$response = craft()->keycdn->clearUrls(array($asset->url));
			KeycdnPlugin::log(print_r($response, true), LogLevel::Info);
		});
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('keycdn/_settings', array(
			'settings' => craft()->keycdn->settings
		));
	}

	protected function defineSettings()
	{
		return array(
			'apiKey' => array(AttributeType::String, 'required' => true, 'label' => 'KeyCDN API Key'),
			'zone'   => array(AttributeType::String, 'required' => true, 'label' => 'KeyCDN Zone URL'),
			'zoneId' => array(AttributeType::String, 'required' => true, 'label' => 'KeyCDN Zone ID'),
		);
	}

}
