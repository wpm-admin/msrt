<?php

class ControllerToolTool extends Controller {
	
	public function coordinate(){
		
		die('1');
		
		$query = $this->db->query("SELECT c.product_id, p.model, p.image FROM " . DB_PREFIX . "product_coordinate c
										LEFT JOIN " . DB_PREFIX . "product p ON (c.product_id = p.product_id)
								  GROUP BY c.product_id");
		
		foreach($query->rows as $row){
			
			if($row['image'] == '' AND $row['model'] != ''){
				$first = $this->db->query("SELECT product_id, model, image FROM first_product WHERE product_id = '".(int)$row['product_id']."' AND image <> '' LIMIT 1" );
			
				if($first->num_rows > 0){
					

					$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '".$this->db->escape($first->row['image'])."'");
					
					if($_SERVER['REMOTE_ADDR'] == '185.94.219.65'){
					echo "<pre>";print_r(var_dump($row));echo "</pre>";
					echo "<pre>";print_r(var_dump($first));echo "</pre>";
					die();
					}


					
				}
				
			
			}
			
			
		}
		
		
	}
	
	
	public function test(){
	
		set_time_limit(0);
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product");
		
		foreach($query->rows as $row){
			
			$start = microtime(true);
			
			$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_mark WHERE product_id = '".(int)$row['product_id']."'");
			$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '".(int)$row['product_id']."'");
			$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '".(int)$row['product_id']."'");
			$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '".(int)$row['product_id']."'");
			$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '".(int)$row['product_id']."'");
			$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '".(int)$row['product_id']."'");
			$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '".(int)$row['product_id']."'");
			
		}
		
		echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';
	}
	
	public function show_as_diagram(){
		$this->session->data['show_as_diagram'] = true;
		
		echo "<pre>";print_r(var_dump($this->session->data['show_as_diagram']));echo "</pre>";
		die();
	}
	
	public function unshow_as_diagram(){
		unset($this->session->data['show_as_diagram']);
		
		echo "<pre>";print_r(var_dump($this->session->data['show_as_diagram']));echo "</pre>";
	}
	
	public function link_models() {
		
		
		$products = $this->db->query("SELECT p.product_id FROM ".DB_PREFIX."product p WHERE p.product_id NOT IN (
									 SELECT distinct pm.product_id FROM ".DB_PREFIX."product_to_mark pm GROUP BY pm.product_id
									 )");

		foreach($products->rows as $row){
			
			$marks = $this->db->query("SELECT p2m.mark_id FROM ".DB_PREFIX."product_to_mark p2m WHERE p2m.product_id IN (
										SELECT pc.product_id FROM ".DB_PREFIX."product_coordinate pc WHERE pc.list_product_id = '".(int)$row['product_id']."'
										) LIMIT 1");
				
			if($marks->num_rows ){
				
				$sql = "INSERT INTO ".DB_PREFIX."product_to_mark SET product_id = '".(int)$row['product_id']."', mark_id = '".(int)$marks->row['mark_id']."'";
				//echo '<br>'.$sql;
				$this->db->query($sql);
										  
			}
			
									 
		}
		
		
	}
	public function set_sogl() {
		
		$this->session->data['is_soglashenie'] = true;
		setcookie('is_soglashenie', $code, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
	}
	public function del_sogl() {
		
		unset($this->session->data['is_soglashenie']);
		setcookie('is_soglashenie', $code, time() - 1000, '/', $this->request->server['HTTP_HOST']);
	}
    public function isMobile() {

        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);

    }
}