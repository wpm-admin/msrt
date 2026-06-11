<?php
class ControllerExtensionFeedMaseratinetSitemap extends Controller {
	public function index() {
	
		$this->cleanDir(DIR_APPLICATION.'../sitemap');
		
		$languages_res = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
		$languages = $languages_res->rows;
				
			
			
			$header  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			$header .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";
			$footer = '</urlset>';
	
			$this->load->model('catalog/mark');
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			$this->load->model('catalog/information');
			$this->load->model('catalog/blog_product');

			$output = array();
			$parent_marks = $marks = $this->model_catalog_mark->getMarks();
			
			foreach ($marks as $mark) {
				$marks = array_merge($marks, $this->model_catalog_mark->getMarks($mark['mark_id']));
			}
			
			
			foreach ($marks as $mark) {
				$marks = array_merge($marks, $this->model_catalog_mark->getMarks($mark['mark_id']));
			}
			
			$categories = $this->model_catalog_category->getCategories();
			
			foreach ($categories as $index => $category) {
				$categories = array_merge($categories, $this->model_catalog_category->getCategories($category['category_id']));
			}
			
			foreach ($marks as $mark) {
				foreach ($languages as $language) {
					
					$url = '';
					if($language['code'] != 'en-gb'){
						$url = $language['url'] . '/';
					}
					
					$output[$language['code']] .= '<url>'."\n";
					$output[$language['code']] .= '  <loc>' . str_replace(HTTPS_SERVER, HTTPS_SERVER . $url, $this->url->link('product/mark', 'mark_id=' . $mark['mark_id'])) . '</loc>'."\n";
					$output[$language['code']] .= '  <changefreq>weekly</changefreq>'."\n";
					$output[$language['code']] .= '  <priority>0.7</priority>'."\n";
					if ($mark['image'] AND is_file(DIR_IMAGE . $mark['image'])) {
						$output[$language['code']] .= '  <image:image>'."\n";
						//$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
						$output[$language['code']] .= '  <image:loc>' . $this->model_tool_image->resize($mark['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup') . '</image:loc>'."\n";
						$output[$language['code']] .= '  <image:caption>' . $mark['name'] . '</image:caption>'."\n";
						$output[$language['code']] .= '  <image:title>' . $mark['name'] . '</image:title>'."\n";
						$output[$language['code']] .= '  </image:image>'."\n";
					}
					$output[$language['code']] .= '</url>'."\n";
					
					if((int)$mark['parent_id'] > 0){
						foreach ($categories as $index => $category) {
	
							if(in_array((int)$category['category_id'], array(223, 224, 225, 226, 228, 235, 236, 237, 238, 240))){
								continue;
							}						
							
							$output[$language['code']] .= '<url>'."\n";
							$output[$language['code']] .= '  <loc>' . str_replace(HTTPS_SERVER, HTTPS_SERVER . $url, $this->url->link('product/category', 'fix_mark_id=' . $mark['mark_id'] . '&path=' . $category['category_id'] )) . '</loc>'."\n";
							$output[$language['code']] .= '  <changefreq>weekly</changefreq>'."\n";
							$output[$language['code']] .= '  <priority>0.7</priority>'."\n";
							if ($category['image'] AND is_file(DIR_IMAGE . $category['image'])) {
								$output[$language['code']] .= '  <image:image>'."\n";
								//$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
								$output[$language['code']] .= '  <image:loc>' . $this->model_tool_image->resize($category['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup') . '</image:loc>'."\n";
								$output[$language['code']] .= '  <image:caption>' . $parent_marks[$mark['parent_id']]['name'] . ' ' . $mark['name'] . ' ' . $category['name'] . '</image:caption>'."\n";
								$output[$language['code']] .= '  <image:title>' .  $parent_marks[$mark['parent_id']]['name'] . ' ' . $mark['name'] . ' ' . $category['name'] . '</image:title>'."\n";
								$output[$language['code']] .= '  </image:image>'."\n";
							}
							$output[$language['code']] .= '</url>'."\n";
						}
					}
				}
			}

			foreach($output as $code => $xml){
			
				file_put_contents(DIR_APPLICATION.'../sitemap/mark_model_' . $code . '.xml' , $header.$xml.$footer);
			
			}		
		
	
			$output = array();
			$blog_products = $this->model_catalog_blog_product->getProducts();

			foreach ($blog_products as $blog_product) {
				foreach ($languages as $language) {
					
					$url = '';
					if($language['code'] != 'en-gb'){
						$url = $language['url'] . '/';
					}
					
					$output[$language['code']] .= '<url>'."\n";
					$output[$language['code']] .= '  <loc>' . str_replace(HTTPS_SERVER, HTTPS_SERVER . $url, $this->url->link('product/blog_product', 'blog_product_id=' . $blog_product['blog_product_id'])) . '</loc>'."\n";
					$output[$language['code']] .= '  <changefreq>weekly</changefreq>'."\n";
					$output[$language['code']] .= '  <priority>0.5</priority>'."\n";
					if ($blog_product['image'] AND is_file(DIR_IMAGE . $blog_product['image'])) {
						$output[$language['code']] .= '  <image:image>'."\n";
						//$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
						$output[$language['code']] .= '  <image:loc>' . $this->model_tool_image->resize($blog_product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup') . '</image:loc>'."\n";
						$output[$language['code']] .= '  <image:caption>' . $blog_product['name'] . '</image:caption>'."\n";
						$output[$language['code']] .= '  <image:title>' . $blog_product['name'] . '</image:title>'."\n";
						$output[$language['code']] .= '  </image:image>'."\n";
					}
					
					$output[$language['code']] .= '</url>'."\n";
				}
			}

			foreach($output as $code => $xml){
			
				file_put_contents(DIR_APPLICATION.'../sitemap/blog_' . $code . '.xml' , $header.$xml.$footer);
			
			}		
		
	
			$output = array();
			$informations = $this->model_catalog_information->getInformations();

			foreach ($informations as $information) {
				foreach ($languages as $language) {
					
					$url = '';
					if($language['code'] != 'en-gb'){
						$url = $language['url'] . '/';
					}
					
					$output[$language['code']] .= '<url>'."\n";
					$output[$language['code']] .= '  <loc>' . str_replace(HTTPS_SERVER, HTTPS_SERVER . $url, $this->url->link('information/information', 'information_id=' . $information['information_id'])) . '</loc>'."\n";
					$output[$language['code']] .= '  <changefreq>weekly</changefreq>'."\n";
					$output[$language['code']] .= '  <priority>0.5</priority>'."\n";
					$output[$language['code']] .= '</url>'."\n";
				}
			}

			foreach($output as $code => $xml){
			
				file_put_contents(DIR_APPLICATION.'../sitemap/information_' . $code . '.xml' , $header.$xml.$footer);
			
			}		
		
		
			$file_count = 0;
			
			while($file_count < 10){
				$products = $this->model_catalog_product->getProducts(array('start'=>($file_count * 10000), 'limit'=>10000 ));
			
				$output = array();
				
				if($products){
				
					foreach ($products as $product) {
						
						foreach ($languages as $language) {
							
							$url = '';
							if($language['code'] != 'en-gb'){
								$url = $language['url'] . '/';
							}
							
							$output[$language['code']] .= '<url>'."\n";
							$output[$language['code']] .= '  <loc>' . str_replace(HTTPS_SERVER, HTTPS_SERVER . $url, $this->url->link('product/product', 'product_id=' . $product['product_id'])) . '</loc>'."\n";
							$output[$language['code']] .= '  <changefreq>weekly</changefreq>'."\n";
							$output[$language['code']] .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>'."\n";
							$output[$language['code']] .= '  <priority>1.0</priority>'."\n";
			
							if ($product['image'] AND is_file(DIR_IMAGE . $product['image']))  {
								$output[$language['code']] .= '  <image:image>'."\n";
								//$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
								$output[$language['code']] .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup') . '</image:loc>'."\n";
								$output[$language['code']] .= '  <image:caption>' . htmlspecialchars($product['name'], ENT_QUOTES) . '</image:caption>'."\n";
								$output[$language['code']] .= '  <image:title>' . htmlspecialchars($product['name'], ENT_QUOTES) . '</image:title>'."\n";
								$output[$language['code']] .= '  </image:image>'."\n";
							}
		
							$output[$language['code']] .= '</url>'."\n";
							
						}
						
					}
						
					foreach($output as $code => $xml){
						
						file_put_contents(DIR_APPLICATION.'../sitemap/products_' . $code . '_' . $file_count . '.xml' , $header.$xml.$footer);
						
					}
				}
				$file_count++;
			}
			
			$this->makeIndex(DIR_APPLICATION.'../sitemap');
		echo 'done';
	}
	
	public function cleanDir($dir) {
		$files = glob($dir."/*.xml");
		$c = count($files);
		if (count($files) > 0) {
			foreach ($files as $file) {      
				if (file_exists($file)) {
				unlink($file);
				}   
			}
		}
	}

	public function makeIndex($dir) {
		
		$output = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		
		$files = glob($dir."/*.xml");
		$c = count($files);
		if (count($files) > 0) {
			foreach ($files as $file) {      
				if (file_exists($file)) {
				
					$file = explode('/', $file);
				
					$output .= '<url>'."\n";
					$output .= '<loc>'.HTTPS_SERVER.'sitemap/'.end($file).'</loc>'."\n";
					$output .= '<lastmod>'.date('Y-m-d').'</lastmod>'."\n";
					$output .= '</url>'."\n";
				
				}   
			}
		}
		
		$output .= '</urlset>'."\n";
		file_put_contents(DIR_APPLICATION.'../sitemap.xml' , $output);
	}

	protected function getCategories($parent_id) {
		$output = '';

		$results = $this->model_catalog_category->getCategories($parent_id);

		foreach ($results as $result) {
			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link('product/category', 'path=' . $result['category_id']) . '</loc>';
			$output .= '  <changefreq>weekly</changefreq>';
			$output .= '  <priority>0.7</priority>';
			$output .= '</url>';

			$output .= $this->getCategories($result['category_id']);
		}

		return $output;
	}
}
