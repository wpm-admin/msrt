<?php
class ModelExtensionModuleSmartMarketingAPI extends Model {
	public function syncSubscribers() {
		$curl_url = HTTPS_CATALOG . 'index.php?route=extension/module/smart_marketing/sync&secret_key=' . $this->config->get('module_smart_marketing_api_key');

		$curl_response = $this->sendCURL($curl_url);

		return $curl_response;
	}

	public function getOCXTemplates() {
		$curl_url = $this->getCURLBaseURL() . 'index.php?route=api/smart_marketing/templates&api_key=' . $this->config->get('module_smart_marketing_api_key');

		$curl_response = $this->sendCURL($curl_url);

		return $curl_response;
	}

	public function getOCXTemplate($template_id) {
		$curl_url = $this->getCURLBaseURL() . 'index.php?route=api/smart_marketing/template&api_key=' . $this->config->get('module_smart_marketing_api_key') . '&template_id=' . $template_id;

		$curl_response = $this->sendCURL($curl_url);

		return $curl_response;
	}

	public function getOCXBlocks() {
		$curl_url = $this->getCURLBaseURL() . 'index.php?route=api/smart_marketing/blocks&api_key=' . $this->config->get('module_smart_marketing_api_key');

		$curl_response = $this->sendCURL($curl_url);

		return $curl_response;
	}

	public function getAPIStatus($api_key) {
		$curl_url = $this->getCURLBaseURL() . 'index.php?route=api/smart_marketing/apiStatus&api_key=' . $api_key;

		$curl_response = $this->sendCURL($curl_url);

		return $curl_response;
	}

	private function getCURLBaseURL() {
		$curl_base_url = $this->config->get('module_smart_marketing_localhost_debug') ? 'https://localhost/work/ocx3/' : 'https://www.oc-extensions.com/';

		return $curl_base_url;
	}

	private function sendCURL($url, $info = array()) {
		$curl_status = array();

		$curl = curl_init();

		// Set SSL if required
		if (substr($url, 0, 5) == 'https') {
			curl_setopt($curl, CURLOPT_PORT, 443);
		}

		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_REFERER, HTTP_CATALOG);
		curl_setopt($curl, CURLOPT_URL, $url);

		if ($info) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($info));
		}

		$curl_response = curl_exec($curl);

		if (!$curl_response) {
			$curl_status['error'] = array('code' => 'error_curl', 'args' => array(curl_error($curl), curl_errno($curl)));
		} else {
			$curl_response = json_decode($curl_response, true);

			if (isset($curl_response['error'])) {
				$curl_status['error'] = $curl_response['error'];
			} elseif (!isset($curl_response['success']) || (isset($curl_response['success']) && $curl_response['success'] != true)) {
				$curl_status['error'] = array('code' => 'error_destination', 'args' => array($url));
			} else {
				$curl_status['response'] = $curl_response;
			}
		}

		curl_close($curl);

		return $curl_status;
	}
}
