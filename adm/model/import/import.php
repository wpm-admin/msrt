<?php
class ModelImportImport extends Model {
	public function getMasfProducts($limit) {
		
		$res = $this->db2->query("SELECT * FROM  product LIMIT " . $limit);
		
		return $res->rows;
	}


	public function	getProductOnSkuModel($sku, $model){
		/*
		$res = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE
								model IN ('".$this->db->escape($sku)."','".$this->db->escape($model)."') OR
								sku IN ('".$this->db->escape($sku)."','".$this->db->escape($model)."') LIMIT 1");
		*/
		$res = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE
								model IN ('".$this->db->escape($model)."') OR
								sku IN ('".$this->db->escape($sku)."') LIMIT 1");
		
		if($res->num_rows){
			return (int)$res->row['product_id'];
		}else{
			return false;
		}
	}
	
	public function	getCustomerOnEmailOrPhone($email, $phone){

	
		if(trim($phone) == '') $phone = '***';
		if(trim($email) == '') $email = '***';
	
		$res = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE
								telephone LIKE '%".$this->db->escape($phone)."' OR
								email LIKE '".$this->db->escape($email)."' LIMIT 1");
		
		if($res->num_rows){
			return (int)$res->row['customer_id'];
		}else{
			return false;
		}
	}
	
