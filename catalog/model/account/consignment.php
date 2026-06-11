<?php
class ModelAccountConsignment extends Model {
	public function addConsignment($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "consignment SET
						 customer_id = '" . (int)$this->customer->isLogged(). "',
						 category_id = '" . (int)$data['category_id'] . "',
						 mark_id = '" . (int)$data['mark_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 price = '" . (float)$data['price'] . "',
						 my_price = '" . (float)$data['my_price'] . "',
						 date_end = '" . $this->db->escape($data['date_end']) . "',
						 status = '" . (int)$data['status'] . "',
						 name = '" . $this->db->escape($data['name']) . "',
						 telephone = '" . $this->db->escape($data['telephone']) . "',
						 email = '" . $this->db->escape($data['email']) . "',
						 year = '" . $this->db->escape($data['year']) . "',
						 description = '" . $this->db->escape($data['description']) . "',
						 images = '" . $this->db->escape(implode(';', $data['images'])) . "'
						 ");

		$consignment_id = $this->db->getLastId();

		return $consignment_id;
	}

	public function editConsignment($consignment_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "consignment SET
						 customer_id = '" . (int)$data['customer_id'] . "',
						 category_id = '" . (int)$data['category_id'] . "',
						 mark_id = '" . (int)$data['mark_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 price = '" . (float)$data['price'] . "',
						 my_price = '" . (float)$data['my_price'] . "',
						 status = '" . (int)$data['status'] . "',
						 name = '" . $this->db->escape($data['name']) . "',
						 telephone = '" . $this->db->escape($data['telephone']) . "',
						 email = '" . $this->db->escape($data['email']) . "',
						 date_end = '" . $this->db->escape($data['date_end']) . "',
						 year = '" . $this->db->escape($data['year']) . "',
						 description = '" . $this->db->escape($data['description']) . "',
						 images = '" . $this->db->escape(implode(';', $data['images'])) . "'
						 WHERE consignment_id = '" . (int)$consignment_id . "'");

		$images = $data['images'];
		
		$data['images'] = array();
		
		$count = 1;
		foreach($images as $image){
			
			if($image != ''){
				$f_path = explode('/', $image);
				$f_name = explode('.', $f_path[count($f_path) - 1]);
				
				$f_name_origin = implode('.', $f_name);
				
				array_pop($f_name);
				$f_name = implode('.', $f_name);
				
				$directory = rtrim(DIR_IMAGE . 'consignment');
			
				if(!is_dir($directory)){
					mkdir($directory , 0777);
					chmod($directory, 0777);
				}
				
				if(!is_dir($directory . '/' . $consignment_id)){
					mkdir($directory . '/' . $consignment_id, 0777);
					chmod($directory . '/' . $consignment_id, 0777);
				}

				if((int)$data['status'] == 3){	
					copy(DIR_UPLOAD.$f_name_origin, $directory . '/' . $consignment_id. '/'.$f_name);
				}
				
				$data['images'][] = 'consignment/' . $consignment_id. '/'.$f_name;
			}
		}
		
		if((int)$data['status'] == 3){	
			$this->db->query("UPDATE " . DB_PREFIX . "consignment SET
						 images_normal = '" . implode(';', $data['images']) . "'
						 WHERE consignment_id = '" . (int)$consignment_id . "'");
			
		}else{
			$this->db->query("UPDATE " . DB_PREFIX . "consignment SET
						 images_normal = ''
						 WHERE consignment_id = '" . (int)$consignment_id . "'");
			
			
			$this->RemoveDir(DIR_IMAGE . 'consignment/' . $consignment_id);
			rmdir(DIR_IMAGE . 'consignment/' . $consignment_id);
		}
	}

	private function RemoveDir($path){
		if(file_exists($path) && is_dir($path)){
			$dirHandle = opendir($path);
			while(false!==($file = readdir($dirHandle))){
				if($file!='.' && $file!='..'){
					$tmpPath = $path.'/'.$file;
					chmod($tmpPath, 0777);
					
					if(is_dir($tmpPath)){
						$this->RemoveDir($tmpPath);
					} else {
						if(!unlink($tmpPath)) echo 'Не удалось удалить файл «'.$path.'»!';
					}
				}
			}
		}
	}
 
	public function deleteConsignment($consignment_id) {
		
		$query = $this->db->query("SELECT images FROM " . DB_PREFIX . "consignment 
						 WHERE consignment_id = '" . (int)$consignment_id . "' AND customer_id='".(int)$this->customer->isLogged()."' LIMIT 1");
		
		$images = explode(';', $query->row['images']);
		
		foreach($images as $image){
			$f_name = explode('/', $image);
			$f_name = array_pop($f_name);
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "upload WHERE filename = '" . $this->db->escape($f_name) . "'");
			
			unlink(DIR_UPLOAD . $f_name);
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "consignment WHERE consignment_id = '" . (int)$consignment_id . "' AND customer_id='".(int)$this->customer->isLogged()."'");
		
		$this->RemoveDir(DIR_IMAGE . 'consignment/' . $consignment_id);
		rmdir(DIR_IMAGE . 'consignment/' . $consignment_id);

	}

	public function getConsignment($consignment_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "consignment WHERE consignment_id = '" . (int)$consignment_id . "' AND customer_id='".(int)$this->customer->isLogged()."'");

		return $query->row;
	}

	public function getConsignments($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "consignment 
					 WHERE customer_id='".(int)$this->customer->isLogged()."' ";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_consignment_group_id'])) {
			$sql .= " AND consignment_group_id = '" . $this->db->escape($data['filter_consignment_group_id']) . "'";
		}

		$sort_data = array(
			'name',
			'consignment_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}


	public function getTotalConsignments() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "consignment WHERE customer_id='".(int)$this->customer->isLogged()."'");

		return $query->row['total'];
	}

	public function getTotalConsignmentsByConsignmentGroupId($consignment_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "consignment WHERE consignment_group_id = '" . (int)$consignment_group_id . "' AND customer_id='".(int)$this->customer->isLogged()."'");

		return $query->row['total'];
	}
	
	public function deleteFile($file){
		$this->db->query("DELETE FROM " . DB_PREFIX . "upload WHERE filename='".$this->db->escape($file)."'");
		
		unlink(DIR_UPLOAD . $file);
	}
}
