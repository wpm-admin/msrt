<?php
class ControllerExtensionModuleSmartMarketingProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/smart_marketing/product');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['entry_product'] = $this->language->get('entry_product');

		$data['help_product'] = $this->language->get('help_product');

		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/product', $data));
	}

	public function import() {
		$this->load->model('extension/module/smart_marketing/product');
		$this->load->model('tool/image');

		$json = array();

		if (isset($this->request->post['products_ids'])) {
			$products_ids = explode('-', $this->request->post['products_ids']);
		} else {
			$products_ids = array();
		}

		if ($products_ids) {
			$json['products'] = array();

			foreach ($products_ids as $product_id) {
				$product_info = $this->model_extension_module_smart_marketing_product->getProduct($product_id, true);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('module_smart_marketing_product_image_width'), $this->config->get('module_smart_marketing_product_image_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('module_smart_marketing_product_image_width'), $this->config->get('module_smart_marketing_product_image_height'));
					}

					$price = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));

					if ((float)$product_info['special']) {
						$special = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));

						$discount_amount = $price - $special;
						$discount_percentage = round(($special / $price - 1) * 100);
					} else {
						$special = false;
						$discount_amount = false;
						$discount_percentage = false;
					}

					$json['products'][] = array(
						'name'                => html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8'),
						'link'                => $this->model_extension_module_smart_marketing_product->getLink($product_id, $product_info['keyword']),
						'image' 					 => str_replace(" ", "%20", $image),  // some stores use space in image path -> replace to load properly
						'price'               => $this->currency->format($special ? $special : $price, $this->config->get('config_currency')),
						'price_new'           => ($special) ? $this->currency->format($special, $this->config->get('config_currency')) : false,
						'price_old'           => ($special) ? $this->currency->format($price, $this->config->get('config_currency')) : false,
						'discount_amount'     => ($special) ? $this->currency->format($discount_amount, $this->config->get('config_currency')) : false,
						'discount_percentage' => ($special) ? $discount_percentage . '%' : false,
						'description'         => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..'
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
			$this->load->model('extension/module/smart_marketing/product');

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

			$results = $this->model_extension_module_smart_marketing_product->getAutocomplete($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
