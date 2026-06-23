<?php
class ControllerExtensionModuleSmartMarketingSearch extends Controller {
	public function index() {
		$this->load->language('extension/module/smart_marketing/search');

		$data['heading_title'] = $this->language->get('heading_title');

		// Text
		$data['text_select'] = $this->language->get('text_select');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_in_stock'] = $this->language->get('text_in_stock');
		$data['text_out_stock'] = $this->language->get('text_out_stock');
		$data['text_hand_pick'] = $this->language->get('text_hand_pick');
		$data['text_latest'] = $this->language->get('text_latest');
		$data['text_special'] = $this->language->get('text_special');
		$data['text_top_sales'] = $this->language->get('text_top_sales');
		$data['text_related'] = $this->language->get('text_related');
		$data['text_also_bought'] = $this->language->get('text_also_bought');
		$data['text_advanced_search'] = $this->language->get('text_advanced_search');
		$data['text_search_match'] = $this->language->get('text_search_match');
		$data['text_any_condition'] = $this->language->get('text_any_condition');
		$data['text_all_condition'] = $this->language->get('text_all_condition');
		$data['text_following_condition'] = $this->language->get('text_following_condition');
		$data['text_product'] = $this->language->get('text_product');

		$data['text_field_name'] = $this->language->get('text_field_name');
		$data['text_field_model'] = $this->language->get('text_field_model');
		$data['text_field_stock'] = $this->language->get('text_field_stock');
		$data['text_field_quantity'] = $this->language->get('text_field_quantity');
		$data['text_field_price'] = $this->language->get('text_field_price');
		$data['text_field_special'] = $this->language->get('text_field_special');
		$data['text_field_discount_percent'] = $this->language->get('text_field_discount_percent');
		$data['text_field_discount_amount'] = $this->language->get('text_field_discount_amount');
		$data['text_field_discount_date_start'] = $this->language->get('text_field_discount_date_start');
		$data['text_field_discount_date_end'] = $this->language->get('text_field_discount_date_end');
		$data['text_field_points'] = $this->language->get('text_field_points');
		$data['text_field_viewed'] = $this->language->get('text_field_viewed');
		$data['text_field_shipping'] = $this->language->get('text_field_shipping');
		$data['text_field_date_added'] = $this->language->get('text_field_date_added');
		$data['text_field_date_modified'] = $this->language->get('text_field_date_modified');
		$data['text_field_manufacturer'] = $this->language->get('text_field_manufacturer');
		$data['text_field_category'] = $this->language->get('text_field_category');

		$data['text_operator_is'] = $this->language->get('text_operator_is');
		$data['text_operator_is_not'] = $this->language->get('text_operator_is_not');
		$data['text_operator_contain'] = $this->language->get('text_operator_contain');
		$data['text_operator_not_contain'] = $this->language->get('text_operator_not_contain');
		$data['text_operator_start_with'] = $this->language->get('text_operator_start_with');
		$data['text_operator_end_with'] = $this->language->get('text_operator_end_with');
		$data['text_operator_greater_than'] = $this->language->get('text_operator_greater_than');
		$data['text_operator_less_than'] = $this->language->get('text_operator_less_than');
		$data['text_operator_is_before'] = $this->language->get('text_operator_is_before');
		$data['text_operator_is_after'] = $this->language->get('text_operator_is_after');
		$data['text_operator_is_in'] = $this->language->get('text_operator_is_in');
		$data['text_operator_is_not_in'] = $this->language->get('text_operator_is_not_in');

		$data['text_search_result'] = $this->language->get('text_search_result');
		$data['text_selected_product'] = $this->language->get('text_selected_product');
		$data['text_name_asc'] = $this->language->get('text_name_asc');
		$data['text_name_desc'] = $this->language->get('text_name_desc');
		$data['text_date_added_asc'] = $this->language->get('text_date_added_asc');
		$data['text_date_added_desc'] = $this->language->get('text_date_added_desc');
		$data['text_discount_asc'] = $this->language->get('text_discount_asc');
		$data['text_discount_desc'] = $this->language->get('text_discount_desc');
		$data['text_sold_quantity_asc'] = $this->language->get('text_sold_quantity_asc');
		$data['text_sold_quantity_desc'] = $this->language->get('text_sold_quantity_desc');
		$data['text_amount_paid_asc'] = $this->language->get('text_amount_paid_asc');
		$data['text_amount_paid_desc'] = $this->language->get('text_amount_paid_desc');
		$data['text_top_quantity'] = $this->language->get('text_top_quantity');
		$data['text_top_total'] = $this->language->get('text_top_total');

		$data['entry_import'] = $this->language->get('entry_import');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_sub_category'] = $this->language->get('entry_sub_category');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_discount_min'] = $this->language->get('entry_discount_min');
		$data['entry_discount_max'] = $this->language->get('entry_discount_max');
		$data['entry_top_type'] = $this->language->get('entry_top_type');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_sort'] = $this->language->get('entry_sort');

