<?php
class ControllerExtensionModuleCategoryMark extends Controller {
	public function index() {

		$this->load->language('extension/module/mark');

		if(!isset($this->session->data['mark_id'])){
			$this->session->data['mark_id'] = 1;
		}
		
		
		if (isset($this->session->data['mark_id'])) {
			$data['mark_id'] = $this->session->data['mark_id']; //explode('_', (string)$this->request->get['path']);
			$data['href'] = $this->url->link('product/mark', 'mark_id=' . $data['mark_id']);
		} else {
			$data['mark_id'] = 0;
			$data['href'] = $this->url->link('product/mark', 'mark_id=1');
		}
		
		if (isset($this->session->data['model_id'])) {
			$data['model_id'] = $this->session->data['model_id']; //explode('_', (string)$this->request->get['path']);
			$data['href'] = $this->url->link('product/mark', 'mark_id=' . $data['model_id']);
		} else {
			$data['model_id'] = 0;
			$data['href'] = $this->url->link('product/mark', 'mark_id=1');
		}

		$data['home_page'] = false;
		if(empty($this->request->get)){
			$data['home_page'] = true;
		}

		$this->load->model('catalog/mark');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$this->load->model('tool/tool');

		$data['is_mobile'] = $this->model_tool_tool->isMobile();
		
		$data['home_url'] = $_SERVER['REQUEST_URI'];

		$data['marks'] = array();

		$marks = $this->model_catalog_mark->getMarks(0);

		foreach ($marks as $mark) {
			
			
			if($this->session->data['mark_id'] == $mark['mark_id']){
			
				$children_data = array();
	
				$children = $this->model_catalog_mark->getMarks($mark['mark_id'], false, true);
	
				//Сортонем модели
				/*
				if(isset($this->session->data['model_id']) AND (int)$this->session->data['model_id'] > 0){
					foreach($children as $index => $child) {
						if((int)$child['mark_id'] == (int)$this->session->data['model_id']){
							if($data['is_mobile']){
								$tmp = $children[0];
								$children[0] = $child;
								$children[$index] = $tmp;
								break;
							}else{
								
								$tmp = $children[1];
								$children[1] = $child;
								$children[$index] = $tmp;
								break;
							}
						}
					}
				}
				*/
			
				$data['active_index'] = 3;
				$active_index = 3;
				foreach($children as $child) {
					
					$children1 = $this->model_catalog_mark->getMarks($child['mark_id']);
					
					$children_data1 = array();
					
					foreach($children1 as $child1) {
						
						$children_data1[$child1['mark_id']] = array(
							'mark_id' => $child1['mark_id'],
							'image' => $this->model_tool_image->resize($child1['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')),
							'name' => $child1['name'],
							'href' => $this->url->link('product/mark', 'mark_id=' . $child1['mark_id'])
						);
					}
					
					if((int)$child['mark_id'] == (int)$data['model_id']) $data['active_index'] = $active_index;
					$active_index++;
				
					$children_data[$child['mark_id']] = array(
						'mark_id' => $child['mark_id'],
						'image' => $this->model_tool_image->resize($child['image'],347,151 ),
						// 'image' => $this->model_tool_image->resize($child['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')),
						'name' => $child['name'], // .' ' . $count++,
						'children'    => $children_data1,
						'href' => $this->url->link('product/mark', 'mark_id=' . $child['mark_id'])
					);
				}
			
			
				$filter_data = array(
					'filter_mark_id'  => $mark['mark_id'],
					'filter_sub_mark' => true
				);
	
				$data['marks'][$mark['mark_id']] = array(
					'mark_id' => $mark['mark_id'],
					'image' => $this->model_tool_image->resize($mark['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')),
					'name'        => $mark['name'],
					'children'    => $children_data,
					'href'        => $this->url->link('product/mark', 'mark_id=' . $mark['mark_id'])
				);
			}
		}

		//echo "<pre>";print_r(var_dump($data['active_index']));echo "</pre>";
		
		
		if(!$this->request->get){
			$data['main_page'] = true;
		}
		
		return $this->load->view('extension/module/categoryMark', $data);
	}
}