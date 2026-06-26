<?php
class ControllerCatalogProductEdit extends Controller {
	private $error = array();

	public function update_vendor(){
		
		$post = $this->request->post;
		
		if (isset($this->request->post['selected']) AND isset($this->request->post['vendor'])) {
			
			$vendor = $this->request->post['vendor'];
			
			foreach ($this->request->post['selected'] as $product_id) {
				if((int)$vendor['vendor_id'] == -1 OR $post['operation'] == 'update'){
					$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_to_product WHERE product_id = '" . (int)$product_id . "'");
				}
				
				if((int)$vendor['vendor_id'] > 0){
				
					$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_to_product SET
									 vendor_id = '" . (int)$vendor['vendor_id'] . "',
									 model = '" . $this->db->escape($vendor['model']) . "',
									 sku = '" . $this->db->escape($vendor['sku']) . "',
									 uom = '" . $this->db->escape($vendor['uom']) . "',
									 cost = '" . (float)$vendor['cost'] . "',
									 quantity = '" . (int)$vendor['qty'] . "',
									 status = '" . (int)$vendor['status'] . "',
									 product_id = '" . (int)$product_id . "'
									 ");
				
				}
			}
		}
		
		$this->response->redirect($post['redirect']);
		
	}
	
	public function mark_save() {
		
		$product_id = (int)$this->request->get['product_id'];
		$mark_id = $this->request->get['mark_id'];
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_mark WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_mark SET product_id = '" . (int)$product_id . "', mark_id = '" . (int)$mark_id . "'");
		
		$this->load->model('catalog/mark');
		$mark_info = $this->model_catalog_mark->getMark($mark_id);
	
		echo $mark_info['path'].'&nbsp;&nbsp;&gt;&nbsp;&nbsp;'.$mark_info['name'];
	}
	
	public function category_save() {
		
		$product_id = (int)$this->request->get['product_id'];
		$category_id = $this->request->get['category_id'];
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
		
		$this->load->model('catalog/category');
		$category_info = $this->model_catalog_category->getCategory($category_id);
	
		echo $category_info['path'].'&nbsp;&nbsp;&gt;&nbsp;&nbsp;'.$category_info['name'];
	}
	