		$data['help_limit'] = $this->language->get('help_limit');
		$data['help_import_limit'] = $this->language->get('help_import_limit');
		$data['help_select_multiple'] = $this->language->get('help_select_multiple');
		$data['help_latest_date_start'] = $this->language->get('help_latest_date_start');
		$data['help_category'] = $this->language->get('help_category');
		$data['help_sub_category'] = $this->language->get('help_sub_category');
		$data['help_discount_min'] = $this->language->get('help_discount_min');
		$data['help_discount_max'] = $this->language->get('help_discount_max');
		$data['help_top_sales_date_start'] = $this->language->get('help_top_sales_date_start');
		$data['help_top_sales_date_end'] = $this->language->get('help_top_sales_date_end');

		$data['currency_symbol_left'] = $this->currency->getSymbolLeft($this->config->get('config_currency'));
		$data['currency_symbol_right'] = $this->currency->getSymbolRight($this->config->get('config_currency'));

		$this->load->model('extension/module/smart_marketing/manufacturer');

		$data['manufacturers'] = $this->model_extension_module_smart_marketing_manufacturer->getManufacturers();

		$this->load->model('extension/module/smart_marketing/category');

		$data['categories'] = $this->model_extension_module_smart_marketing_category->getCategories();

		$data['language_switcher'] = $this->load->controller('extension/module/smart_marketing/language');

