<?php
	/**
	 * Builds a baseUrl containing parameters defined in config.json, and
	 * performs the defined request against the Shopify API.
	 *
	 * Currently implemented only for use with Private Shopify apps.
	 */
	class ShopifyRequest {
		protected $configFile = 'config.json';
		protected $config = '';
		protected $configMap = [
			'api_key' => [
				'prop' => 'apiKey',
				'env' => 'SHOPIFY_API_KEY'
			],
			'pass' => [
				'prop' => 'pass',
				'env' => 'SHOPIFY_PASS'
			],
			'shop_name' => [
				'prop' => 'shopName',
				'env' => 'SHOPIFY_SHOP_NAME'
			],
		];

		protected $apiKey;
		protected $pass;
		protected $shopName;
		protected $baseUrl = "https://%s:%s@%s.myshopify.com";

		protected $supportedRequestTypes = [
			'GET',
			'POST'
		];

		/**
		 * Build the base url on instantiation.
		 */
		public function __construct() {
			$this->getConfig();
			$this->buildShopUrl();
		}

		/**
		 * Loads up the config, and retrieves values defined
		 * in the configMap.
		 *
		 * @return ShopifyRequest $this
		 */
		protected function getConfig() {
			$handle = fopen($this->configFile, 'r');
			$contents = fread($handle, filesize($this->configFile));
			fclose($handle);

			$this->config = json_decode($contents);

			foreach ($this->configMap as $key => $value) {
				if (!array_key_exists($key, $this->config)) {
					throw new Exception('Missing key: ' . $key);
				}

				if (empty($this->config->{$key})) {
					/*
						If the config isn't found we'll check if an environment
						variable exists, this is largely Heroku specific, and offers
						us a nice way to avoid deploying any config.

						If time were no issue this handling would be more loosely
						coupled to this class.
					*/
					$env_value = getenv($value['env']);

					if (empty($env_value) && empty($this->config->{$env_value})) {
						throw new Exception('Missing value: ' . $key);
					}
				}

				$this->{$value['prop']} = !empty($env_value) ? $this->config->{$env_value} : $this->config->{$key};
			}

			return $this;
		}

		/**
		 * Replaces the tokenised values defined in the baseUrl.
		 *
		 * @return ShopifyRequest $this
		 */
		public function buildShopUrl() {
			$this->baseUrl = sprintf($this->baseUrl, $this->apiKey, $this->pass, $this->shopName);
			return $this;
		}

		/**
		 * Performs the request.
		 *
		 * @param  string $type
		 *   As defined in $upportedRequestTypes
		 * @param  string $route
		 *   The destination of the request (appended to the baseUrl).
		 * @param  array  $params
		 *   An associative array of any parameters to be passed.
		 *
		 * @return string $response
		 *   An unmodified response.
		 */
		public function doRequest($type, $route, array $params = []) {
			if (!in_array($type, $this->supportedRequestTypes)) {
				throw new Exception('Unexpected request type: ' . $this->supportedRequestTypes);
			}

			// Use curl, do request
			$request = curl_init();

			// Set the basic request parameters.
			$options = [
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => "$this->baseUrl/$route",
			];

			// Handle post data.
			if ($type === 'POST') {
				$params = json_encode($params);

				$options = $options + [
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => $params,
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json',
						'Content-Length: ' . strlen($params)
					]
				];
			}

			curl_setopt_array($request, $options);

			// Perform the request and handle errors (rudimentarily).
			if (!$response = curl_exec($request)) {
			  throw new Exception('Request failed: "' . curl_error($request) . " (" . curl_errno($request) . ")");
			}

			if (strstr($response, 'errors')) {
				throw new Exception('Request failed: ' . $response);
			}

			return $response;
		}

		public function setApiKey($api_key) {
			$this->apiKey = $api_key;
			return $this;
		}

		public function setPass($pass) {
			$this->pass = $pass;
			return $this;
		}

		public function setShopName($shop_name) {
			$this->shopName = $shop_name;
			return $this;
		}

		public function getStatus() {
			return $this->connected;
		}
	}
?>
