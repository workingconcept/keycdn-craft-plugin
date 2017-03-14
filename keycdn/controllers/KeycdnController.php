<?php

namespace Craft;

class KeycdnController extends BaseController
{

	public function actionClearZone()
	{
		$response = craft()->keycdn->clearAll();
		KeycdnPlugin::log(print_r($response, true), LogLevel::Info);

		if ($response->status === 'success')
		{
			craft()->userSession->setNotice(Craft::t('KeyCDN zone cache cleared.'));
		}
		else
		{
			craft()->userSession->setNotice(Craft::t('KeyCDN zone cache clear failed.'));
		}

		// TODO: redirect to someplace more sensible
		craft()->request->redirect('/admin');
	}

}