		// Button
		$data['button_add_condition'] = $this->language->get('button_add_condition');
		$data['button_remove_condition'] = $this->language->get('button_remove_condition');
		$data['button_search_product'] = $this->language->get('button_search_product');
		$data['button_import'] = $this->language->get('button_import');
		$data['button_select_all'] = $this->language->get('button_select_all');
		$data['button_remove_all'] = $this->language->get('button_remove_all');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/search', $data));
	}

	public function search() {
		$this->load->language('extension/module/smart_marketing/search');

		$this->load->model('extension/module/smart_marketing/search');

		$json = array();

		$products_ids = array();

		$import_source = $this->request->post['import_source'];

		if ($import_source == 'latest') {
			if (utf8_strlen($this->request->post['latest_date_start']) > 0 && utf8_strlen($this->request->post['latest_date_start']) != 10) {
				$json['error']['latest_date_start'] = $this->language->get('error_invalid_date');
			}

			if (!$json) {
				$filter_data = array(
					'filter_date_start'   => $this->request->post['latest_date_start'],
					'filter_category'     => isset($this->request->post['latest_category']) ? $this->request->post['latest_category']	: array(),
					'filter_sub_category' => $this->request->post['latest_sub_category'],
					'sort'       		    => 'p.product_id',
					'order'      		    => 'DESC',
					'limit'      		    => isset($this->request->post['limit']) ? $this->request->post['limit'] : 3
				);

				$products_ids = $this->model_extension_module_smart_marketing_search->getLatestProductsIds($filter_data);
			}
		}

		if ($import_source == 'special') {
			if (utf8_strlen($this->request->post['special_discount_min']) > 0 && !is_numeric($this->request->post['special_discount_min'])) {
				$json['error']['special_discount_min'] = $this->language->get('error_discount_percent');
			}

			if (utf8_strlen($this->request->post['special_discount_max']) > 0 && !is_numeric($this->request->post['special_discount_max'])) {
				$json['error']['special_discount_max'] = $this->language->get('error_discount_percent');
			}

			if (!$json) {
				$sort_order_info = explode("|", $this->request->post['special_sort_order']);

				$filter_data = array(
					'filter_category'     => isset($this->request->post['special_category']) ? $this->request->post['special_category']	: array(),
					'filter_sub_category' => $this->request->post['special_sub_category'],
					'filter_discount_min' => is_numeric($this->request->post['special_discount_min']) ?  (float)$this->request->post['special_discount_min']: '',
					'filter_discount_max' => is_numeric($this->request->post['special_discount_max']) ?  (float)$this->request->post['special_discount_max']: '',
					'sort'       		    => $sort_order_info[0],
					'order'      		    => strtoupper($sort_order_info[1]),
					'limit'      		    => isset($this->request->post['limit']) ? $this->request->post['limit'] : 3
				);

				$products_ids = $this->model_extension_module_smart_marketing_search->getSpecialProductsIds($filter_data);
			}
		}

		if ($import_source == 'top-sales') {
			if (utf8_strlen($this->request->post['top_sales_date_start']) > 0 && utf8_strlen($this->request->post['top_sales_date_start']) != 10) {
				$json['error']['top_sales_date_start'] = $this->language->get('error_invalid_date');
			}

			if (utf8_strlen($this->request->post['top_sales_date_end']) > 0 && utf8_strlen($this->request->post['top_sales_date_end']) != 10) {
				$json['error']['top_sales_date_end'] = $this->language->get('error_invalid_date');
			}

			if (!$json) {
				$filter_data = array(
					'filter_date_start'   => $this->request->post['top_sales_date_start'],
					'filter_date_end'     => $this->request->post['top_sales_date_end'],
					'filter_category'     => isset($this->request->post['top_sales_category']) ? $this->request->post['top_sales_category']	: array(),
					'filter_sub_category' => $this->request->post['top_sales_sub_category'],
					'sort'       		    => ($this->request->post['top_sales_type'] == 'quantity') ? 'sold_quantity' : 'amount_paid',
					'order'      		    => 'DESC',
					'limit'      		    => isset($this->request->post['limit']) ? $this->request->post['limit'] : 3
				);

				$products_ids = $this->model_extension_module_smart_marketing_search->getTopSalesProductsIds($filter_data);
			}
		}

		if ($import_source == 'related') {
			if (!isset($this->request->post['related_product'])) {
				$json['error']['related_product'] = $this->language->get('error_product');
			}

			if (!$json) {
				$filter_data = array(
					'filter_related' => $this->request->post['related_product'],
					'sort'       	  => 'p.product_id',
					'order'      	  => 'DESC',
					'limit'      	  => isset($this->request->post['limit']) ? $this->request->post['limit'] : 3
				);

				$products_ids = $this->model_extension_module_smart_marketing_search->getRelatedProductsIds($filter_data);
			}
		}

		if ($import_source == 'also-bought') {
			if (!isset($this->request->post['also_bought_product'])) {
				$json['error']['also_bought_product'] = $this->language->get('error_product');
			}

			if (!$json) {
				$sort_order_info = explode("|", $this->request->post['special_sort_order']);

				$filter_data = array(
					'filter_also_bought' => $this->request->post['also_bought_product'],
					'sort'       		   => $sort_order_info[0],
					'order'      		   => strtoupper($sort_order_info[1]),
					'limit'      	      => isset($this->request->post['limit']) ? $this->request->post['limit'] : 3
				);

				$products_ids = $this->model_extension_module_smart_marketing_search->getBoughtTogetherProductsIds($filter_data);
			}
		}

		if ($import_source == 'advanced-search') {
			if (!isset($this->request->post['search_condition'])) {
				$json['error']['warning'] = $this->language->get('error_no_search_condition');
			} else {
				foreach ($this->request->post['search_condition'] as $search_condititon) {
					if (utf8_strlen($search_condititon['key']) < 1) {
						$json['error']['warning'] = $this->language->get('error_search_condition_key');

						break;
					}

					if (utf8_strlen($search_condititon['operator']) < 1) {
						$json['error']['warning'] = $this->language->get('error_search_condition_operator');

						break;
					}

					if (utf8_strlen($search_condititon['value']) < 1) {
						$json['error']['warning'] = $this->language->get('error_search_condition_value');

						break;
					}
				}
			}

			if (!$json) {
				$sort_order_info = explode("|", $this->request->post['search_sort_order']);

				$filter_data = array(
					'filter_match'     => $this->request->post['search_match'],
					'filter_condition' => $this->request->post['search_condition'],
					'sort'       		 => $sort_order_info[0],
					'order'      		 => strtoupper($sort_order_info[1]),
					'limit'      	    => isset($this->request->post['limit']) ? $this->request->post['limit'] : 3
				);

				$products_ids = $this->model_extension_module_smart_marketing_search->getAdvancedSearchProductsIds($filter_data);
			}
		}

		if ($products_ids) {
			$products = $this->model_extension_module_smart_marketing_search->getProductsByIds($products_ids);

			if ($products) {
				$data['column_name'] = $this->language->get('column_name');
				$data['column_quantity'] = $this->language->get('column_quantity');
				$data['column_price'] = $this->language->get('column_price');

				foreach ($products as $product) {
					$price = $this->currency->format($this->tax->calculate($product['discount'] ? $product['discount'] : $product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));

					if ((float)$product['special']) {
						$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
					} else {
						$special = false;
					}

					$data['products'][] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'quantity'   => $product['quantity'],
						'price'      => $price,
						'special'    => $special
					);
				}

				$json['output'] = $this->load->view('extension/module/smart_marketing/search_result', $data);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getManufacturers() {
		$json = array();

		$this->load->model('catalog/manufacturer');

		$filter_data = array(
			'sort'  => 'name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => PHP_INT_MAX
		);

		$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

		if ($results) {
			foreach ($results as $result) {
				$json['manufacturers'][] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => $result['name']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getCategories() {
		$json = array();

		$this->load->model('catalog/category');

		$filter_data = array(
			'filter_name' => '',
			'sort'        => 'name',
			'order'       => 'ASC',
			'start'       => 0,
			'limit'       => PHP_INT_MAX
		);

		$results = $this->model_catalog_category->getCategories($filter_data);

		if ($results) {
			foreach ($results as $result) {
				$json['categories'][] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
