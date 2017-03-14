<?php

namespace Craft;

class KeycdnService extends BaseApplicationComponent
{
	public $settings = array();

	protected $apiBaseUrl;
	protected $isLinked;

	/**
	 * Constructor
	 */

	public function init()
	{
		parent::init();

		$this->settings   = craft()->plugins->getPlugin("keycdn")->getSettings();
		$this->apiBaseUrl = "https://api.keycdn.com/";
		$this->isLinked   = isset($this->settings->apiKey);
	}


	/**
	 * Clear specific URLs in KeyCDN's cache.
	 *
	 * @param  array  $urls  array of urls (without http(s)://)
	 *
	 * @return mixed  API response object or null
	 */

	public function clearUrls($urls = array())
	{
		if (count($urls) === 0)
		{
			return;
		}


		// make sure URLs start with the zone base and not an alias
		foreach ($urls as &$url)
		{
			$urlParts = parse_url($url);

			KeycdnPlugin::log('handling ' . $url, LogLevel::Info);

			if (isset($urlParts['scheme']) && isset($urlParts['host']))
			{
				// `https://cdn.foo.com/flower.png` → `foo.kxcdn.com/flower.png`
				$url = str_replace($urlParts['scheme'] . '://' . $urlParts['host'], $this->settings->zone, $url);
			}
			else
			{
				// `/flower.png` → `foo.kxcdn.com/flower.png`
				$url = $this->settings->zone . $url;
			}

			KeycdnPlugin::log('clearing ' . $url, LogLevel::Info);
		}


		return $this->holler('zones/purgeurl/' . $this->settings->zoneId . '.json', array('urls' => $urls), 'delete');
	}


	/**
	 * Clear the entire zone's cache in KeyCDN.
	 *
	 * @return mixed  API response object or null
	 */

	public function clearAll()
	{
		return $this->holler('zones/purge/' . $this->settings->zoneId . '.json');
	}

	private function holler($endpoint, $data = array(), $method = 'get')
	{
		if ( ! $this->isLinked)
		{
			KeycdnPlugin::log('Please set the KeyCDN API key.', LogLevel::Warning);
			return;
		}

		$requestSettings = array(
			"verify" => false,
			"debug"  => false
		);

		$client = new \Guzzle\Http\Client($this->apiBaseUrl);

		if ($method === 'get')
		{
			$query = '';

			if ( ! empty($data))
			{
				$query .= '?';
				$query .= http_build_query($data);
			}

			try
			{
				$request  = $client->get($endpoint . $query, array(), $requestSettings);

				$request->setAuth($this->settings->apiKey);

				$response = $request->send();

				if ( ! $response->isSuccessful())
				{
					KeycdnPlugin::log('Request failed: ' . $response->getBody(), LogLevel::Warning);
					return;
				}

				return json_decode($response->getBody(true));
			}
			catch(\Exception $e)
			{
				return;
			}
		}
		elseif ($method === 'post')
		{
			try
			{
				$request = $client->post($endpoint, array(), $requestSettings);

				$request->setAuth($this->settings->apiKey);
				$request->setBody($data);

				$response = $request->send();

				if ( ! $response->isSuccessful())
				{
					KeycdnPlugin::log('Request failed: ' . $response->getBody(), LogLevel::Warning);
					return;
				}

				return json_decode($response->getBody(true));
			}
			catch(\Exception $e)
			{
				return;
			}
		}
		elseif ($method === 'delete')
		{
			try
			{
				$request = $client->delete($endpoint, array(), $requestSettings);

				$request->setAuth($this->settings->apiKey);
				$request->setBody($data);

				$response = $request->send();

				if ( ! $response->isSuccessful())
				{
					KeycdnPlugin::log('Request failed: ' . $response->getBody(), LogLevel::Warning);
					return;
				}

				return json_decode($response->getBody(true));
			}
			catch(\Exception $e)
			{
				return;
			}
		}
		elseif ($method === 'put')
		{
			try
			{
				$request = $client->put($endpoint, array(), $requestSettings);

				$request->setBody($data);
				$request->setAuth($this->settings->apiKey);

				$response = $request->send();

				if ( ! $response->isSuccessful())
				{
					KeycdnPlugin::log('Request failed: ' . $response->getBody(), LogLevel::Warning);
					return;
				}

				return json_decode($response->getBody(true));
			}
			catch(\Exception $e)
			{
				return;
			}
		}
		else
		{
			// unsupportd method
		}
	}

}