	public function	getStockStatusIdOnName($name){
		$res = $this->db->query("SELECT stock_status_id FROM " . DB_PREFIX . "stock_status WHERE LOWER(name) LIKE '".$this->db->escape(utf8_strtolower($name))."' LIMIT 1");
		
		if($res->num_rows){
			return (int)$res->row['stock_status_id'];
		}else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET `name` = '".$this->db->escape($name)."', language_id='2'");
			
			$stock_status_id = $this->db->getLastId();
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET `name` = '".$this->db->escape($name)."', language_id='4', stock_status_id='".$stock_status_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET `name` = '".$this->db->escape($name)."', language_id='5', stock_status_id='".$stock_status_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET `name` = '".$this->db->escape($name)."', language_id='3', stock_status_id='".$stock_status_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET `name` = '".$this->db->escape($name)."', language_id='1', stock_status_id='".$stock_status_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET `name` = '".$this->db->escape($name)."', language_id='6', stock_status_id='".$stock_status_id."'");
			
			return $stock_status_id;
		}
	}
	
	public function	getConditionStatusIdOnName($name){
		$res = $this->db->query("SELECT condition_status_id FROM " . DB_PREFIX . "condition_status WHERE LOWER(name) LIKE '".$this->db->escape(utf8_strtolower($name))."' LIMIT 1");
		
		if($res->num_rows){
			return (int)$res->row['condition_status_id'];
		}else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "condition_status SET `name` = '".$this->db->escape($name)."', language_id='2'");
			
			$condition_status_id = $this->db->getLastId();
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "condition_status SET `name` = '".$this->db->escape($name)."', language_id='4', condition_status_id='".$condition_status_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "condition_status SET `name` = '".$this->db->escape($name)."', language_id='5', condition_status_id='".$condition_status_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "condition_status SET `name` = '".$this->db->escape($name)."', language_id='3', condition_status_id='".$condition_status_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "condition_status SET `name` = '".$this->db->escape($name)."', language_id='1', condition_status_id='".$condition_status_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "condition_status SET `name` = '".$this->db->escape($name)."', language_id='6', condition_status_id='".$condition_status_id."'");
			
			return $condition_status_id;
		}
	}
	
	public function	getWeightUOMIdOnName($name){
		$res = $this->db->query("SELECT weight_class_id FROM " . DB_PREFIX . "weight_class_description WHERE
								LOWER(title) LIKE '".$this->db->escape(utf8_strtolower($name))."' OR
								LOWER(unit) LIKE '".$this->db->escape(utf8_strtolower($name))."'
								LIMIT 1");
		
		if($res->num_rows){
			return (int)$res->row['weight_class_id'];
		}else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class SET `value` = '1'");
			
			$weight_class_id = $this->db->getLastId();
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='2', weight_class_id='".$weight_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='4', weight_class_id='".$weight_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='5', weight_class_id='".$weight_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='3', weight_class_id='".$weight_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='1', weight_class_id='".$weight_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='6', weight_class_id='".$weight_class_id."'");
			
			return $weight_class_id;
		}
	}
	
	public function	getLengthUOMIdOnName($name){
		$res = $this->db->query("SELECT length_class_id FROM " . DB_PREFIX . "length_class_description WHERE
								LOWER(title) LIKE '".$this->db->escape(utf8_strtolower($name))."' OR
								LOWER(unit) LIKE '".$this->db->escape(utf8_strtolower($name))."'
								LIMIT 1");
		
		if($res->num_rows){
			return (int)$res->row['length_class_id'];
		}else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "length_class SET `value` = '1'");
			
			$length_class_id = $this->db->getLastId();
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='2', length_class_id='".$length_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='4', length_class_id='".$length_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='5', length_class_id='".$length_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='3', length_class_id='".$length_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='1', length_class_id='".$length_class_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET `title` = '".$this->db->escape($name)."', `unit` = '".$this->db->escape($name)."', language_id='6', length_class_id='".$length_class_id."'");
			
			return $length_class_id;
		}
	}
	
	public function	getTaxClassIdOnName($name){
		$res = $this->db->query("SELECT tax_class_id FROM " . DB_PREFIX . "tax_class WHERE
								LOWER(title) LIKE '".$this->db->escape(utf8_strtolower($name))."' OR
								LOWER(description) LIKE '".$this->db->escape(utf8_strtolower($name))."'
								LIMIT 1");
		
		if($res->num_rows){
			return (int)$res->row['tax_class_id'];
		}else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "tax_class SET
							 `description`='".$this->db->escape($name)."',
							 `title`='".$this->db->escape($name)."',
							 date_added= NOW(),
							 date_modified= NOW()");
			
			$tax_class_id = $this->db->getLastId();
				
			return $tax_class_id;
		}
	}
	
	public function	getManuracturerIdOnName($name){
		$res = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer WHERE LOWER(name) LIKE '".$this->db->escape(utf8_strtolower($name))."' LIMIT 1");
		
		if($res->num_rows){
			return (int)$res->row['manufacturer_id'];
		}else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET `name` = '".$this->db->escape($name)."'");
			
			$manufacturer_id = $this->db->getLastId();
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET `store_id` = '0', manufacturer_id='".(int)$manufacturer_id."'");
				
			return $manufacturer_id;
		}
	}
	

	public function	getDiagramId($name, $row){
		
		$res = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p
										LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
									WHERE
								LOWER(pd.name) LIKE '".$this->db->escape(utf8_strtolower($name))."'
								AND LOWER(p.modeli) LIKE '".$this->db->escape(utf8_strtolower($row['Model']))."'
								LIMIT 1");
		
		//Если нет такой диаграммы с моделью - поищем ее без модели
		if(!$res->num_rows){
			$res = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p
									LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
									WHERE
									LOWER(pd.name) LIKE '".$this->db->escape(utf8_strtolower($name))."'
									LIMIT 1");
		}
			
			
		if($res->num_rows){
			$product_id = $res->row['product_id'];
			
			/*
			$row['Italian'] = $row['English'] = $row['French'] = $row['German'] = $row['Russian'] = $row['Spanish'] = $name;
			$this->updateOCProductDescription($product_id, $row);
			$row['is_diagram'] = true;
			$row['sort_order'] = -100;
			$this->updateOCProduct($product_id, $row);
			$this->updateOCProductMarkAndModel($product_id, $row);
						
			$this->updateOCProductCategory($product_id, $row);
			
			$this->updateOCProductKeywords($product_id, $row);
			*/
			return $product_id;
		}else{
			
			$row['CodeManual'] = substr($name,0, 63);
			
			$row['Italian'] = $row['English'] = $row['French'] = $row['German'] = $row['Russian'] = $row['Spanish'] = $name;
			$row['is_diagram'] = true;
			$row['sort_order'] = -100;
			$row['Availability'] = 'In Stock';
			$row['Qty'] = 1000;
			
			$product_id = $this->addProductShort($row);
			
			$this->updateOCProductDescription($product_id, $row);
			
			$this->updateOCProduct($product_id, $row);
						
			$this->updateOCProductMarkAndModel($product_id, $row);
						
			$this->updateOCProductCategory($product_id, $row);
			
			$this->updateOCProductKeywords($product_id, $row);
				
			
			return $product_id;
		}
	}
	
	public function	getOCProduct($num){
		$res = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE model='".$this->db->escape($num)."' LIMIT 1");
		
		if($res->num_rows){
			return $res->row['product_id'];
		}else{
			return false;
		}
	}
	
	public function addProductShort($data){
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET
						 model = '" . $this->db->escape($data['CodeManual']) . "',
						 status = '0',
						 sort_order = '0',
						 date_added = NOW(),
						 date_modified = NOW()");
		
		$product_id = $this->db->getLastId();
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '0'");
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '0', layout_id = '0'");
		
		return $product_id;
						 
	}
	
	public function addCustomerShort($data){
		
		if(!isset($data['Email'])) $data['Email'] = '';
		if(!isset($data['Phone'])) $data['Phone'] = '';
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET
						 email = '" . $this->db->escape($data['Email']) . "',
						 telephone = '" . $this->db->escape($data['Phone']) . "',
						 status = '1',
						 date_added = NOW()");
		
		$customer_id = $this->db->getLastId();
		
		return $customer_id;
						 
	}
	public function updateOCProductDescription($product_id, $data){
				
		$description = (isset($data['Product Description']) AND trim($data['Product Description']) != '') ? $data['Product Description'] : false;
				
		if(isset($data['Italian'])) $this->_updateDescription($product_id, 4, $data['Italian'], $description);
		if(isset($data['English'])) $this->_updateDescription($product_id, 2, $data['English'], $description);
		if(isset($data['French'])) 	$this->_updateDescription($product_id, 5, $data['French'], $description);
		if(isset($data['German'])) 	$this->_updateDescription($product_id, 3, $data['German'], $description);
		if(isset($data['Russian'])) $this->_updateDescription($product_id, 1, $data['Russian'], $description);
		if(isset($data['Spanish'])) $this->_updateDescription($product_id, 6, $data['Spanish'], $description); //еспанский пропал

	}
	
	public function updateOCProductKeywords($product_id, $data = array()){
		
		//if(!isset($data['CodeManual'])) return false;
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query`= 'product_id=" . (int)$product_id . "'");
		
		
		$product_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE `product_id`='" . (int)$product_id . "' LIMIT 1");
		$product_description = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description
													WHERE `product_id`='" . (int)$product_id . "'
													AND language_id='2' LIMIT 1");
		
		if(!$product_description->num_rows) return false;
		
		//$keyword = $product_info->row['mark'].'-'.$product_info->row['modeli'].'-'.$product_description->row['name'];
		$keyword = $product_id.'-'.$product_description->row['name'];
		
		$keyword = trim($keyword);
		$keyword = str_replace(array("*","\n",'&',',',';','.','/','(',')',' ','#'), '-', $keyword);
		$keyword = str_replace(array('"',"'"), '', $keyword);
		$keyword = str_replace('--', '-', $keyword);
		$keyword = str_replace('--', '-', $keyword);
		$keyword = strtolower($keyword);
		$keyword = trim($keyword, '-');
		
		$languages_res = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
		$languages = $languages_res->rows;
		
		foreach ($languages as $language) {
			
			$sql = "INSERT INTO " . DB_PREFIX . "seo_url SET
						`query` = 'product_id=" . (int)$product_id . "',
						`keyword` = '".$this->db->escape($keyword)."',
						`store_id` = '0',
						language_id = '".(int)$language['language_id']."'
						";
			//echo '<br>'.$sql;
			$this->db->query($sql);
		}
	}
	
	private function _getCategoryIdOnKeyword($name){
		
		$res = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category_description WHERE `name` LIKE '".$this->db->escape(utf8_strtolower($name))."' LIMIT 1"); 
	
		if($res->num_rows){
			return (int)$res->row['category_id'];
		}else{
			
			$keyword = utf8_strtolower($name);
			$keyword = trim($keyword);
			$keyword = str_replace(array("\n",'&',',',';','.','/',' ','#'), '-', $keyword);
			$keyword = str_replace(array('"',"'"), '', $keyword);
			$keyword = str_replace('--', '-', $keyword);
			$keyword = strtolower($keyword);
			$keyword = trim($keyword, '-');
	
			$res = $this->db->query("SELECT `query` FROM " . DB_PREFIX . "seo_url WHERE `keyword` LIKE '".$this->db->escape($keyword)."' LIMIT 1"); 
	
			if($res->num_rows){
				$rows = explode('=', $res->row['query']);
				
				if($rows[0] == 'category_id'){
					return (int)$rows[1];
				}else{
					echo '<br>Find keyword, but not is Category - <b>'.$name.'</b>';
					return false;
				}
				
			}else{
			
				echo '<br>Category not find - <b>'.$name.'</b>';
				return false;
	
			}
		}
	}
	public function addToDiagramm($diagram_product_id, $product_id, $num, $quantity){
		
		if((int)$diagram_product_id > 0 AND (int)$product_id > 0 AND (int)$num > 0){
		
			$sql = "INSERT INTO " . DB_PREFIX . "product_coordinate SET
						`product_id` = '" . (int)$diagram_product_id . "',
						`list_product_id` = '".(int)$product_id."',
						`quantity` = '".(int)$quantity."',
						`num` = '".$this->db->escape($num)."'
						ON DUPLICATE KEY UPDATE `num` = '".$this->db->escape($num)."'
						";
		
			$this->db->query($sql);
		}
	}
	
	public function getWeightClasses(){
		
		$query = $this->db->query("SELECT *, `title` AS `name` FROM " . DB_PREFIX . "weight_class_description WHERE language_id='2' ORDER BY `title`");

		$return[0] = array(
						'weight_class_id' => 0,
						'name' => '',
						'unit' => ''
						);
		
		foreach ($query->rows as $result) {
			$return[$result['weight_class_id']] = $result;
		}

		return $return;

	}
	
	public function getLengthClasses(){
		
		$query = $this->db->query("SELECT *, `title` AS `name` FROM " . DB_PREFIX . "length_class_description WHERE language_id='2' ORDER BY `title`");

		$return[0] = array(
						'length_class_id' => 0,
						'name' => '',
						'unit' => ''
						);
		
		foreach ($query->rows as $result) {
			$return[$result['length_class_id']] = $result;
		}

		return $return;

	}
	
	public function getTaxClasses(){
		
		$query = $this->db->query("SELECT *, `title` AS `name` FROM " . DB_PREFIX . "tax_class ORDER BY `title`");

		$return[0] = array(
						'tax_class_id' => 0,
						'name' => '',
						'unit' => ''
						);
		
		foreach ($query->rows as $result) {
			$return[$result['tax_class_id']] = $result;
		}

		return $return;

	}
	
	public function _getStockStatuses(){
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id='2' ORDER BY `name`");
		
		$return[0] = array(
						'stock_status_id' => 0,
						'name' => '',
						);
		
		foreach ($query->rows as $result) {
			$return[$result['stock_status_id']] = $result;
		}

		return $return;

	}
	
	public function _getConditionStatuses(){
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "condition_status WHERE language_id='2' ORDER BY `name`");
		
		$return[0] = array(
						'condition_status_id' => 0,
						'name' => '',
						);
		
		foreach ($query->rows as $result) {
			$return[$result['condition_status_id']] = $result;
		}

		return $return;

	}
	
	public function updateCustomer($customer_id, $data){
		
		$CountryInfo = false;
		
		if(isset($data['Country'])){
			$res = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "country WHERE
											LOWER(`name`) LIKE '".utf8_strtolower($this->db->escape(trim($data['Country'])))."%' OR
											LOWER(`trans_name`) LIKE '".utf8_strtolower($this->db->escape(trim($data['Country'])))."'
											LIMIT 1
											");
			
			$CountryInfo = $res->row;
			
			if($CountryInfo){
				
				$tel_pref = str_replace('+','',$CountryInfo['tel_pref']);
				
				$tel_tmp = substr($data['Phone'], 0, strlen($tel_pref));
				
				if($tel_pref == $tel_tmp){
					$data['Phone'] = '+'.$data['Phone'];
				}else{
					$data['Phone'] = $CountryInfo['tel_pref'].$data['Phone'];
				}
				
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET `telephone` = '".$this->db->escape($data['Phone'])."' WHERE `customer_id`='" . (int)$customer_id . "'");
				
			}
			
		}

		if(isset($data['Name'])){
			
			$name = explode(' ', $data['Name']);
			$firstname = $name[0];
			unset($name[0]);
			$lastname = isset($name) ? implode(' ', $name) : '';
			
			
			
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET `firstname` = '".$this->db->escape($firstname)."', `lastname` = '".$this->db->escape($lastname)."' WHERE `customer_id`='" . (int)$customer_id . "'");
					
			//Если нет колонки имя то тупо сохранять адреса
			if(isset($data['Address'])){
				
				//Определим зону
				$zone_id = 0;
				if(isset($data['State']) AND $CountryInfo){
					
					$zone = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE `country_id` = '".$CountryInfo['country_id']."' AND `code`='" . $this->db->escape($data['State']) . "' LIMIT 1");
				
					if($zone->num_rows){
						$zone_id = $zone->row['zone_id'];
					}
					
				}
				
				
				$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE `customer_id`='" . (int)$customer_id . "'");
				
				$sql = "INSERT INTO " . DB_PREFIX . "address SET
									`customer_id` = '" . (int)$customer_id . "',
									`firstname` = '".$this->db->escape($firstname)."',
									`lastname` = '".$this->db->escape($lastname)."',
									`address_1` = '".$this->db->escape($data['Address'])."',
									`address_2` = '".$this->db->escape(isset($data['State']) ? $data['State'] : '')."',
									`city` = '".$this->db->escape(isset($data['City']) ? $data['City'] : '')."',
									`postcode` = '".$this->db->escape(isset($data['Zip Code']) ? $data['Zip Code'] : '')."',
									`country_id` = '".(($CountryInfo) ? $CountryInfo['country_id'] : 0)."',
									`zone_id` = '".(int)$zone_id."'
									";
	
	
				$this->db->query($sql);
				
				$address_id = $this->db->getLastId();
				
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET `address_id` = '".$address_id."' WHERE `customer_id`='" . (int)$customer_id . "'");
			
			}
		
		}
		
		if(isset($data['Account number'])){
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET `account_number` = '".$this->db->escape($data['Account number'])."' WHERE `customer_id`='" . (int)$customer_id . "'");
		}			
			
		if(isset($data['Shipping'])){
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET `shipping` = '".$this->db->escape($data['Shipping'])."' WHERE `customer_id`='" . (int)$customer_id . "'");
		}			
			
		if(isset($data['Group'])){
			$customer_group_id = $this->getCustomerGroupId($data['Group']);
			
			if($customer_group_id < 1) $customer_group_id = 1;
			
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET `customer_group_id` = '".(int)$customer_group_id."' WHERE `customer_id`='" . (int)$customer_id . "'");
	
		}else{
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET `customer_group_id` = '1' WHERE `customer_id`='" . (int)$customer_id . "'");
		}
		
		if(isset($data['Model']) OR isset($data['Year']) OR isset($data['VIN'])){
		
			if(isset($data['Model'])){
				$model_info = $this->getModelInfo($data['Model']); 
			}
		
			$sql = "SELECT garage_id FROM " . DB_PREFIX . "garage WHERE customer_id = '".(int)$customer_id."' ";
			
			if($model_info){
				$sql .= " AND mark='".$model_info['mark_id']."'";
			}
			
			if($data['Year']){
				$sql .= " AND year='".$data['Year']."'";
			}
			
			if($data['VIN']){
				$sql .= " AND LOWER(`vin`) LIKE '".utf8_strtolower($this->db->escape($data['VIN']))."' ";
			}
			
			$res = $this->db->query($sql);
			
			if(!$res->num_rows){

				$sql = "INSERT INTO " . DB_PREFIX . "garage SET customer_id = '".(int)$customer_id."', status=1 ";
				
				if($model_info){
					$sql .= ", modeli='".$model_info['parent_id']."'";
					$sql .= ", mark='".$model_info['mark_id']."'";
				}
				
				if($data['Year']){
					$sql .= ", year='".$data['Year']."'";
				}
				
				if($data['VIN']){
					$sql .= ", `vin` = '".utf8_strtolower($this->db->escape($data['VIN']))."' ";
				}
				$this->db->query($sql);

			}else{

				$sql = "UPDATE " . DB_PREFIX . "garage SET customer_id = '".(int)$customer_id."' ";
				
				if($model_info){
					$sql .= ", modeli='".$model_info['parent_id']."'";
					$sql .= ", mark='".$model_info['mark_id']."'";
				}
				
				if($data['Year']){
					$sql .= ", year='".$data['Year']."'";
				}
				
				if($data['VIN']){
					$sql .= ", `vin` = '".utf8_strtolower($this->db->escape($data['VIN']))."' ";
				}
				
				$sql .= " WHERE garage_id='".$res->row['garage_id']."'";
				
				$this->db->query($sql);
				
			}
		
		}

	}
	
	public function getModelInfo($name){
		$res = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark_description md
								  LEFT JOIN " . DB_PREFIX . "mark m ON (md.mark_id = m.mark_id)
								  WHERE LOWER(`name`) LIKE '".utf8_strtolower($this->db->escape($name))."' LIMIT 1");
		
		if(!$res->num_rows){
			return false;
		}
		
		return $res->row;
		
	}
	
	public function getCustomerGroupId($name){
		
		$res = $this->db->query("SELECT customer_group_id FROM " . DB_PREFIX . "customer_group_description WHERE
								 LOWER(`name`) LIKE '".utf8_strtolower($this->db->escape($name))."'
								 LIMIT 1");
	
	
		if($res->num_rows){
			return (int)$res->row['customer_group_id'];
		}
	
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_group SET `approval` = '0'");
		
		$customer_group_id = $this->db->getLastId();
		
		$name = ucfirst(utf8_strtolower($this->db->escape($name)));
		
		$languages = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language");
	
		foreach($languages->rows as $lang){
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_group_description SET
							 `language_id` = '".$lang['language_id']."',
							 `customer_group_id` = '".(int)$customer_group_id."',
							 `name` = '".$name."'");
		}
		
		return $customer_group_id;
	
	}
	
	public function updateOCProduct($product_id, $data){
		
		
		if(isset($data['Product Number'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `sku` = '".$this->db->escape($data['Product Number'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Qty'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `quantity` = '".(int)$data['Qty']."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Marque'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `mark` = '".$this->db->escape($data['Marque'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Model'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `modeli` = '".$this->db->escape($data['Model'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Year'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `year` = '".$this->db->escape($data['Year'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Diagram'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `diagram_name` = '".$this->db->escape($data['Diagram'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['SubCategory (Alias)'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `sub_category` = '".$this->db->escape($data['SubCategory (Alias)'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['is_diagram'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `diagram` = '1' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Price'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `price` = '".(float)$data['Price']."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Availability'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `stock_status_id` = '".$this->getStockStatusIdOnName($data['Availability'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Condition'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `condition_status_id` = '".$this->getConditionStatusIdOnName($data['Condition'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Manufacturer'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `manufacturer_id` = '".$this->getManuracturerIdOnName($data['Manufacturer'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['UOM'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `uom` = '".$this->db->escape($data['UOM'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Location'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `location` = '".$this->db->escape($data['Location'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Weight'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `weight` = '".$this->db->escape($data['Weight'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['WeightUOM'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `weight_class_id` = '".$this->getWeightUOMIdOnName($data['WeightUOM'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Length'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `length` = '".$this->db->escape($data['Length'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Width'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `width` = '".$this->db->escape($data['Width'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Height'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `height` = '".$this->db->escape($data['Height'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['LengthUOM'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `length_class_id` = '".$this->getLengthUOMIdOnName($data['LengthUOM'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['TaxClass'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `tax_class_id` = '".$this->getTaxClassIdOnName($data['TaxClass'])."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['sort_order'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `sort_order` = '".(int)$data['sort_order']."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Cost'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `cost` = '".(float)$data['Cost']."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		if(isset($data['Status'])){
			
			if(strtolower($data['Status']) == 'enabled'){
				$data['Status'] = 1;
			}else{
				$data['Status'] = 0;
			}
			
			$this->db->query("UPDATE " . DB_PREFIX . "product SET `status` = '".(int)$data['Status']."' WHERE `product_id`='" . (int)$product_id . "'");
		}			
		
		if(isset($data['Special'])){	
			$special = (float)$data['Special'];
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special 
							WHERE `product_id`='" . (int)$product_id . "'");
			
			if($special > 0){
				
				$sql = "INSERT INTO " . DB_PREFIX . "product_special SET
							`product_id` = '" . (int)$product_id . "',
							`customer_group_id`='1',
							`priority`='1',
							`price` = '".(float)$special."'
							";
				//echo '<br>'.$sql;
				$this->db->query($sql);
			}
		}
	}

	public function updateOCProductCategory($product_id, $data){
		
		if(!isset($data['Category (Alias)'])) return false;
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		$cates = explode(';', $data['Category (Alias)']);
		
		foreach($cates as $cat){
			$category_id = $this->_getCategoryIdOnKeyword(trim($cat));
			//$sub_category_id = $this->_getCategoryIdOnKeyword($data['subcateg']);
			
			if($category_id){
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET
							 product_id = '" . (int)$product_id . "',
							 category_id = '" . (int)$category_id . "'
							 ");
			}
		}
	}

	public function getCategoryKeywordOnId($category_id, $key = ''){
		
		if($key == 'parent'){
			$category = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id='".(int)$category_id."' LIMIT 1");
			
			if($category->num_rows){
				$category_id = $category->row['parent_id'];
			}
		}
		
		$category = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` LIKE 'category_id=".(int)$category_id."' LIMIT 1"); 
		$category_info = $category->row;
	
		
		if(isset($category_info['keyword'])){
			return $category_info['keyword'];
		}else{
			return false;
		}

	}
	
	private function _updateDescription($product_id, $lang_id, $name, $description = false){
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE
							product_id = '" . (int)$product_id . "' AND
							language_id = '" . (int)$lang_id . "'");
		
		if(!$description){
			$description = $name;
		}

		if($description == 'delete_description'){
			$description = '';
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET
					 product_id = '" . (int)$product_id . "',
					 language_id = '" . (int)$lang_id . "',
					 name = '" . trim($this->db->escape(htmlspecialchars($name,ENT_QUOTES))) . "',
					 description = '" . trim($this->db->escape($description)) . "',
					 tag = '" . trim($this->db->escape($name)) . "',
					 meta_title = '" . trim($this->db->escape($name)) . "',
					 meta_description = '" . trim($this->db->escape(strip_tags($description))) . "',
					 meta_keyword = '" . trim($this->db->escape($name)) . "'");

					 
	}
	
	public function addOCProduct($data){
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET
						 model = '" . $this->db->escape($data['num']) . "',
						 sku = '" . $this->db->escape($data['num']) . "',
						 quantity = '" . (int)$data['quantity'] . "',
						 minimum = '1',
						 price = '" . (float)$data['price'] . "',
						 status = '" . (int)$data['status'] . "',
						 sort_order = '0',
						 date_added = NOW(),
						 date_modified = '".date('Y-m-d H:i:s', strtotime($data['dateLastModified']))."'");
		
		$product_id = $this->db->getLastId();
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '0'");
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '0', layout_id = '0'");
		
		return $product_id;
						 
	}
	
	public function updateOCProductImage($product_id, $image){
	
		if($image!='none'){
			$image_a = explode('/', $image);
		$image_name = array_pop($image_a);
	
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id =  '".(int)$product_id."' LIMIT 1");
		
		if($query->num_rows == 0){
			return false;
		}
		
		$image_old = $query->row['image'];
		
		//Если картинки нет или не совпадает имя - то скачаем и заменим ее
		if($image_old == '' OR $image_old != 'import/' . $image_name){
			
			if(file_put_contents(DIR_IMAGE . 'import/' . $image_name, file_get_contents($image))){
				$this->db->query("UPDATE " . DB_PREFIX . "product SET image = 'import/".$this->db->escape($image_name)."' WHERE product_id =  '".(int)$product_id."' LIMIT 1");	
			}
			
		}
		}
		else{
			$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id =  '".(int)$product_id."' LIMIT 1");
			if($query->num_rows == 0){
				return false;
			}
			$image_old = $query->row['image'];
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '' WHERE image =  '".$image_old."'");
			if (file_exists(DIR_IMAGE . $image_old)) {
				unlink(DIR_IMAGE . $image_old);
			}

		}
		
	}
	
	
	public function updateOCProductMarkAndModel($product_id, $data){
		
		if(!isset($data['Model'])) return false;
		
		$Model = explode('/', trim($data['Model']));
		
		foreach($Model as $model){
			$marks = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark_description WHERE LOWER(`name`) LIKE '".utf8_strtolower($this->db->escape($model))."' LIMIT 1");
			
			$mark_id = 0;
			if($marks->num_rows){
				$mark_id = (int)$marks->row['mark_id'];
			}
			
			if($mark_id > 0){
				//$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_mark WHERE product_id = '" . (int)$product_id . "'");
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_mark SET
							 product_id = '" . (int)$product_id . "',
							 mark_id = '" . (int)$mark_id . "'
							 ON DUPLICATE KEY UPDATE mark_id = '" . (int)$mark_id . "'
						");
			}
		}
		
	}
	
	public function getProducts($data = array()){
		
		
		if(count($data) == 0){
			$res = $this->db->query("SELECT *, pc.product_id AS diagram_id, pc.quantity AS diagram_quantity, p.quantity AS quantity, pd.name
									FROM " . DB_PREFIX . "product p
									LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
									LEFT JOIN " . DB_PREFIX . "product_coordinate pc ON (p.product_id = pc.list_product_id)
									LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = ".$this->config->get('config_language_id').")
									WHERE 1 ");
			return $res->rows;
		}else{
			
			$vendor = $category = $manufacturer = $name = '';
			
			if(isset($data['category']) AND count($data['category']) > 0){
				$category = ' AND p2c.category_id IN ('.implode(',', $data['category']).')';
			}
			
			if(isset($data['vendor']) AND count($data['vendor']) > 0){
				$vendor = ' AND v2p.vendor_id IN ('.implode(',', $data['vendor']).')';
			}
			
			if(isset($data['manufacturer']) AND count($data['manufacturer']) > 0){
				$manufacturer = ' AND p.manufacturer_id IN ('.implode(',', $data['manufacturer']).')';
			}
			
			if(isset($data['name']) AND trim($data['name']) != ''){
				$name = ' AND LOWER(pd.name) LIKE "%'.utf8_strtolower($this->db->escape($data['name'])).'%"';
			}
			
			$sql = "SELECT *, pc.product_id AS diagram_id, pc.quantity AS diagram_quantity, p.quantity AS quantity, pd.name, p.*
							FROM " . DB_PREFIX . "product p
							LEFT JOIN " . DB_PREFIX . "vendor_to_product v2p ON (p.product_id = v2p.product_id)
							LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
							LEFT JOIN " . DB_PREFIX . "product_coordinate pc ON (p.product_id = pc.list_product_id)
							LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = ".$this->config->get('config_language_id').")
							WHERE 1 $category $manufacturer $name $vendor";

			//$sql .= " AND p.product_id = 49810";							
							
			$sql .= " GROUP BY p.product_id
							ORDER BY pc.product_id ASC, pc.num ASC, pd.name ASC
							";
			
			if(isset($data['category']) AND count($data['category']) > 0){
				//$category = ' AND p2c.main_category = 1';
			}
			
			//die($sql);
			
			$res = $this->db->query($sql);
			
			return $res->rows;
		}
	}
	
	public function getProductsShort($data = array()){
		
		
		if(count($data) == 0){
			$res = $this->db->query("SELECT p.*, pd.name
									FROM " . DB_PREFIX . "product p
									LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = ".$this->config->get('config_language_id').")
									WHERE 1 ");
			return $res->rows;
		}else{
			
			$vendor = $category = $manufacturer = $name = '';
			
			if(isset($data['category']) AND count($data['category']) > 0){
				$category = ' AND p2c.category_id IN ('.implode(',', $data['category']).')';
			}
			
			if(isset($data['vendor']) AND count($data['vendor']) > 0){
				$vendor = ' AND v2p.vendor_id IN ('.implode(',', $data['vendor']).')';
			}
			
			if(isset($data['manufacturer']) AND count($data['manufacturer']) > 0){
				$manufacturer = ' AND p.manufacturer_id IN ('.implode(',', $data['manufacturer']).')';
			}
			
			if(isset($data['name']) AND trim($data['name']) != ''){
				$name = ' AND LOWER(pd.name) LIKE "%'.utf8_strtolower($this->db->escape($data['name'])).'%"';
			}
			
			$sql = "SELECT  p.*, pd.name
							FROM " . DB_PREFIX . "product p
							LEFT JOIN " . DB_PREFIX . "vendor_to_product v2p ON (p.product_id = v2p.product_id)
							LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
							LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = ".$this->config->get('config_language_id').")
							WHERE 1 $category $manufacturer $name $vendor";

			//$sql .= " AND p.product_id = 49810";							
							
			$sql .= " GROUP BY p.product_id
							ORDER BY pd.name ASC
							";
			
			if(isset($data['category']) AND count($data['category']) > 0){
				//$category = ' AND p2c.main_category = 1';
			}
			
			//die($sql);
			
			$res = $this->db->query($sql);
			
			return $res->rows;
		}
	}
	public function updateOCProductOLD($product_id, $data){
		
		$languages_res = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
		$languages = $languages_res->rows;
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($languages as $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET
							 product_id = '" . (int)$product_id . "',
							 language_id = '" . (int)$language['language_id'] . "',
							 name = '" . $this->db->escape($data['description']) . "',
							 description = '" . $this->db->escape($data['details']) . "',
							 tag = '" . $this->db->escape($data['description']) . "',
							 meta_title = '" . $this->db->escape($data['description']) . "',
							 meta_description = '" . $this->db->escape($data['description']) . "',
							 meta_keyword = '" . $this->db->escape($data['description']) . "'");
		}
		
	}
	
	public function getCategoriesByParentId($parent_id = 0, $type = false) {
		$sql = "SELECT *,
					(SELECT COUNT(parent_id) FROM " . DB_PREFIX . "category WHERE parent_id = c.category_id) AS children,
					(SELECT `name` FROM " . DB_PREFIX . "category_description cd2 WHERE c.category_id = cd2.category_id AND cd2.language_id = '2') AS name2
						FROM " . DB_PREFIX . "category c
						LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
						WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
	
        if ($type) {
            $sql .= " AND c.type = '" . $this->db->escape($type) . "'";
        }

        $sql .= " ORDER BY c.sort_order, cd.name";

        $query = $this->db->query($sql);

		return $query->rows;
	}
	
}