	public function get_form(){
		
		if(!isset($this->request->get['field']) OR !isset($this->request->get['product_id'])){
			return false;
		}
		
		$product_id = (int)$this->request->get['product_id'];
		$field = $this->request->get['field'];
		
		$this->load->model('catalog/product');
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$html = '<input type="hidden" name="product_id" value="'.$product_id.'" class="modal_edit">';
		
		$product_fiends = array('name', 'description');
		if(in_array($field, $product_fiends)){
			
			$data['description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
			
			foreach($data['languages'] as $language){
				
				$value = html_entity_decode($data['description'][$language['language_id']][$field], ENT_QUOTES, 'UTF-8');
				$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
				$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
				$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
				
				$html .= '<div class="input-group" style="min-width:300px;"><span class="input-group-addon"><img src="language/' . $language['code'] .'/' . $language['code'] . '.png" title="' . $language['name'] . '"/></span>';
				$html .= '<input type="text" name="product_description[' . $language['language_id'] . ']['.$field.']" value="' . htmlspecialchars($value, ENT_QUOTES) . '" class="form-control modal_edit"/>';
				$html .= '</div>';
				
			}
			$html .= '<div class="input-group" style="min-width:400px;">';
		}
		
		$product_fiends = array('quantity', 'cost', 'price', 'manufacturer_id', 'stock_status_id', 'status', 'uom', 'model', 'sku', 'sort_order', 'weight', 'tax_class_id', 'condition_status_id', 'dimensions');
		if(in_array($field, $product_fiends)){ 
			
			$data['product_info'] = $this->model_catalog_product->getProduct($this->request->get['product_id']);

			$product_fiends = array('quantity', 'price', 'cost', 'model', 'sku', 'sort_order', 'weight');
			if(in_array($field, $product_fiends)){ 
					
				$html .= '<div class="input-group" style="min-width:100px;">';
				$html .= '<input type="text" name="product['.$field.']" value="' . htmlspecialchars($data['product_info'][$field], ENT_QUOTES) . '" class="form-control modal_edit"/>';
				$html .= '</div>';
			}
			
			$product_fiends = array('dimensions');
			if(in_array($field, $product_fiends)){ 
					
				$html .= '<div class="input-group" style="min-width:100px;"><span class="input-group-addon">L: </span>';
				$html .= '<input type="text" name="dimensions[length]" value="' . htmlspecialchars($data['product_info']['length'], ENT_QUOTES) . '" class="form-control modal_edit"/>';
				$html .= '</div>';
				$html .= '<div class="input-group" style="min-width:100px;"><span class="input-group-addon">W: </span>';
				$html .= '<input type="text" name="dimensions[width]" value="' . htmlspecialchars($data['product_info']['width'], ENT_QUOTES) . '" class="form-control modal_edit"/>';
				$html .= '</div>';
				$html .= '<div class="input-group" style="min-width:100px;"><span class="input-group-addon">H: </span>';
				$html .= '<input type="text" name="dimensions[height]" value="' . htmlspecialchars($data['product_info']['height'], ENT_QUOTES) . '" class="form-control modal_edit"/>';
				$html .= '</div>';
				
				$this->load->model('localisation/length_class');
				$list = $this->model_localisation_length_class->getLengthClasses();
				
				$html .= '<div class="input-group" style="min-width:100px;">';
				$html .= '<select name="dimensions[length_class_id]" class="form-control modal_edit">';
					foreach($list as $row){
					$html .= '<option value="'.$row['length_class_id'].'"';
					if($row['length_class_id'] == $data['product_info']['length_class_id']) $html .= ' selected';
					$html .= '>'.(isset($row['title']) ? $row['title'] : $row['name']).'</option>';
					}
				$html .= '</select></div>';
			}
			
			
			$product_fiends = array('manufacturer_id', 'stock_status_id', 'status', 'tax_class_id', 'condition_status_id');
			if(in_array($field, $product_fiends)){
				
				if($field == 'manufacturer_id'){
					$this->load->model('catalog/manufacturer');
					$list = $this->model_catalog_manufacturer->getManufacturers();
				}elseif($field == 'status'){
					$list = array(
								  array('status' => 0, 'name' => 'Disabled'),
								  array('status' => 1, 'name' => 'Enabled'),
								);
				}elseif($field == 'stock_status_id'){
					$this->load->model('localisation/stock_status');
					$list = $this->model_localisation_stock_status->getStockStatuses();
				}elseif($field == 'tax_class_id'){
					$this->load->model('localisation/tax_class');
					$list = $this->model_localisation_tax_class->getTaxClasses();
				}elseif($field == 'condition_status_id'){
					$this->load->model('localisation/condition_status');
					$list = $this->model_localisation_condition_status->getConditionStatuses();
				}
			
				$html .= '<div class="input-group" style="min-width:100px;">';
				$html .= '<select name="product['.$field.']" class="form-control modal_edit">';
					if($field == 'manufacturer_id'){
						$sel = ($data['product_info'][$field] == 0) ? ' selected' : '';
						$html .= '<option value="0"'.$sel.'>--- None ---</option>';
					}
					foreach($list as $row){
					$html .= '<option value="'.$row[$field].'"';
					if($row[$field] == $data['product_info'][$field]) $html .= ' selected';
					$html .= '>'.(isset($row['title']) ? $row['title'] : $row['name']).'</option>';
					}
				$html .= '</select></div>';
			}
			
			if($field == 'uom'){
				$html .= '<div class="input-group" style="min-width:100px;">';
				$html .= '<input type="text" name="product[uom]" value="' . htmlspecialchars($data['product_info']['uom'], ENT_QUOTES) . '" class="form-control modal_edit"/>';
				$html .= '</div>';
			}			
			$html .= '<div class="input-group" style="min-width:100px;">';
		}
		
		if($field == 'vendors'){ 
		
			$this->load->model('vendor/vendor');
			$data['vendors'] = $this->model_vendor_vendor->getVendors();
		
			$data['product_vendors'] = $this->model_catalog_product->getProductVendors($this->request->get['product_id']);
			$html = '<div class="input-group vendors_edit" style="min-width:100px;">
							<table class="table table-bordered"><thead><tr>
								<td style="width: 150px;">vendor</td>
								<td style="width: 70px;">model</td>
								<td style="width: 70px;">sku</td>
								<td style="width: 70px;">uom</td>
								<td style="width: 70px;">cost</td>
								<td style="width: 70px;">qty</td>
								</tr></thead><tbody>';
			
			foreach($data['product_vendors'] as $index => $vendor){ 
				
				$html .= '<tr><td><select name="vendors['.$index.'][vendor_id]" class="form-control modal_edit">';
					$html .= '<option value="0">delete</option>';
					foreach($data['vendors'] as $row){
					$html .= '<option value="'.$row['vendor_id'].'"';
					if($row['vendor_id'] == $vendor['vendor_id']) $html .= ' selected';
					$html .= '>' . $row['name'] . '</option>';
					}
				$html .= '</select></td>';
				$html .= '<td><input type="text" name="vendors['.$index.'][model]" value="'.$vendor['model'] .'" class="form-control"></td>';
				$html .= '<td><input type="text" name="vendors['.$index.'][sku]" value="'.$vendor['sku'] .'" class="form-control"></td>';
				$html .= '<td><input type="text" name="vendors['.$index.'][uom]" value="'.$vendor['uom'] .'" class="form-control"></td>';
				$html .= '<td><input type="text" name="vendors['.$index.'][cost]" value="'.$vendor['cost'] .'" class="form-control"></td>';
				$html .= '<td><input type="text" name="vendors['.$index.'][qty]" value="'.$vendor['quantity'] .'" class="form-control">';
				$html .= '<input type="hidden" name="vendors['.$index.'][status]" value="'.$vendor['status'] .'" class="form-control"></td>';
				$html .= '</tr>';
			}
			
			$html .= '<tr><td><select name="vendors[10000][vendor_id]" class="form-control modal_edit">';
				$html .= '<option value="0">add new</option>';
				foreach($data['vendors'] as $row){
					$html .= '<option value="'.$row['vendor_id'].'">' . $row['name'] . '</option>';
				}
			$html .= '</select></td>';
			$html .= '<td><input type="text" name="vendors[10000][model]" value="" class="form-control"></td>';
			$html .= '<td><input type="text" name="vendors[10000][sku]" value="" class="form-control"></td>';
			$html .= '<td><input type="text" name="vendors[10000][uom]" value="" class="form-control"></td>';
			$html .= '<td><input type="text" name="vendors[10000][cost]" value="0" class="form-control"></td>';
			$html .= '<td><input type="text" name="vendors[10000][qty]" value="0" class="form-control">';
			$html .= '<input type="hidden" name="vendors[10000][status]" value="1"></td>';
			$html .= '</tr></tbody></table>';
			
				$html .= '<div class="input-group " style="min-width:100px;">';
				$html .= '<input type="button" name="save" value="save" class="form-control modal_save" style="background-color: #b0bef3;">';
				$html .= '<input type="button" name="close" value="close" class="form-control modal_close" style="background-color: #f3ebb0;">';
				$html .= '</div>';
			$html .= '</div>';
		
		}
		
		
		if($field != 'vendors'){
			$html .= '<input type="button" name="save" value="save" class="form-control modal_save" style="background-color: #b0bef3;">';
			$html .= '<input type="button" name="close" value="close" class="form-control modal_close" style="background-color: #f3ebb0;">';
			$html .= '</div>';
		}
		
		
		echo $html;
		
	}
	
	//index.php?route=catalog/product_edit/save&user_token='.$this->session->data['user_token'].'
	public function save(){
		
		$product_id = (int)$this->request->get['product_id'];
		$field = $this->request->get['field'];
		
		$this->load->model('catalog/product');
		$this->load->model('localisation/language');

		
		//Сохраняем
		if($this->request->get['operation'] != 'close'){
			
			$data = $this->request->post;
			
			if(isset($data['product_description'])){
				foreach ($data['product_description'] as $language_id => $value) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET
									 product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "',
									 $field = '" . $this->db->escape($field) . "'
									 ON DUPLICATE KEY UPDATE $field = '" . $this->db->escape($value[$field]) . "'
									 ");
				}
			}elseif(isset($data['dimensions'])){
				foreach ($data['dimensions'] as $field => $value) {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET
									 $field = '" . $this->db->escape($value) . "'
									 WHERE product_id = '" . (int)$product_id . "'");
				}
			}elseif(isset($data['product'])){
				foreach ($data['product'] as $name => $value) {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET
									 $name = '" . $this->db->escape($value) . "'
									 WHERE product_id = '" . (int)$product_id . "'
									 ");
				}
			}elseif(isset($data['vendors'])){
				
				$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_to_product WHERE product_id = '" . (int)$product_id . "'");
				
				foreach ($data['vendors'] as $index => $row) {
					if((int)$row['vendor_id'] > 0){
						$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_to_product SET
									 vendor_id = '" . (int)$row['vendor_id'] . "',
									 model = '" . $this->db->escape($row['model']) . "',
									 sku = '" . $this->db->escape($row['sku']) . "',
									 uom = '" . $this->db->escape($row['uom']) . "',
									 cost = '" . (float)$row['cost'] . "',
									 quantity = '" . (int)$row['qty'] . "',
									 status = '" . (int)$row['status'] . "',
									 product_id = '" . (int)$product_id . "'
									 ");
					}
				}
			}
		}
		
		
		//Выводим обновленное поле
		if($field == 'name' OR $field == 'description'){
			
			$data['description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
			
			$value = html_entity_decode($data['description'][$this->config->get('config_language_id')][$field], ENT_QUOTES, 'UTF-8');
			$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
			$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
			
			echo $value;
			
		}elseif($field == 'vendors'){

			$data['product_vendors'] = $this->model_catalog_product->getProductVendors($this->request->get['product_id']);
			
			foreach($data['product_vendors'] as $row){
				echo  $row['name'] . '<br>';
			}
			
		}elseif($field == 'dimensions'){

			$data['product_info'] = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			$this->load->model('localisation/length_class');
			$length_classes = $this->model_localisation_length_class->getLengthClasses();
			
			foreach($length_classes as $row){
				$data['length_classes'][$row['length_class_id']] = $row;
			}
		
			echo (int)$data['product_info']['length'].' x '.(int)$data['product_info']['width'].' x '.(int)$data['product_info']['height'].'<br>'.$data['length_classes'][(int)$data['product_info']['length_class_id']]['unit'];
			
		}elseif($field == 'tax_class_id'){

			$data['product_info'] = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			$this->load->model('localisation/tax_class');
			$info = $this->model_localisation_tax_class->getTaxClass($data['product_info'][$field]);
		
			echo $info['title'];
			
		}elseif($field == 'condition_status_id'){

			$data['product_info'] = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			$this->load->model('localisation/condition_status');
			$info = $this->model_localisation_condition_status->getConditionStatus($data['product_info'][$field]);
			
			echo $info['name'];
			
		}elseif($field == 'stock_status_id'){

			$data['product_info'] = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			$this->load->model('localisation/stock_status');
			$info = $this->model_localisation_stock_status->getStockStatus($data['product_info'][$field]);
			
			echo $info['name'];
			
		}elseif($field == 'uom'){

			$data['product_info'] = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			echo $data['product_info']['uom'];
			
		}elseif(isset($data['product'])){
			
			$data['product_info'] = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			foreach ($data['product'] as $name => $value) {
				echo $data['product_info'][$name];
			}
			
		}

	}
	
}