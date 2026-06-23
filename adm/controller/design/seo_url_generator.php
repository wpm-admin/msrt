<?php
class ControllerDesignSeoUrlGenerator extends Controller {
	private $error = array();

	public function index() {
		
		$languages_res = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
		$languages = $languages_res->rows;
		
		
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query` LIKE 'product_id=%'");
		
		$description = $this->db->query("SELECT pd.product_id, pd.name, md.name AS mark_name, md2.name AS mark_parent_name
										FROM " . DB_PREFIX . "product_description pd
										LEFT JOIN " . DB_PREFIX . "product_to_mark p2m ON (pd.product_id = p2m.product_id)
										LEFT JOIN " . DB_PREFIX . "mark m ON (p2m.mark_id = m.mark_id)
										LEFT JOIN " . DB_PREFIX . "mark_description md ON (m.mark_id = md.mark_id AND md.language_id=2)
										LEFT JOIN " . DB_PREFIX . "mark_description md2 ON (m.parent_id = md2.mark_id AND md2.language_id=2)
										
										WHERE pd.language_id='2'");
		
		
		foreach($description->rows as $product){
		
			foreach ($languages as $language) {
				
				$keyword = $this->translitArtkl(trim($product['mark_parent_name']).'-'.trim($product['mark_name']).'-'.trim($product['name']));
				
				$sql = "INSERT INTO " . DB_PREFIX . "seo_url SET
							`query` = 'product_id=" . (int)$product['product_id'] . "',
							`keyword` = '".$this->db->escape($keyword)."',
							`store_id` = '0',
							language_id = '".(int)$language['language_id']."'
							";
				$this->db->query($sql);
			}
		}	
		
		
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query` LIKE 'category_id=%'");
		
		$description = $this->db->query("SELECT category_id, name FROM " . DB_PREFIX . "category_description WHERE language_id='2'");
		
		foreach($description->rows as $product){
		
			foreach ($languages as $language) {
				
				$keyword = $this->translitArtkl(trim($product['name']));
				
				$sql = "INSERT INTO " . DB_PREFIX . "seo_url SET
							`query` = 'category_id=" . (int)$product['category_id'] . "',
							`keyword` = '".$this->db->escape($keyword)."',
							`store_id` = '0',
							language_id = '".(int)$language['language_id']."'
							";
				$this->db->query($sql);
			}
		}	
		
		
			
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query` LIKE 'information_id=%'");
		
		$description = $this->db->query("SELECT information_id, title FROM " . DB_PREFIX . "information_description WHERE language_id='2'");
		
		foreach($description->rows as $product){
		
			foreach ($languages as $language) {
				
				$keyword = $this->translitArtkl(trim($product['title']));
				
				$sql = "INSERT INTO " . DB_PREFIX . "seo_url SET
							`query` = 'information_id=" . (int)$product['information_id'] . "',
							`keyword` = '".$this->db->escape($keyword)."',
							`store_id` = '0',
							language_id = '".(int)$language['language_id']."'
							";
				$this->db->query($sql);
			}
		}	
		
		
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query` LIKE 'mark_id=%'");
		
		$description = $this->db->query("SELECT mark_id, name FROM " . DB_PREFIX . "mark_description WHERE language_id='2'");
		
		foreach($description->rows as $product){
		
			foreach ($languages as $language) {
				
				$keyword = $this->translitArtkl(trim($product['name']));
				
				$sql = "INSERT INTO " . DB_PREFIX . "seo_url SET
							`query` = 'mark_id=" . (int)$product['mark_id'] . "',
							`keyword` = '".$this->db->escape($keyword)."',
							`store_id` = '0',
							language_id = '".(int)$language['language_id']."'
							";
				$this->db->query($sql);
			}
		}	
		
		
		
	
	
	
	}
	
	
	public function translitArtkl($str) {
		
		$rus = array('и','і','є','Є','ї','А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$lat = array('u','i','e','E','i','A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
		$str = str_replace($rus, $lat, $str);
	  
		$rus = array('&amp;', ':','“','”','«','»','quot;',"ʹ","'",'°','+','|','.',',','<,','>','~','@','!',"/",'\\','}','{','[',']',')','(','*','^','%','$','#','#','?','&','/','(', ')','"','\'','.');
		$lat = array('-');
		$str = str_replace($rus, $lat, $str);
		
		$rus = array(' ', '--');
		$lat = array('-');
		$str = str_replace($rus, $lat, $str);
	
	   return strtolower(trim($str,'-'));
	}
	
}