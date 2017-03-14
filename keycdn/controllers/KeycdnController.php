<?php

namespace Craft;

class KeycdnController extends BaseController
{

	public function clearZone()
	{
		return craft()->keycdn->clearAll();
	}

}
