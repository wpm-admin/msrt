<?php
class ModelVendorImport extends Model {
	
	public function getCountryByName($name) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE
								  LCASE(name) = '" . $this->db->escape(utf8_strtolower($name)) . "'
								  LIMIT 1");

		return $query->row;
	}

	public function getVendorProduct($vendor_id, $product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_to_product WHERE
								  vendor_id = '" . (int)$vendor_id . "' AND product_id = '" . (int)$product_id . "'
								  LIMIT 1");

		if($query->num_rows == 0){
			
			return false;
		}
								  
		return $query->row;
	}

	public function editVendorProduct($pd_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "vendor_to_product SET
						 vendor_id = '" . (int)$data['vendor_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 cost = '" . (float)$data['cost'] . "',
						 quantity = '" . (int)$data['quantity'] . "',
						 status = '" . (int)$data['status'] . "',
						 model = '" . $this->db->escape($data['model']) . "',
						 sku = '" . $this->db->escape($data['sku']) . "',
						 comment = '" . $this->db->escape($data['comment']) . "'
						 WHERE pd_id = '".(int)$pd_id."'
						 ");
		
		echo "UPDATE " . DB_PREFIX . "vendor_to_product SET
						 vendor_id = '" . (int)$data['vendor_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 cost = '" . (float)$data['cost'] . "',
						 quantity = '" . (int)$data['quantity'] . "',
						 status = '" . (int)$data['status'] . "',
						 model = '" . $this->db->escape($data['model']) . "',
						 sku = '" . $this->db->escape($data['sku']) . "',
						 comment = '" . $this->db->escape($data['comment']) . "'
						 WHERE pd_id = '".(int)$pd_id."'
						 ";

	}	
	
	public function addVendorProduct($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_to_product SET
						 vendor_id = '" . (int)$data['vendor_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 cost = '" . (float)$data['cost'] . "',
						 quantity = '" . (int)$data['quantity'] . "',
						 status = '" . (int)$data['status'] . "',
						 model = '" . $this->db->escape($data['model']) . "',
						 sku = '" . $this->db->escape($data['sku']) . "',
						 comment = '" . $this->db->escape($data['comment']) . "'");

	}	
	
	public function getStateByName($country_id, $name) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE
								  (LCASE(code) = '" . $this->db->escape(utf8_strtolower($name)) . "' OR
								  LCASE(name) = '" . $this->db->escape(utf8_strtolower($name)) . "') and
								  country_id = '".(int)$country_id."'
								  LIMIT 1");

		return $query->row;
	}
	public function getStateByZoneId($zone_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE
								  zone_id = '".(int)$zone_id."'
								  LIMIT 1");

		return $query->row;
	}


	public function translitArtkl($str) {
		
		$rus = array('и','і','є','Є','ї','А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$lat = array('u','i','e','E','i','A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
		$str = str_replace($rus, $lat, $str);
	  
		$rus = array(':','“','”','«','»','quot;',"ʹ","'",'°','+','|','.',',','<,','>','~','@','!',"/",'\\','}','{','[',']',')','(','*','^','%','$','#','#','?','&','/','(', ')','"','\'','.');
		$lat = array('-');
		$str = str_replace($rus, $lat, $str);
		
		$rus = array(' ', '--');
		$lat = array('-');
		$str = str_replace($rus, $lat, $str);
	
	   return strtolower(trim($str,'-'));
	}
	
	
}