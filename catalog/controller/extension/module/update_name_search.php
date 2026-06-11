<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class ControllerExtensionModuleUpdateNameSearch extends Controller {
	
	
	public function diagram_model_to_product(){
		//Проставляем товарам недостающие модели от схем если они состоят в какойто схеме			
		$sql = "SELECT * FROM " . DB_PREFIX . "product_coordinate";// WHERE list_product_id = 13696";
		$results = $this->db->query($sql);
		
		
		foreach($results->rows as $row){
			
			$sql = "SELECT * FROM " . DB_PREFIX . "product_to_mark WHERE product_id = '".(int)$row['product_id']."'";
			$marks = $this->db->query($sql);
			
			foreach($marks->rows as $mark){
				
				$sql = "INSERT INTO " . DB_PREFIX . "product_to_mark SET product_id = '".(int)$row['list_product_id']."', mark_id = '".(int)$mark['mark_id']."' ON DUPLICATE KEY UPDATE mark_id = '".(int)$mark['mark_id']."'";
				$marks = $this->db->query($sql);
				
			}
			
		}
		$data['rows'] = $data['total'] = $results->num_rows;
		$data['run_name'] = 'Привязка Марка-Модель к товару';
		$data['url'] =  HTTPS_SERVER."index.php?route=extension/module/update_name_search&page=1";

		$this->response->setOutput($this->load->view('extension/module/update_name_search', $data));
		return false;
	}	

	public function update_mark_names_to_products_girsus(){
		$condition = true;
		$page = 1;
		
		if(isset($this->request->get['page'])){
			$page = (int)$this->request->get['page'];
		}
	
		$limit = 20000;
	
		
		$sql = "SELECT count(*) AS total FROM " . DB_PREFIX . "product_to_mark p2m
							LEFT JOIN " . DB_PREFIX . "mark m ON (p2m.mark_id = m.mark_id)
							LEFT JOIN " . DB_PREFIX . "mark_description md1 ON (p2m.mark_id = md1.mark_id)
							LEFT JOIN " . DB_PREFIX . "mark_description md2 ON (m.parent_id = md2.mark_id)";
															  
		$results = $this->db->query($sql);
		$data['total'] = (int)$results->row['total'];
		$data['rows'] = (int)(($page-1) * $limit);
		$data['run_name'] = '';
		
		//'models - <b>' . number_format(((int)$results->row['total'] - (int)(($page-1) * $limit)), 0, '', ' ') . '</b>';
	
		$sql = "SELECT *, md2.name AS parent_name, md1.name AS name FROM " . DB_PREFIX . "product_to_mark p2m
						LEFT JOIN " . DB_PREFIX . "mark m ON (p2m.mark_id = m.mark_id)
						LEFT JOIN " . DB_PREFIX . "mark_description md1 ON (p2m.mark_id = md1.mark_id)
						LEFT JOIN " . DB_PREFIX . "mark_description md2 ON (m.parent_id = md2.mark_id)
						LIMIT ".(int)(($page-1) * $limit).", ".$limit."";
														  
		$results = $this->db->query($sql);
		
		foreach($results->rows as $row){
			if(trim($row['name']) != '' AND trim($row['parent_name']) != ''){
				$sql = "UPDATE " . DB_PREFIX . "product_description SET `name_search` = CONCAT(`name_search`, ' ".$this->db->escape($row['parent_name']).' '.$this->db->escape($row['name'])."')
							WHERE product_id='".(int)$row['product_id']."' AND language_id='".(int)$row['language_id']."' AND
							`name_search` NOT LIKE '%".$this->db->escape(trim($row['name']))."%'";
				$this->db->query($sql);
			}
			
		}

		if((int)$data['total'] >= (int)$data['rows']){
			$data['url'] =  HTTPS_SERVER."index.php?route=extension/module/update_name_search/update_mark_names_to_products&page=" . ($page + 1);
		}else{
			$data['url'] =  HTTPS_SERVER."index.php?route=extension/module/update_name_search/add_parent_diagram_detail&page=1";
		}
		
		$this->response->setOutput($this->load->view('extension/module/update_name_search', $data));
		
		/*
		if($results->num_rows > 0){
			$page= $page+1;
			// $json['url'] = "/index.php?route=extension/module/update_name_search/update_mark_names_to_products&page=" . ($page + 1) ;
		}else{
			$condition = false;
			$json['url'] = "/index.php?route=extension/module/update_name_search/add_parent_diagram_detail";
			$json['total'] = 'diadrams - ...';
		}
		*/
		
	}

	public function add_parent_diagram_detail_girsus(){
		$condition = true;
		$page = 1;
		while ($condition) {
		if(isset($this->request->get['page'])){
			$page = (int)$this->request->get['page'];
		}
		
		$json = array();	
		
		$sql = "SELECT count(*) AS total FROM " . DB_PREFIX . "product_coordinate p2co
						LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p2co.product_id)
						LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id)";
															  
		$results = $this->db->query($sql);
		$json['total'] = 'diadrams - <b>' . number_format(((int)$results->row['total'] - (int)(($page-1) * 50000)), 0, '', ' ') . '</b>';
		
		
		$sql = "SELECT * FROM " . DB_PREFIX . "product_coordinate p2co
						LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p2co.product_id)
						LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id)
						LIMIT ".(int)(($page-1) * 50000).", 50000";
		$results = $this->db->query($sql);

	
		foreach($results->rows as $row){
			if(trim($row['name']) != ''){
				
				$sql = "UPDATE " . DB_PREFIX . "product_description SET `name_search` = CONCAT(`name_search`, ' ".$this->db->escape($row['name'])."')
							WHERE product_id='".(int)$row['list_product_id']."' AND language_id='".(int)$row['language_id']."' AND
							`name_search` NOT LIKE '%".$this->db->escape(trim($row['name']))."%'";
				
				$this->db->query($sql);
			}
			
			//$added[(int)$row['list_product_id']][(int)$row['language_id']] .= $this->db->escape(trim($row['name']));
		}
		
		
		if($results->num_rows > 0){
			$page= $page+1;
			// $json['url'] = "/index.php?route=extension/module/update_name_search/add_parent_diagram_detail&page=" . ($page + 1) ;
		}else{
			$condition = false;
			$json['url'] = false;
			$json['total'] = 'DONE';
		}
	}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	

	
	
	
	public function index() {
		set_time_limit(0);

		$page = 0;
		if(isset($this->request->get['page'])){
			$page = (int)$this->request->get['page'];
		}
		
			//Вначале прочешим диаграммы
		if($page == 0){
			$page = $this->diagram_model_to_product();
			if(!$page) return false;
		}

		
		$data['run_name'] = 'Связи товар-категория';
		$limit = 20000;
		
		
			$sql = "SELECT COUNT(p2c.product_id) AS total FROM " . DB_PREFIX . "product_to_category p2c
							LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id)";
			
			$results = $this->db->query($sql);
			
			$data['total'] = (int)$results->row['total'];
		
			//$this->load->model('catalog/product');
			$this->db->query("UPDATE " . DB_PREFIX . "log SET date_added = NOW() ");
			
			if($page == 1){
				$sql = "UPDATE " . DB_PREFIX . "product_description SET `name_search` = `name`";
				$this->db->query($sql);
			}
			
			
			$sql = "SELECT * FROM " . DB_PREFIX . "product_to_category p2c
							LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id)
							LIMIT ".(int)(($page-1)*$limit).", ".(int)($page) * $limit;
	
	
			$results = $this->db->query($sql);
			
			foreach($results->rows as $row){
				
				if(trim($row['name']) != ''){
					$sql = "UPDATE " . DB_PREFIX . "product_description SET `name_search` = CONCAT(`name_search`, ' ".$this->db->escape(trim($row['name']))."')
							WHERE product_id='".(int)$row['product_id']."' AND language_id='".(int)$row['language_id']."' AND
								`name_search` NOT LIKE '%".$this->db->escape(trim($row['name']))."%'";
					$this->db->query($sql);
				}
				
			}
		
			$data['rows'] = (int)($page) * $limit;
		
		
		/*
		 https://maseratinet.com/index.php?route=extension/module/update_name_search&page=1
		 */
		
			if((int)$data['total'] >= (int)$data['rows']){
				$data['url'] =  HTTPS_SERVER."index.php?route=extension/module/update_name_search&page=" . ($page + 1);
			}else{
				$data['url'] =  HTTPS_SERVER."index.php?route=extension/module/update_name_search/update_mark_names_to_products&page=1";
			}
			
			$this->response->setOutput($this->load->view('extension/module/update_name_search', $data));
		
		
	}
	
	public function update_mark_names_to_products(){

		if(isset($this->request->get['page'])){
			$page = (int)$this->request->get['page'];
		}
		
		$limit = 20000;
		
		$sql = "SELECT count(*) AS total FROM " . DB_PREFIX . "product_to_mark p2m
							LEFT JOIN " . DB_PREFIX . "mark m ON (p2m.mark_id = m.mark_id)
							LEFT JOIN " . DB_PREFIX . "mark_description md1 ON (p2m.mark_id = md1.mark_id)
							LEFT JOIN " . DB_PREFIX . "mark_description md2 ON (m.parent_id = md2.mark_id)";
															  
		$results = $this->db->query($sql);
		
		$data['total'] = (int)$results->row['total'];
		$data['rows'] = (int)(($page-1) * $limit);
		$data['run_name'] = 'Имена марок в товарах';
		
		
		$sql = "SELECT *, md2.name AS parent_name, md1.name AS name FROM " . DB_PREFIX . "product_to_mark p2m
						LEFT JOIN " . DB_PREFIX . "mark m ON (p2m.mark_id = m.mark_id)
						LEFT JOIN " . DB_PREFIX . "mark_description md1 ON (p2m.mark_id = md1.mark_id)
						LEFT JOIN " . DB_PREFIX . "mark_description md2 ON (m.parent_id = md2.mark_id)
						LIMIT ".(int)(($page-1) * $limit).", ".$limit."";
														  
		$results = $this->db->query($sql);
		
		foreach($results->rows as $row){
			if(trim($row['name']) != '' AND trim($row['parent_name']) != ''){
				$sql = "UPDATE " . DB_PREFIX . "product_description SET `name_search` = CONCAT(`name_search`, ' ".$this->db->escape($row['parent_name']).' '.$this->db->escape($row['name'])."')
							WHERE product_id='".(int)$row['product_id']."' AND language_id='".(int)$row['language_id']."' AND
							`name_search` NOT LIKE '%".$this->db->escape(trim($row['name']))."%'";
				$this->db->query($sql);
			}
			
		}
		
		if((int)$data['total'] >= (int)$data['rows']){
			$data['url'] =  HTTPS_SERVER."index.php?route=extension/module/update_name_search/update_mark_names_to_products&page=" . ($page + 1);
		}else{
			$data['url'] =  HTTPS_SERVER."index.php?route=extension/module/update_name_search/add_parent_diagram_detail&page=1";
		}
		
		$this->response->setOutput($this->load->view('extension/module/update_name_search', $data));	
		
	}
	

	public function add_parent_diagram_detail(){
		
		$page = 1;
		if(isset($this->request->get['page'])){
			$page = (int)$this->request->get['page'];
		}
		
		$limit = 20000;	
		
		$sql = "SELECT count(*) AS total FROM " . DB_PREFIX . "product_coordinate p2co
						LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p2co.product_id)
						LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id)";
															  
		$results = $this->db->query($sql);
	
	
		$data['total'] = (int)$results->row['total'];
		$data['rows'] = (int)(($page-1) * $limit);
		$data['run_name'] = 'Родители диаграм в товары';	
		
		$sql = "SELECT * FROM " . DB_PREFIX . "product_coordinate p2co
						LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p2co.product_id)
						LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id)
						LIMIT ".(int)(($page-1) * $limit).", ".$limit."";
		$results = $this->db->query($sql);

	
		foreach($results->rows as $row){
			if(trim($row['name']) != ''){
				
				$sql = "UPDATE " . DB_PREFIX . "product_description SET `name_search` = CONCAT(`name_search`, ' ".$this->db->escape($row['name'])."')
							WHERE product_id='".(int)$row['list_product_id']."' AND language_id='".(int)$row['language_id']."' AND
							`name_search` NOT LIKE '%".$this->db->escape(trim($row['name']))."%'";
				
				$this->db->query($sql);
			}
			
			//$added[(int)$row['list_product_id']][(int)$row['language_id']] .= $this->db->escape(trim($row['name']));
		}
		
		if((int)$data['total'] >= (int)$data['rows']){
			$data['url'] =  HTTPS_SERVER."index.php?route=extension/module/update_name_search/add_parent_diagram_detail&page=" . ($page + 1);
		}else{
			$data['url'] = 0;
		}
		
		$this->response->setOutput($this->load->view('extension/module/update_name_search', $data));

	}	
	
	
}
