<?php
class ControllerExtensionModuleSmartMarketingLanguage extends Controller {
	public function index() {
		$this->load->language('extension/module/smart_marketing/language');

		$this->load->model('extension/module/smart_marketing/language');

		$data['text_language'] = $this->language->get('text_language');

		$data['languages'] = $this->model_extension_module_smart_marketing_language->getActiveLanguages();

		$data['multilanguage'] = (count($data['languages']) > 1) ? true : false;

		if (!isset($this->session->data['smart_marketing_language_id'])) {
			$store_language_id = $this->model_extension_module_smart_marketing_language->getLanguageIdByCode($this->config->get('config_language'));

			$this->session->data['smart_marketing_language_id'] = $store_language_id;
		}

		$data['language_name'] = $this->model_extension_module_smart_marketing_language->getLanguageNameById($this->session->data['smart_marketing_language_id']);

		return $this->load->view('extension/module/smart_marketing/language', $data);
	}

	public function switch() {
		$json = array();

		if (isset($this->request->post['language_id'])) {
			$this->session->data['smart_marketing_language_id'] = (int)$this->request->post['language_id'];

			$json['success'] = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
