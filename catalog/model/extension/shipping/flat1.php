<?php
class ModelExtensionShippingFlat1 extends Model {
	function getQuote($address) {
		
		$my_log = new Log('shipping_fedex.log');
		
		
		
		$this->load->language('extension/shipping/flat1');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_flat1_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_flat1_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		
		$ip = $_SERVER['REMOTE_ADDR'];
		if(!isset($this->session->data['shipping_address']) AND @$ip_info = file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}")){
			$details = json_decode($ip_info);
			$country_name = $details->geoplugin_countryName;
			
			$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountryOnISO($details->geoplugin_countryCode);
		}else{
			$country_info = $this->session->data['shipping_address'];
		}
		
		if($country_info['iso_code_2'] == 'US'){
			$type_id = 1;
		}else{
			$type_id = 2;
		}
		
		$weight = $this->cart->getWeight();
		
		//8 класс веса это фунты. Возможно надо будет сюда тоже логику добавить. Но пока все в фунтах
		$weight = $this->weight->convert($weight, $this->config->get('config_weight_class_id'), 8);
		
		$sql = "SELECT * FROM " . DB_PREFIX . "shipping_cost WHERE type_id = '" . (int)$type_id . "' AND weight >= '" . (float)$weight . "' ORDER BY weight ASC LIMIT 1";
		
		$query = $this->db->query($sql);

		$my_log->write(date('Y-m-d H:i:s'). ' '. $sql);
								  
		if($query->num_rows){
			$cost = $query->row['price'];
		}else{
			$cost = 0;
		}
		

		
		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['flat1'] = array(
				'code'         => 'flat1.flat1',
				'title'        => sprintf($this->language->get('text_description'), '<strong>' . (isset($country_info['name']) ? $country_info['name'] : $country_info['country']) . '</strong>'),
				'cost'         => $cost,
				'tax_class_id' => $this->config->get('shipping_flat1_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_flat1_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'flat1',
				'title'      => $this->language->get('text_title') . ' - <b>' . (isset($country_info['name']) ? $country_info['name'] : $country_info['country']) . '</b>',
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_flat1_sort_order'),
				'error'      => false
			);
		}
		
		$my_log->write(date('Y-m-d H:i:s'). ' '. json_encode($method_data));
		$my_log->write(date('Y-m-d H:i:s'). '------------------');
		
		return $method_data;
	}
	function getQuoteOnProduct($product_id) {
		$this->load->language('extension/shipping/flat1');
		$this->load->model('catalog/product');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_flat1_geo_zone_id') . "'");

		if (!$this->config->get('shipping_flat1_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		
		$ip = $_SERVER['REMOTE_ADDR'];
		if(!isset($this->session->data['shipping_address']) AND @$ip_info = file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}")){
			$details = json_decode($ip_info);
			$country_name = $details->geoplugin_countryName;
			
			$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountryOnISO($details->geoplugin_countryCode);
		}else{
			$country_info = isset($this->session->data['shipping_address']) ? $this->session->data['shipping_address'] : false;
		}
		
		if($country_info['iso_code_2'] == 'US'){
			$type_id = 1;
		}else{
			$type_id = 2;
		}
	
		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		//8 класс веса это фунты. Возможно надо будет сюда тоже логику добавить. Но пока все в фунтах
		$weight = $this->weight->convert($product_info['weight'], $product_info['weight_class_id'], 8);
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipping_cost WHERE
								  type_id = '" . (int)$type_id . "' AND
								  weight >= '" . (float)$weight . "'
								  ORDER BY weight ASC LIMIT 1");

		if($query->num_rows){
			$cost = $query->row['price'];
		}else{
			$cost = 0;
		}
		
		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['flat1'] = array(
				'code'         => 'flat1.flat1',
				'title'        => sprintf($this->language->get('text_description'), '<strong>' . (isset($country_info['name']) ? $country_info['name'] : $country_info['country']) . '</strong>'),
				'cost'         => $cost,
				'tax_class_id' => $this->config->get('shipping_flat1_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_flat1_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'flat1',
				'title'      => $this->language->get('text_title') . ' - <b>' . (isset($country_info['name']) ? $country_info['name'] : $country_info['country']) . '</b>',
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_flat1_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}