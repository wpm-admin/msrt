<?php
class ControllerExtensionModuleSmartMarketingCategory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/smart_marketing/category');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['entry_category'] = $this->language->get('entry_category');

		$data['help_category'] = $this->language->get('help_category');

		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/category', $data));
	}

	public function import() {
		$this->load->model('extension/module/smart_marketing/category');
		$this->load->model('tool/image');

		$json = array();

		if (isset($this->request->post['categories_ids'])) {
			$categories_ids = explode('-', $this->request->post['categories_ids']);
		} else {
			$categories_ids = array();
		}

		if ($categories_ids) {
			$json['categories'] = array();

			foreach ($categories_ids as $category_id) {
				$category_info = $this->model_extension_module_smart_marketing_category->getCategory($category_id, true);

				if ($category_info) {
					if ($category_info['image']) {
						$image = $this->model_tool_image->resize($category_info['image'], $this->config->get('module_smart_marketing_category_image_width'), $this->config->get('module_smart_marketing_category_image_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('module_smart_marketing_category_image_width'), $this->config->get('module_smart_marketing_category_image_height'));
					}

					$json['categories'][] = array(
						'name'  					 => html_entity_decode($category_info['name'], ENT_QUOTES, 'UTF-8'),
						'link'  					 => $this->model_extension_module_smart_marketing_category->getLink($category_id, $category_info['keyword']),
						'image' 					 => str_replace(" ", "%20", $image),  // some stores use space in image path -> replace to load properly
						'description'         => utf8_substr(strip_tags(html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..'
					);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_search'])) {
			$this->load->model('extension/module/smart_marketing/category');

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

			$results = $this->model_extension_module_smart_marketing_category->getAutocomplete($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
