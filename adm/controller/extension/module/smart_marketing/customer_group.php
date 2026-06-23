<?php
class ControllerExtensionModuleSmartMarketingCustomerGroup extends Controller {
	private $error = array();

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_search'])) {
			$this->load->model('extension/module/smart_marketing/customer_group');

			$filter_search = $this->request->get['filter_search'];

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_search' => $filter_search,
				'start'         => 0,
				'limit'         => $limit
			);

			$results = $this->model_extension_module_smart_marketing_customer_group->getAutocomplete($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'customer_group_id' => $result['customer_group_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
