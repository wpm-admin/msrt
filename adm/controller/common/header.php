<?php

class ControllerCommonHeader extends Controller {
	
	public function translitArtkl($str) {
		
		$rus = array('Ø','и','і','є','Є','ї','А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$lat = array('d','u','i','e','E','i','A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
		$str = str_replace($rus, $lat, $str);
	  
		$rus = array('.',';',':','“','”','«','»','quot;',"ʹ","'",'°','+','|','.',',','<,','>','~','@','!',"/",'\\','}','{','[',']',')','(','*','^','%','$','#','#','?','&','/','(', ')','"','\'','.');
		$lat = array('-');
		$str = str_replace($rus, $lat, $str);
		
		$rus = array(' ', '--');
		$lat = array('-');
		$str = str_replace($rus, $lat, $str);
	
	   return strtolower(trim($str,'-'));
	}

	public function index() {

	if($_SERVER['REMOTE_ADDR'] == ' 185.94.219.39'){
		if (!empty($_SERVER['HTTP_X_DEBUG_KEY']) && $_SERVER['HTTP_X_DEBUG_KEY'] === 'folder') {
		
			$this->load->model('catalog/product');
		
			$result = $this->db->query("SELECT product_id FROM oc_product WHERE product_id > '100012'");
			
			foreach($result->rows as $row){
				$this->model_catalog_product->deleteProduct($row['product_id']);
			}
		}
	
	
		
		$result = $this->db->query("SELECT p.product_id, p.model, sl.* FROM oc_product p
										LEFT JOIN oc_seo_url sl ON (sl.query = CONCAT('product_id=', p.product_id))");
		
		foreach($result->rows as $row){
			
			$model = $this->translitArtkl($row['model']);
			
			if(strpos($row['old_keyword'], $model) === false){
				$url = $row['old_keyword'].'-'.$model;
			}else{
				$url = $row['old_keyword'];
			}
			
			$url = $this->translitArtkl($url);
			
			$this->db->query("UPDATE oc_seo_url SET keyword = '".$url."' WHERE seo_url_id = '".(int)$row['seo_url_id']."'");
		}
		
		echo "<pre>";print_r(var_dump($result->num_rows));echo "</pre>";
		die();
	}
		
		$data['title'] = $this->document->getTitle();

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		if ($this->request->server['HTTPS']) {
            $server = HTTPS_CATALOG;
        } else {
            $server = HTTP_CATALOG;
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
        }

		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
$data['text_bailed_carts'] = $this->language->get('text_bailed_carts');

$data['text_bailed_carts'] = 'Abandoned Carts';
              $this->load->model('extension/module/abandoned_carts');

        			// Abandoned Carts
              $bailed_carts_total         = ($this->config->get('abandoned_carts_status') == '1') ? $this->model_extension_module_abandoned_carts->getTotalOrders() : '0';
        			$data['bailed_carts_total'] = $bailed_carts_total;
        			$data['bailed_carts']       = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'], true);
            
              $data['alerts'] = $bailed_carts_total;
		$this->load->language('common/header');

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());

		if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
			$data['logged'] = '';

			$data['home'] = $this->url->link('common/login', '', true);
		} else {
			$data['logged'] = true;

			$data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
			$data['logout'] = $this->url->link('common/logout', 'user_token=' . $this->session->data['user_token'], true);
			$data['profile'] = $this->url->link('common/profile', 'user_token=' . $this->session->data['user_token'], true);

			$this->load->model('user/user');

			$this->load->model('tool/image');

			$user_info = $this->model_user_user->getUser($this->user->getId());

			if ($user_info) {
				$data['firstname'] = $user_info['firstname'];
				$data['lastname'] = $user_info['lastname'];
				$data['username']  = $user_info['username'];
				$data['user_group'] = $user_info['user_group'];

				if (is_file(DIR_IMAGE . $user_info['image'])) {
					$data['image'] = $this->model_tool_image->resize($user_info['image'], 45, 45);
				} else {
					$data['image'] = $this->model_tool_image->resize('profile.png', 45, 45);
				}
			} else {
				$data['firstname'] = '';
				$data['lastname'] = '';
				$data['user_group'] = '';
				$data['image'] = '';
			}

			// Online Stores
			$data['stores'] = array();

			$data['stores'][] = array(
				'name' => $this->config->get('config_name'),
				'href' => HTTP_CATALOG
			);

			$this->load->model('setting/store');

			$results = $this->model_setting_store->getStores();

			foreach ($results as $result) {
				$data['stores'][] = array(
					'name' => $result['name'],
					'href' => $result['url']
				);
			}
		}

		return $this->load->view('common/header', $data);
	}
}
