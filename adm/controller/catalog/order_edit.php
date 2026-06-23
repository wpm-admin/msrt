<?php
class ControllerCatalogOrderEdit extends Controller {
	private $error = array();

	public function get_form(){
		
		if(!isset($this->request->get['field']) OR !isset($this->request->get['order_id'])){
			return false;
		}
		
		$order_id = (int)$this->request->get['order_id'];
		$field = $this->request->get['field'];
		
		$this->load->model('sale/order');
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$html = '<input type="hidden" name="order_id" value="'.$order_id.'" class="modal_edit">';
		/*
		$order_fiends = array('name', 'description');
		if(in_array($field, $order_fiends)){
			
			$data['description'] = $this->model_catalog_order->getProductDescriptions($this->request->get['order_id']);
			
			foreach($data['languages'] as $language){
				
				$value = html_entity_decode($data['description'][$language['language_id']][$field], ENT_QUOTES, 'UTF-8');
				$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
				$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
				$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
				
				$html .= '<div class="input-group" style="min-width:300px;"><span class="input-group-addon"><img src="language/' . $language['code'] .'/' . $language['code'] . '.png" title="' . $language['name'] . '"/></span>';
				$html .= '<input type="text" name="order_description[' . $language['language_id'] . ']['.$field.']" value="' . htmlspecialchars($value, ENT_QUOTES) . '" class="form-control modal_edit"/>';
				$html .= '</div>';
				
			}
			$html .= '<div class="input-group" style="min-width:400px;">';
		}
		*/
		$order_fiends = array('shipping_method', 'shipping_number', 'shipping_date');
		if(in_array($field, $order_fiends)){ 
			
			$data['order_info'] = $this->model_sale_order->getOrder($this->request->get['order_id']);

			$order_fiends = array( 'shipping_number', 'shipping_date');
		
			if(in_array($field, $order_fiends)){ 
					
				if($field == 'shipping_date'){
					if(!empty($data['order_info'][$field])){
						$data['order_info'][$field]      = date('d/m/Y', strtotime($data['order_info'][$field]));
					}else{
						$data['order_info'][$field]      = date('d/m/Y');
					}
				}
				$html .= '<div class="input-group" style="min-width:100px;">';
				$html .= '<input type="text" name="order['.$field.']" value="' . htmlspecialchars($data['order_info'][$field], ENT_QUOTES) . '" class="form-control modal_edit"/>';
				$html .= '</div>';
			}
			
			if($field == 'shipping_method'){
				// Shipping Methods
				$method_data = array();
	
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=checkout/shipping_method/getjson');
				$method_data = json_decode(curl_exec($ch), true);
				curl_close($ch);
				
				
				$html .= '<div class="input-group" style="min-width:100px;">';
				$html .= '<select name="order['.$field.']" class="form-control modal_edit">';
					foreach($method_data as $code => $row){
					$html .= '<option value="'.$code.'"';
					if($row['title'] == $data['order_info'][$field]) $html .= ' selected';
					$html .= '>'.$row['title'].'</option>';
					}
				$html .= '</select></div>';
			}
			/*
			$order_fiends = array('manufacturer_id', 'stock_status_id', 'status');
			if(in_array($field, $order_fiends)){
				
				if($field == 'manufacturer_id'){
					$this->load->model('catalog/manufacturer');
					$list = $this->model_catalog_manufacturer->getManufacturers();
				}elseif($field == 'status'){
					$list = array(
								  array('status' => 0, 'name' => 'Disabled'),
								  array('status' => 1, 'name' => 'Enabled'),
								);
				}elseif($field == 'stock_status_id'){
					$this->load->model('catalog/stock_status');
					$list = $this->model_catalog_stock_status->getStockStatuses();
				}
				
				$html .= '<div class="input-group" style="min-width:100px;">';
				$html .= '<select name="order['.$field.']" class="form-control modal_edit">';
					foreach($list as $row){
					$html .= '<option value="'.$row[$field].'"';
					if($row[$field] == $data['order_info'][$field]) $html .= ' selected';
					$html .= '>'.$row['name'].'</option>';
					}
				$html .= '</select></div>';
			}
			*/
			$html .= '<div class="input-group" style="min-width:100px;">';
		}
		
		$order_fiends = array('shipping_method', 'shipping_number', 'shipping_date');
		if(in_array($field, $order_fiends)){ 
			$html .= '<input type="button" name="save_email" value="save+email" class="form-control modal_save" style="background-color: #b0bef3;">';
		}
			
		$html .= '<input type="button" name="save" value="save" class="form-control modal_save" style="background-color: #b0bef3;">';
		$html .= '<input type="button" name="close" value="close" class="form-control modal_close" style="background-color: #f3ebb0;">';
		$html .= '</div>';
		
		
		echo $html;
		
	}
	
