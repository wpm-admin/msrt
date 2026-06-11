<?php
class ControllerExtensionModuleCategoryCatalog extends Controller {
	public function index() {

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}
		$this->load->language('information/information');
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		
		$data['model_href'] = '';
		if(isset($this->request->get['mark_id'])){
			$data['model_href'] = $this->url->link('product/mark', 'mark_id=' . $this->request->get['mark_id']);
		}
		
		
		foreach ($categories as $category) {
			
			if((int)$category['category_id'] == 234) continue; //Аукцион
			
			$filter_data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			if ($category['image']) {
				$cat_image = $this->model_tool_image->resize($category['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			} else {
				$cat_image = '';
			}				

			$tmp_href = $this->url->link('product/category', 'path=' . $category['category_id']);
			
			$tmp_href_arr = explode('/', $tmp_href);
			
			$filter_data = array(
				'filter_category_id' => (int)$category['category_id'],
				'filter_diagram'      => true,
				'filter_model'	=> true,
				'filter_mark_id' => (int)$this->request->get['mark_id'],
				'filter_sub_category' => true,
			);
			
			$total = $this->model_catalog_product->getTotalProducts($filter_data);
			if($category['category_id'] == 229 OR $total){
				$data['categories'][] = array(
					'category_id' => $category['category_id'],
					'image' 	  => $cat_image,
					'short_href'        => '/'.array_pop($tmp_href_arr),
					'name'        => $category['name'], // . ' ' . (($category['category_id'] == 229) ? '' : '(<b>'.$total.'</b>)'),
					'href'        => $tmp_href,
					//'total' => ($category['category_id'] == 229) ? 0 :$this->model_catalog_product->getTotalProducts($filter_data)
				);
			}
		}

		
		return $this->load->view('extension/module/categoryCatalog', $data);
	}
}