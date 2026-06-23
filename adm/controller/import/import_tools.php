<?php
//set_time_limit(0);
class ControllerImportImportTools extends Controller {
	
	public function index() {
		$this->load->language('import/import');
		die('Заделка на будущее, не реализовано');

		$this->document->setTitle($this->language->get('heading_title'));
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'] = array();

		$url = '';
		
		//$this->db->query("ALTER TABLE oc_product ADD COLUMN cost float(11) NOT NULL AFTER price;");
		/* Удалить все товары
		$this->load->model('catalog/product');
		$products = $this->model_catalog_product->getProducts(array());
		foreach($products as $product){
			$this->model_catalog_product->deleteProduct($product['product_id']);
		}
		*/
		
		/*
		$this->load->model('catalog/product');
		$this->load->model('import/import');
		$products = $this->model_catalog_product->getProducts();
		
		foreach($products as $product){
			$this->model_import_import->updateOCProductKeywords($product['product_id']);
		}
		*/
		
		$data['tab'] = 'data';
		if(isset($this->request->get['tab'])){
			$data['tab'] = $this->request->get['tab'];
		}
	
		if(isset($this->session->data['statistic'])){
			$data['statistic'] = $this->session->data['statistic'];
		}
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('import/import', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('import/import');
		$data['categories'] = $this->_getCategories(0);
		$data['manufacturers'] = $this->_getManufacturers();
		/*
		$products = $this->model_catalog_product->getProducts();
		foreach($products as $product){
			$this->model_import_import->updateOCProductKeywords($product['product_id']);
		}
		*/
		
		$data['field_names'] = $this->_getFields();
		$data['customer_field_names'] = $this->_getCustomerFields();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['href_import_db2'] = $this->url->link('import/import/import_db2', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['href_import_excel'] = $this->url->link('import/import/import_excel', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['href_import_files'] = $this->url->link('import/import/import_files', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['href_export_excel'] = $this->url->link('import/import/export_excel', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['href_import_customer'] = $this->url->link('import/import/import_customer', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['href_export_customer'] = $this->url->link('import/import/export_customer', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$this->response->setOutput($this->load->view('import/import', $data));
	}

	public function export_customer(){
		
		$this->load->model('customer/customer');
		$this->load->model('customer/garage');
		$this->load->model('catalog/mark');
		
		$ABC = $this->_getABC();
		
		$field_names_tmp = $this->_getCustomerFields();
		
		//Соберем выбранные колонки
		$field_names = array();
		$count = 1;
		foreach($field_names_tmp as $field_name){
			$field_names[$count++] = $field_name;
		}
		
		require_once (DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$i = 1;
		
		$objPHPExcel->getActiveSheet()->getStyle("A".$i.":AG".$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A".$i.":AG".$i)->getFill()->applyFromArray(array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array(
						 'rgb' => 'ffc397'
					)
				));
		
		foreach($field_names as $index => $field_name){
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$index].$i, $field_name['name']);
		}
			
		$objPHPExcel->getActiveSheet()->getStyle("A".$i.":AG".$i)->getFont()->setBold(true);
		$i++;
	
		foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
			//$objPHPExcel->getActiveSheet()->getColumnWeightUOM($col)->setAutoSize(true);
		} 
			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
			
		
		$customers = $this->model_customer_customer->getCustomers();
		
		foreach($customers as $row){
			
			$address = $this->model_customer_customer->getAddress($row['address_id']);
			$garage = $this->model_customer_garage->getGarage($row['customer_id']);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['name']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $address['address_1']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $address['city']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $address['address_2']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $address['postcode']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $address['country']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['telephone']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['email']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row['customer_group']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['account_number']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['shipping']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $garage['model_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $garage['year']);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $garage['vin']);
			$i++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle("A1:AG".$i)->getFont()->setSize(9);
		$objPHPExcel->getActiveSheet()->getStyle("A1:AG1")->getFont()->setBold(true);
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="maseratinet_customers.xls"'); 
		header('Cache-Control: max-age=0'); 

		$objWriter->save('php://output'); 
		exit();
	}
	
	public function export_excel(){
		
		$this->session->data['import_error'] = array();
		
		$this->load->language('import/import');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('import/import');
		$this->load->model('catalog/product');
	
		
		$post = $this->request->post;
		
		$categoryes = $this->_getCategories(0);
		
		$categoryes[0] = array(
							   'category_id' => 0,
							   'name' => '',
							   'name2' => '',
							   'parent_id' => 0
							   );
		
		$manufacturers = $this->_getManufacturers();
		$stock_statuses = $this->_getStockStatuses();
		$condition_statuses = $this->_getConditionStatuses();
		$weight_classes = $this->_getWeightClasses();
		$length_classes = $this->_getLengthClasses();
		$tax_classes = $this->_getTaxClasses();
		
		$statuses = array(
						  0 => 'Disabled',
						  1 => 'Enabled',
						  );
		
		//$attributes = $this->model_import_excel->getAttributes();
		//$attributes_name = $this->model_import_excel->getAttributesNames();
		
		
		//$find_data = $this->request->post;
		
		$ABC = $this->_getABC();
		
		$field_names_tmp = $this->_getFields();
		
		//Соберем выбранные колонки
		$field_names = array();
		$count = 1;
		foreach($field_names_tmp as $field_name){
			if(isset($this->request->post['column'][$field_name['id']])){
				$field_names[$count++] = $field_name;
			}
		}
		
				
		$products = $this->model_import_import->getProducts($this->request->post);
		
		
		$export = array();
		foreach($products as $product){
			
			$product['descriptions'] = $this->model_catalog_product->getProductDescriptions($product['product_id']);
			$product['specials'] = $this->model_catalog_product->getProductSpecials($product['product_id']);
			
			
			
			$product['special'] = '';
			if(count($product['specials']) > 0){
				$special = array_shift($product['specials']);
				$product['special'] = $special['price'];
			}
		
			//Диаграмы
			if(strpos($product['model'], 'diagram_') !== false){
				$export[(int)$product['product_id']] = $product;
			//Списки диаграммы
			}elseif($product['diagram_id']){
				$export[(int)$product['diagram_id']]['list'][$product['product_id']] = $product;
			//Бесходные товары
			}else{
				$export[-1]['list'][$product['product_id']] = $product;	
			}
			
		}
		

			
		if(count($export) > 0){
		
			require_once (DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
		
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$i = 1;
		
			$undiagramme = $export[-1];
			unset($export[-1]);
		
			foreach($export as $index => $rows){
				
				
				if(!isset($rows['product_id']) OR !$rows['product_id']) continue;
				
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $rows['diagram_name']. ' - ' . $rows['sub_category']);
				//$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $description['name']);
				$objPHPExcel->getActiveSheet()->getStyle("A".$i.":AO".$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle("A".$i.":AO".$i)->getFill()->applyFromArray(array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array(
						 'rgb' => 'ffc397'
					)
				));
				$i++;
				
				foreach($field_names as $index => $field_name){
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$index].$i, $field_name['name']);
				}
				
				$objPHPExcel->getActiveSheet()->getStyle("A".$i.":AO".$i)->getFont()->setBold(true);
				$i++;
			
				foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
					$objPHPExcel->getActiveSheet() ->getColumnWeightUOM($col) ->setAutoSize(true);
				} 
			 
			
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
			
				if(isset($rows['list']) AND count($rows['list'])){
					foreach($rows['list'] as $row){
						
						
						$row['descriptions'][4]['name'] = $this->decodeHtml($row['descriptions'][4]['name']);
						$row['descriptions'][2]['name'] = $this->decodeHtml($row['descriptions'][2]['name']);
						$row['descriptions'][5]['name'] = $this->decodeHtml($row['descriptions'][5]['name']);
						$row['descriptions'][3]['name'] = $this->decodeHtml($row['descriptions'][3]['name']);
						$row['descriptions'][1]['name'] = $this->decodeHtml($row['descriptions'][1]['name']);
						$row['descriptions'][6]['name'] = $this->decodeHtml($row['descriptions'][6]['name']);
							
						$bukva = 1;
						if(isset($post['column'][1])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['num']);
						}
						if(isset($post['column'][2])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['model']);
						}
						if(isset($post['column'][3])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['sku']);
						}
						if(isset($post['column'][4])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][4]['name']);
						}
						if(isset($post['column'][5])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][2]['name']);
						}
						if(isset($post['column'][6])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][5]['name']);
						}
						if(isset($post['column'][7])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][3]['name']);
						}
						if(isset($post['column'][8])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][1]['name']);
						}
						if(isset($post['column'][9])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][6]['name']);
						}
						if(isset($post['column'][10])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['quantity']);
						}
						if(isset($post['column'][11])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['mark']);
						}
						if(isset($post['column'][12])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['modeli']);
						}
						if(isset($post['column'][13])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['year']);
						}
						if(isset($post['column'][14])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $categoryes[(int)$row['category_id']]['name2']);
						}
						if(isset($post['column'][15])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $rows['sub_category']);
						}
						if(isset($post['column'][16])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $rows['diagram_name']);
						}
						if(isset($post['column'][17])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['price']);
						}
						if(isset($post['column'][18])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['special']);
						}
						if(isset($post['column'][19])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $stock_statuses[(int)$row['stock_status_id']]['name']);
						}
						if(isset($post['column'][20])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $manufacturers[(int)$row['manufacturer_id']]['name']);
						}
						if(isset($post['column'][21])){ 
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['uom']);
						}
						if(isset($post['column'][22])){ 
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i,  $row['location']);
						}
						if(isset($post['column'][23])){ 
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $rows['descriptions'][4]['description']);
						}
						if(isset($post['column'][24])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['weight']);
						}
						if(isset($post['column'][25])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $weight_classes[(int)$row['weight_class_id']]['unit']);
						}
						if(isset($post['column'][26])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $condition_statuses[(int)$row['condition_status_id']]['name']);
						}
						if(isset($post['column'][27])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $statuses[(int)$row['status']]);
						}
						if(isset($post['column'][28])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, (int)$row['diagram_quantity']);
						}
						if(isset($post['column'][29])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['length']);
						}
						if(isset($post['column'][30])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['width']);
						}
						if(isset($post['column'][31])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['height']);
						}
						if(isset($post['column'][32])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $length_classes[(int)$row['length_class_id']]['unit']);
						}
						if(isset($post['column'][33])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['cost']);
						}
						if(isset($post['column'][34])){
							$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $tax_classes[(int)$row['tax_class_id']]['name']);
						}

						$i++;
					}
				}
				$i++;$i++;
			}
		
					
			//$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'No in diagram');
			//$objPHPExcel->getActiveSheet()->getStyle("A".$i.":AG".$i)->getFont()->setBold(true);
			//$i++;
				
				foreach($field_names as $index => $field_name){
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$index].$i, $field_name['name']);
				}
				$objPHPExcel->getActiveSheet()->getStyle("A".$i.":AO".$i)->getFont()->setBold(true);
		
			$i++;
				

			if(isset($undiagramme['list']) AND count($undiagramme['list'])){
				foreach($undiagramme['list'] as $row){
					
					if(!isset($row['product_id']) OR !$row['product_id']) continue;
					
					$bukva = 1;
					if(isset($post['column'][1])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['num']);
					}
					if(isset($post['column'][2])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['model']);
					}
					if(isset($post['column'][3])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['sku']);
					}
					if(isset($post['column'][4])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][4]['name']);
					}
					if(isset($post['column'][5])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][2]['name']);
					}
					if(isset($post['column'][6])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][5]['name']);
					}
					if(isset($post['column'][7])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][3]['name']);
					}
					if(isset($post['column'][8])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][1]['name']);
					}
					if(isset($post['column'][9])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['descriptions'][6]['name']);
					}
					if(isset($post['column'][10])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['quantity']);
					}
					if(isset($post['column'][11])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['mark']);
					}
					if(isset($post['column'][12])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['modeli']);
					}
					if(isset($post['column'][13])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['year']);
					}
					if(isset($post['column'][14])){
						$parent_id = (int)(isset($categoryes[$row['category_id']]['parent_id']) ? $categoryes[$row['category_id']]['parent_id'] : 0);
						//if($parent_id == 0) $parent_id = $row['category_id'];
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $categoryes[$parent_id]['name2']);
					}
					if(isset($post['column'][15])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $categoryes[(int)$row['category_id']]['name2']);
					}
					if(isset($post['column'][16])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['diagram_name']);
					}
					if(isset($post['column'][17])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['price']);
					}
					if(isset($post['column'][18])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['special']);
					}
					if(isset($post['column'][19])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $stock_statuses[(int)$row['stock_status_id']]['name']);
					}
					if(isset($post['column'][20])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, (isset($row['manufacture_id']) AND isset($manufacturers[(int)$row['manufacture_id']])) ? $manufacturers[(int)$row['manufacture_id']]['name'] : '');
					}
					if(isset($post['column'][21])){ 
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['uom']);
					}
					if(isset($post['column'][22])){ 
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i,  $row['location']);
					}
					if(isset($post['column'][23])){ 
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, (isset($rows['descriptions'][4])) ? $rows['descriptions'][4]['description'] : '');
					}
					if(isset($post['column'][24])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['weight']);
					}
					if(isset($post['column'][25])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $weight_classes[(int)$row['weight_class_id']]['unit']);
					}
					if(isset($post['column'][26])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $condition_statuses[(int)$row['condition_status_id']]['name']);
					}
					if(isset($post['column'][27])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $statuses[(int)$row['status']]);
					}
					if(isset($post['column'][28])){
						$bukva++;
					}
					if(isset($post['column'][29])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['length']);
					}
					if(isset($post['column'][30])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['width']);
					}
					if(isset($post['column'][31])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['height']);
					}
					if(isset($post['column'][32])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $length_classes[(int)$row['length_class_id']]['unit']);
					}
					if(isset($post['column'][33])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $row['cost']);
					}
					if(isset($post['column'][34])){
						$objPHPExcel->getActiveSheet()->setCellValue($ABC[$bukva++].$i, $tax_classes[(int)$row['tax_class_id']]['name']);
					}
	
					$i++;
				}
			}
	
			$objPHPExcel->getActiveSheet()->getStyle("A1:AO".$i)->getFont()->setSize(9);
			$objPHPExcel->getActiveSheet()->getStyle("A1:AO1")->getFont()->setBold(true);
		
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
			
			header('Content-Type: application/vnd.ms-excel'); 
			header('Content-Disposition: attachment;filename="maseratinet_diagram.xls"'); 
			header('Cache-Control: max-age=0'); 
	
			$objWriter->save('php://output'); 
			exit(); 
			
				
		}else{
			$this->index();
		}
	}
	
	public function decodeHtml($html){
		$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
		$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
		$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
		$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
		$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
		$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
		return $html;
	}

	
	function reArrayFiles(&$file_post) {

		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);
	
		$this->session->data['statistic'][] = 'Files: <b>'.(int)$file_count.'</b>';
	
		for ($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}
	
		return $file_ary;
	}
	
	public function import_files(){
			
		$this->load->model('import/import');
		
		unset($this->session->data['statistic']);
		
		$products = array();
		
		if(isset($_FILES['files'])){
			$file_ary = $this->reArrayFiles($_FILES['files']);
		
			foreach ($file_ary as $file) {
				$filename = html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8');
				
				$filename = str_replace(' ', '_', $filename);
				
				$tmp = explode('.', $filename);
				$file['extension'] = array_pop($tmp);
				$file['filename_st'] = implode('.',$tmp);
				
				//Если это инкремента - обережем число
				if(strpos($file['filename_st'], '-') !== false){
					$search = explode('-', $file['filename_st']);
					$sort = array_pop($search);
					$search = implode('-', $search);
				}else{
					$search = $file['filename_st'];
					$sort = 0;
				}
				
				
				$res = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE
										`sku` LIKE '".$this->db->escape(str_replace('_', '%', $search))."' OR
										`model` LIKE '".$this->db->escape(str_replace('_', '%', $search))."'
										LIMIT 1");
	
				if($res->num_rows){
	
					//Если стоит галочка о полной замене и это первый фаил товара то удалим все файлы
					if(isset($this->request->post['replace']) AND !in_array((int)$res->row['product_id'], $products)){
						$products[] = (int)$res->row['product_id'];
						$res->row['image'] = '';
						
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE
									 `product_id`='" . (int)$res->row['product_id'] . "'");
					}
	
	
					
					move_uploaded_file($file['tmp_name'], DIR_IMAGE . 'catalog/upload/' . $filename);
					
					if($res->row['image'] == ''){
						$this->db->query("UPDATE " . DB_PREFIX . "product SET
									 `image` = '".'catalog/upload/'.$filename."' WHERE `product_id`='" . (int)$res->row['product_id'] . "'");
					}else{
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET
									 `image` = '".'catalog/upload/'.$filename."',
									 `product_id`='" . (int)$res->row['product_id'] . "',
									 `sort_order` = '".(int)$sort."'");
					}
					
					$this->session->data['statistic'][] = '<span class="success">File: <b>'.$filename.'</b> code: <b>'.$search.'</b> - success</span>';
				}else{
					$this->session->data['statistic'][] = '<span class="error">File: <b>'.$filename.'</b> code: <b>'.$search.'</b> - not found</span>';
				}
			}
		}
		
		$this->response->redirect($this->url->link('import/import', 'tab=files&user_token=' . $this->session->data['user_token'], true));
		//die();
	}
	
	public function import_customer(){
			
		$this->load->model('import/import');
		
		$ABC = $this->_getABC();
		
		$field_names_tmp = $this->_getCustomerFields();
		//Соберем выбранные колонки
		$field_names = array();
		$count = 1;
		foreach($field_names_tmp as $field_name){
			//$field_names[$field_name['name']] = $field_name['id'];
		}
		
		
		$field_id_name = array();
		$field_name_id = array();
		
		foreach($field_names_tmp as $row){
			
			$field_id_name[$row['id']] = $row['name'];
			$field_name_id[$row['name']] = $row['id'];
			
		}
		
		if(isset($_FILES['file']['tmp_name']) AND $_FILES['file']['tmp_name'] != ''){
			
				$tmpFilename = $_FILES['file']['tmp_name'];
	
				require_once (DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
				
				$worksheet = PHPExcel_IOFactory::load($tmpFilename)->getSheet(0);
				
				$worksheetRows = $worksheet->getHighestRow();
	/*
	$i = 1;
	while($i < $worksheetRows){
		
		$county = $worksheet->getCell('A'.$i )->getValue();
		$phone = $worksheet->getCell('C'.$i )->getValue();
		$i++;
		
		if(strpos($phone, '+') === false){
			$phone = '+'.$phone;
		}
		
		$this->db->query("UPDATE  " . DB_PREFIX . "country SET `tel_pref`='".$this->db->escape($phone)."', `trans_name`='".$this->db->escape($county)."'  WHERE LOWER(name) LIKE '".$this->db->escape(utf8_strtolower($county))."'");
			
	}
	die();
	*/
	
	
				$rows = array();
				$i = 0;
				
				$header_name = array();
				$header_id = array();
				
				unset($ABC[0]);

				while($i < $worksheetRows){
					
					foreach($ABC as $abs){
						
						$value = $worksheet->getCell($abs.$i )->getValue();
						
						if(trim($value) == '') continue;
						
						//Если это заголовк
						if(in_array($value, $field_id_name) and is_string($value)){
							$header_name[$abs] = $value;
							$header_id[$value] = $abs;
						}elseif(isset($header_name[$abs])){
							$rows[$i][$header_name[$abs]] = $value;
						}
						
					}
	
					$i++;	
				}
	
	
			
				//Чистим все что без кртиклов
				foreach($rows as $index => $row){
					if(!isset($row['Phone']) AND !isset($row['Email'])) unset($rows[$index]);
					//Работаем только с емаил
					if(!isset($row['Email'])) unset($rows[$index]);
				}

				
				//пишем в базу
				$updated_diarrams = array();
				foreach($rows as $index => $row){
					
					if(trim($row['Email']) == '' AND trim($row['Phone']) == '') continue;
					
					//Работаем только с емаил
					if(trim($row['Email']) == '') continue;
					
					$customer_id = $this->model_import_import->getCustomerOnEmailOrPhone((isset($row['Email']) ? $row['Email'] : ''), (isset($row['Phone']) ? $row['Phone'] : '') );
					
						
					if(!$customer_id){
						$customer_id = $this->model_import_import->addCustomerShort($row);
					}
					
					$this->model_import_import->updateCustomer($customer_id, $row);
		
				}
					
		}		
		
		$url = '';
		
		$this->response->redirect($this->url->link('import/import', 'user_token=' . $this->session->data['user_token'] . $url, true));
		
	}
	public function import_excel(){
			
		$this->load->model('import/import');
		
		$ABC = $this->_getABC();
		
		$field_names_tmp = $this->_getFields();
		//Соберем выбранные колонки
		$field_names = array();
		$count = 1;
		foreach($field_names_tmp as $field_name){
			//$field_names[$field_name['name']] = $field_name['id'];
		}
		
		
		$field_id_name = array();
		$field_name_id = array();
		
		foreach($field_names_tmp as $row){
			
			$field_id_name[$row['id']] = $row['name'];
			$field_name_id[$row['name']] = $row['id'];
			
		}
		
		if(isset($_FILES['file']['tmp_name']) AND $_FILES['file']['tmp_name'] != ''){
			
				$tmpFilename = $_FILES['file']['tmp_name'];
	
				require_once (DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
				
				$worksheet = PHPExcel_IOFactory::load($tmpFilename)->getSheet(0);
				
				$worksheetRows = $worksheet->getHighestRow();
	
	
				$rows = array();
				$i = 0;
				
				$header_name = array();
				$header_id = array();
				
				unset($ABC[0]);

				while($i < $worksheetRows){
					
					foreach($ABC as $abs){
						
						$value = $worksheet->getCell($abs.$i )->getValue();
						
						if(trim($value) == '') continue;
						
						//Если это заголовк
						if(in_array($value, $field_id_name) and is_string($value)){
							$header_name[$abs] = $value;
							$header_id[$value] = $abs;
						}elseif(isset($header_name[$abs])){
							$rows[$i][$header_name[$abs]] = $value;
						}
						
					}
	
					$i++;	
				}
	
	
				
				//Чистим все что без кртиклов
				foreach($rows as $index => $row){
					if(!isset($row['CodeManual'])) unset($rows[$index]);
				}

				
				//пишем в базу
				$updated_diarrams = array();
				foreach($rows as $index => $row){
					
					if(!isset($row['CodeManual']) AND !isset($row['Product Number'])) continue;
					
					$product_id = $this->model_import_import->getProductOnSkuModel((isset($row['CodeManual']) ? $row['CodeManual'] : $row['Product Number']), ((isset($row['Product Number']) AND trim($row['Product Number']) != '') ? $row['Product Number'] : $row['CodeManual']) );
						
					if(!$product_id){
						$product_id = $this->model_import_import->addProductShort($row);
					}
					
					//Составим имя диаграммы - если нет всех колонок для нее - пропускаем
					if(isset($row['Marque']) AND isset($row['Model']) AND isset($row['SubCategory (Alias)']) AND isset($row['Diagram'])){
						$diagram_name = $row['Marque'] . ' ' . $row['Model'] . ', ' . utf8_strtolower($row['SubCategory (Alias)']) . ' - ' . $row['Diagram'];
						
						$diagram_name = $row['Diagram'] . ' - ' . utf8_strtolower($row['SubCategory (Alias)']);
					
						$diagram_id = (int)$this->model_import_import->getDiagramId($diagram_name, $row);
					
					}else{
						$diagram_id = false;
					}
				
					$this->model_import_import->updateOCProduct($product_id, $row);
					$this->model_import_import->updateOCProductMarkAndModel($product_id, $row);
					
					$this->model_import_import->updateOCProductCategory($product_id, $row);
					
					
					$this->model_import_import->updateOCProductDescription($product_id, $row);

					$this->model_import_import->updateOCProductKeywords($product_id, $row);
				
				
					if(isset($row['Number on the diagram']) AND (int)$row['Number on the diagram'] > 0 AND (int)$diagram_id > 0){
						$this->model_import_import->addToDiagramm($diagram_id, $product_id, $row['Number on the diagram'],
																  ((isset($row['DiagramQty']) AND (int)$row['DiagramQty'] > 0) ? (int)$row['DiagramQty'] : 1));
					}
					
					echo '<br>Diagram_id='.$diagram_id.' product_id='.$product_id;
				}
					
		}		
		
		$url = '';
		
		$this->response->redirect($this->url->link('import/import', 'user_token=' . $this->session->data['user_token'] . $url, true));
		
	}
	
	private function _getStockStatuses(){
		
		$this->load->model('import/import');
		
		return $this->model_import_import->_getStockStatuses();
		
	}
	
	private function _getConditionStatuses(){
		
		$this->load->model('import/import');
		
		return $this->model_import_import->_getConditionStatuses();
		
	}
	

	
	public function import_db2() {
		
		$this->load->model('import/import');
		
		$PRODS = $this->model_import_import->getMasfProducts(10);
		
		
		foreach($PRODS as $PROD){
			
			$product_id = $this->model_import_import->getOCProduct($PROD['num']);
			 
			if(!$product_id){
				$product_id = $this->model_import_import->addOCProduct($PROD);
			}
			 
			$this->model_import_import->updateOCProduct($product_id, $PROD); 
			 
			 
		}
		
		echo "<pre>";print_r(var_dump($result));echo "</pre>";
		die();
		
		echo "<pre>";print_r(var_dump(count($res)));echo "</pre>";
		die();
	
	}
	public function index1111() {
		    
		$url = 'http://api.mizol.ua';

		$ch = curl_init();
	
		if ($ch) {
			$data = [
			  "modelName" => "OpenBox_Dealers", //модель
			  "calledMethod" => "getDealer", //вызов функции из модели
			  "methodProperties" => [
				  "site" => "mizol.ua",
				  //"city" => $city_name,
				  "latitude" => "50.449633", //широта
				  "longitude" => "30.521012", //долгота
			  ],
			  "page" => [
				  "limit" => 20000, // количество элементов в ответе
			  ],
			  "apiKey" => 20, //Ваш открытый ключ
			  "response" => 'xml', //xml, json
			];
	
			$data['hash'] = $this->createHash($data); //хеш
	
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	
			$response = curl_exec($ch);
	
			if ($response === false) {
				throw new Exception('Error: [' . curl_errno($ch) . '] ' . curl_error($ch));
			}
		
		
			$xml = simplexml_load_string($response);
	
			$response_attributes = $xml->response->attributes();
	
			if ($response_attributes['code'] != 200) {
				throw new Exception('[' . $response_attributes['code'] . '] ' . $response_attributes['status']);
			}
	
			curl_close($ch);
			
			$this->load->model('import/dealers');
			$this->model_import_dealers->updateDealers($xml);
			
		}
			
			
			die('111111');
	}
	
	private function _getManufacturers(){
		$this->load->model('catalog/manufacturer');
		
		$results = $this->model_catalog_manufacturer->getManufacturers();
		
		$return[0] = array(
						'manufacturer_id' => 0,
						'name' => ''
						);
		
		foreach ($results as $result) { 
			$return[$result['manufacturer_id']] = $result;
		}

		return $return;
	}
	
	private function _getWeightClasses(){
		
		$this->load->model('import/import');
		
		return $this->model_import_import->getWeightClasses();
	}
	
	private function _getLengthClasses(){
		
		$this->load->model('import/import');
		
		return $this->model_import_import->getLengthClasses();
	}
	
	private function _getTaxClasses(){
		
		$this->load->model('import/import');
		
		return $this->model_import_import->getTaxClasses();
	}
	
	private function _getCategories($parent_id, $parent_path = '', $indent = '', $parent_name = '') {
		
		$this->load->model('import/import');
		
		//$category_id = array_shift($this->path);

		$output = array();

		$url = '';

		$results = $this->model_import_import->getCategoriesByParentId($parent_id);

		foreach ($results as $result) {
			$path = $parent_path . $result['category_id'];

			$name = $result['name'];

			$action = array();

			$name = ($parent_name != '') ? $parent_name .' -> '.$name : $name;
			
			$output[$result['category_id']] = array(
				'category_id' => $result['category_id'],
				'name'        => $name,
				'name2'        => $result['name2'],
				'short_name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'indent'      => $indent,
				'parent_id'      => $indent
			);

			$output += $this->_getCategories($result['category_id'], '', $result['category_id'], $name);
		}

		return $output;
	}
	
	private function _getFields(){
		$return = array();
		
		$return[] = array(
						  'id' => '1',
						  'name' => 'Number on the diagram',
						  'pos' => 'A',
						  );
		
		$return[] = array(
						  'id' => '2',
						  'name' => 'CodeManual',
						  'pos' => 'B',
						  );
		
		$return[] = array(
						  'id' => '3',
						  'name' => 'Product Number',
						  'pos' => 'C',
						  );
		
		$return[] = array(
						  'id' => '4',
						  'name' => 'Italian',
						  'pos' => 'D',
						  );
		
		$return[] = array(
						  'id' => '5',
						  'name' => 'English',
						  'pos' => 'E',
						  );
		
		$return[] = array(
						  'id' => '6',
						  'name' => 'French',
						  'pos' => 'F',
						  );
		
		$return[] = array(
						  'id' => '7',
						  'name' => 'German',
						  'pos' => 'G',
						  );
		
		$return[] = array(
						  'id' => '8',
						  'name' => 'Russian',
						  'pos' => 'H',
						  );
		
		$return[] = array(
						  'id' => '9',
						  'name' => 'Spanish',
						  'pos' => 'I',
						  );
		
		$return[] = array(
						  'id' => '10',
						  'name' => 'Qty',
						  'pos' => 'J',
						  );
		
		$return[] = array(
						  'id' => '11',
						  'name' => 'Marque',
						  'pos' => 'K',
						  );
		
		$return[] = array(
						  'id' => '12',
						  'name' => 'Model',
						  'pos' => 'L',
						  );
		
		$return[] = array(
						  'id' => '13',
						  'name' => 'Year',
						  'pos' => 'M',
						  );
		
		$return[] = array(
						  'id' => '14',
						  'name' => 'Category (Alias)',
						  'pos' => 'N',
						  );
		
		$return[] = array(
						  'id' => '15',
						  'name' => 'SubCategory (Alias)',
						  'pos' => 'O',
						  );
		
		$return[] = array(
						  'id' => '16',
						  'name' => 'Diagram',
						  'pos' => 'P',
						  );
		
		$return[] = array(
						  'id' => '17',
						  'name' => 'Price',
						  'pos' => 'Q',
						  );
		
		$return[] = array(
						  'id' => '18',
						  'name' => 'Special',
						  'pos' => 'R',
						  );

		$return[] = array(
						  'id' => '19',
						  'name' => 'Availability',
						  'pos' => 'S',
						  );

		$return[] = array(
						  'id' => '20',
						  'name' => 'Manufacturer',
						  'pos' => 'T',
						  );

		$return[] = array(
						  'id' => '21',
						  'name' => 'UOM',
						  'pos' => 'U',
						  );

		$return[] = array(
						  'id' => '22',
						  'name' => 'Location',
						  'pos' => 'V',
						  );

		$return[] = array(
						  'id' => '23',
						  'name' => 'Product Description',
						  'pos' => 'W',
						  );

		$return[] = array(
						  'id' => '24',
						  'name' => 'Weight',
						  'pos' => 'X',
						  );

		$return[] = array(
						  'id' => '25',
						  'name' => 'WeightUOM',
						  'pos' => 'Y',
						  );

		$return[] = array(
						  'id' => '26',
						  'name' => 'Condition',
						  'pos' => 'Z',
						  );

		$return[] = array(
						  'id' => '27',
						  'name' => 'Status',
						  'pos' => 'AA',
						  );

		$return[] = array(
						  'id' => '28',
						  'name' => 'DiagramQty',
						  'pos' => 'AB',
						  );
		
		$return[] = array(
						  'id' => '29',
						  'name' => 'Width',
						  'pos' => 'AC',
						  );
		
		$return[] = array(
						  'id' => '30',
						  'name' => 'Height',
						  'pos' => 'AD',
						  );
		
		$return[] = array(
						  'id' => '31',
						  'name' => 'Length',
						  'pos' => 'AE',
						  );
		
		$return[] = array(
						  'id' => '32',
						  'name' => 'SizeUOM',
						  'pos' => 'AF',
						  );
		
		$return[] = array(
						  'id' => '33',
						  'name' => 'Cost',
						  'pos' => 'AG',
						  );
		
		$return[] = array(
						  'id' => '34',
						  'name' => 'TaxClass',
						  'pos' => 'AH',
						  );
		
	
		if($this->config->get('config_import_fields')){
			$return = $this->config->get('config_import_fields');
		}

		return $return;
	}
	private function _getCustomerFields(){
		$return = array();
		
		$return[] = array(
						  'id' => '1',
						  'name' => 'Name',
						  'pos' => 'A',
						  );
		
		$return[] = array(
						  'id' => '2',
						  'name' => 'Address',
						  'pos' => 'B',
						  );
		
		$return[] = array(
						  'id' => '3',
						  'name' => 'City',
						  'pos' => 'C',
						  );
		
		$return[] = array(
						  'id' => '4',
						  'name' => 'State',
						  'pos' => 'D',
						  );
		
		$return[] = array(
						  'id' => '5',
						  'name' => 'Zip Code',
						  'pos' => 'E',
						  );
		
		$return[] = array(
						  'id' => '6',
						  'name' => 'Country',
						  'pos' => 'F',
						  );
		
		$return[] = array(
						  'id' => '7',
						  'name' => 'Phone',
						  'pos' => 'G',
						  );
		
		$return[] = array(
						  'id' => '8',
						  'name' => 'Email',
						  'pos' => 'H',
						  );
		
		$return[] = array(
						  'id' => '9',
						  'name' => 'Group',
						  'pos' => 'I',
						  );
		
		$return[] = array(
						  'id' => '10',
						  'name' => 'Account number',
						  'pos' => 'J',
						  );
		
		$return[] = array(
						  'id' => '11',
						  'name' => 'Shipping',
						  'pos' => 'K',
						  );
		
		$return[] = array(
						  'id' => '12',
						  'name' => 'Model',
						  'pos' => 'L',
						  );
		
		$return[] = array(
						  'id' => '13',
						  'name' => 'Year',
						  'pos' => 'M',
						  );
		
		$return[] = array(
						  'id' => '14',
						  'name' => 'VIN',
						  'pos' => 'N',
						  );
		

		
		
		
	
		if($this->config->get('config_customer_fields')){
			$return = $this->config->get('config_customer_fields');
		}

		return $return;
	}
	
	private function _getABC(){
		return array('*',
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
				'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
				'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR',
				'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
			);
	}

}