	//index.php?route=catalog/order_edit/save&user_token='.$this->session->data['user_token'].'
	public function save(){
		
		$order_id = (int)$this->request->get['order_id'];
		$field = $this->request->get['field'];
		
		$this->load->model('sale/order');
		$this->load->model('localisation/language');

		$data = $this->request->post;
		
		//Сохраняем
		if($this->request->get['operation'] != 'close'){
			
			
			
			if(isset($data['order_description'])){
				foreach ($data['order_description'] as $language_id => $value) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_description SET
									 order_id = '" . (int)$order_id . "', language_id = '" . (int)$language_id . "',
									 $field = '" . $this->db->escape($field) . "'
									 ON DUPLICATE KEY UPDATE $field = '" . $this->db->escape($value[$field]) . "'
									 ");
				}
			}elseif(isset($data['order'])){
				foreach ($data['order'] as $name => $value) {
					if($name == 'shipping_date'){
						$d = explode('/', $value);
						
						$value = date('Y-m-d', strtotime($d[2].'-'.$d[1].'-'.$d[0]));
					}
					
					if($name == 'shipping_method'){
						$method_data = array();
			
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=checkout/shipping_method/getjson');
						$method_data = json_decode(curl_exec($ch), true);
						curl_close($ch);

						$this->db->query("UPDATE " . DB_PREFIX . "order SET
									 shipping_method = '" . $this->db->escape($method_data[$value]['title']) . "',
									 shipping_code = '" . $this->db->escape($value) . "'
									 WHERE order_id = '" . (int)$order_id . "'
									 ");

					}else{					
						$this->db->query("UPDATE " . DB_PREFIX . "order SET
									 $name = '" . $this->db->escape($value) . "'
									 WHERE order_id = '" . (int)$order_id . "'
									 ");
					}
				}
			}
		}
		
		
		//Выводим обновленное поле
		if($field == 'name' OR $field == 'description'){
			
			$data['description'] = $this->model_catalog_order->getProductDescriptions($this->request->get['order_id']);
			
			$value = html_entity_decode($data['description'][$this->config->get('config_language_id')][$field], ENT_QUOTES, 'UTF-8');
			$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
			$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
			
			echo $value;
			
		}elseif(isset($data['order'])){
		
		}	
			
		$data['order_info'] = $this->model_sale_order->getOrder($this->request->get['order_id']);
			
				
		if($field == 'shipping_date'){
			$d = explode('/', $data['order_info'][$field]);
				
			echo $d[0].'/'.$d[1].'/'.$d[2];
		}else{
			echo $data['order_info'][$field];
		}
			
		//Если кнопка на отправку емайла	
		if(isset($this->request->get['btn_name']) AND $this->request->get['btn_name'] == 'save_email'){
			
			$email_html = '';
			
			//Если меняли номер ТТН - отправляем емаил
			if($field == 'shipping_number'){
				
				$email_html = 'Order # ' .$this->request->get['order_id'] . ' maseratinet.com<br>
				Shipping Track Num: ' . $data['order_info']['shipping_number'] . '<br>
				Shipping Method: ' . $data['order_info']['shipping_method'] . '<br>';
			}
			
		}
		
		if($email_html != ''){
			
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
	
			$mail->setTo($data['order_info']['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($data['order_info']['store_name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode(($data['order_info']['store_name'] . ' Order # ' . $data['order_info']['order_id']), ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($email_html);
			$mail->send();
			
		}
	
	
	}
	
}