<?php

namespace Craft;

class KeycdnService extends BaseApplicationComponent
{

	protected $apiBaseUrl;
	protected $settings;
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


		foreach ($urls as &$url)
		{
			$urlParts = parse_url($url);

			// make sure URL starts with the zone base and not an alias
			// "https://cdn.foo.com/flower.png" → "foo.kxcdn.com/flower.png"

			str_replace($urlParts['scheme'] . '://' . $urlParts['host'], $this->settings->zone, $url);
		}

		return $this->holler('zones/purgeurl/' . $this->settings->zoneId . '.json', $urls, 'delete');
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
			// TODO: warn that we don't have an API key, rather than just failing silently
			return;
		}

		$requestSettings = array(
			"auth"    => array($this->settings->apiKey, "password", "Basic"),
			"headers" => array(
				"Content-Type" => "applicaton/json; charset=utf-8",
				"Accept"       => "application/json, text/javascript, */*; q=0.01",
			),
			"verify" => false,
			"debug"  => false
		);

		$client = new \Guzzle\Http\Client($this->apiBaseUrl);

		// TODO: respond thoughtfully to failures

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
				$response = $request->send();

				if ( ! $response->isSuccessful())
				{
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
				$request = $this->client->post($endpoint, array(), $requestSettings);

				$request->setBody($data);
				$response = $request->send();

				if ( ! $response->isSuccessful())
				{
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
				$request = $this->client->delete($endpoint, array(), $requestSettings);

				$request->setBody($data);
				$response = $request->send();

				if ( ! $response->isSuccessful())
				{
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
				$request = $this->client->put($endpoint, array(), $requestSettings);

				$request->setBody($data);
				$response = $request->send();

				if ( ! $response->isSuccessful())
				{
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
