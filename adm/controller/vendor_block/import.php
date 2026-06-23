<?php
class ControllerVendorImport extends Controller {
	private $error = array();
	private $ABC = array();
	
	public function initABC(){
		$this->ABC = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

	}
	
	public function index() {
		$this->load->language('vendor/import');

		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor');
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('vendor/import', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['import'] = $this->url->link('vendor/import/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['import_products'] = $this->url->link('vendor/import/import_products', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('vendor/import/export', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export_products'] = $this->url->link('vendor/import/export_products', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		
		$data['hom_href'] = $this->url->link('vendor/import', 'user_token=' . $this->session->data['user_token'], true);
		$data['log_href'] = $this->url->link('vendor/import', 'user_token=' . $this->session->data['user_token'] . '&log_id=', true);
		$data['vendor_edit'] = $this->url->link('vendor/vendor/edit', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=', true);
		
		$this->response->setOutput($this->load->view('vendor/import', $data));
	}

	private function _getABC(){
		return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
				'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
				'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR',
				'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
			);
	}
	
	public function import_products(){
		
		$this->load->model('vendor/vendor');
		$this->load->model('vendor/import');
		$this->load->model('catalog/product');
		
		$this->initABC();
		
		$data = array();

		$imported_product_ids = array();
		
		$category_product_count = array();
		
		$product_option_list = array();
		$product_image_list = array();
		$product_attribute_list = array();
		
		if(isset($_FILES['upload']['tmp_name']) AND $_FILES['upload']['tmp_name'] != ''){
			
			$filename = $_FILES['upload']['name'];
			
			$tmpFilename = $_FILES['upload']['tmp_name'];

			include DIR_SYSTEM.'library/PHPExcel/PHPExcel.php';
			
			$worksheet = PHPExcel_IOFactory::load($tmpFilename)->getSheet(0);
			
			$worksheetRows = $worksheet->getHighestRow();

			$rows = array();
			$i = 2;
			while($i < ($worksheetRows + 1)){
				
				foreach($this->ABC as $abc){
					$rows[$i][trim((string)$worksheet->getCell($abc.'1' )->getValue())] = trim((string)$worksheet->getCell($abc.$i )->getValue());
				}
				$i++;	
			}
	

			$products = array();
			$count = 1;
			
			$import[] = array();
			//проверка и поиск данных
			foreach($rows as $index => $row){
				
				//Проверяем на формулы
				foreach($row as $ind => $val){
					
					if(isset($val[0]) AND $val[0] == '='){
						$this->error['warning'] .= 'row: <b>' . $index . '</b> col: <b>' . $ind . '</b> - <b>formula</b> ('.(string)$val.')<br>';
					}
					
				}
				
				if(trim($row['VendorName']) == '' AND trim($row['VendorId'])) continue;
				
				if(!isset($row['VendorId'])){
					$row['vendor_id'] = false;
				}else{
					$row['vendor_id'] = (int)$row['VendorId'];
				}
			
				if(!$row['vendor_id']){
					$row['vendor_id'] = $this->model_vendor_vendor->getVendorOnName(trim($row['VendorName']));	
				}
				
				if(!$row['vendor_id']){
					$this->error['warning'] .= 'row: <b>' . $index . '</b> - <b>No Vendor </b><br>';
					continue;
				}
				
				
				$row['product_id'] = $this->model_catalog_product->getProductIdOnModel(trim($row['CodeManual']));	
				
				if(!$row['product_id']){
					$this->error['warning'] .= 'row: <b>' . $index . '</b> - <b>No Product </b><br>';
					continue;
				}
				
				
				//$row['vendor_id'] = 9;
				$row['model'] = trim($row['VendorCodeManual']);
				$row['sku'] = trim($row['VendorFishbowl N']);
				$row['cost'] = (float)trim($row['Cost']);
				$row['quantity'] = (float)trim($row['Quantity']);
				$row['status'] = (int)trim($row['Status']);
				$row['comment'] = trim($row['Comment']);
				
				$import[$row['vendor_id']][] = $row;
				
			}
			
			unset($import[0]);
			
			foreach($import as $vendor_id => $rows){
				
				//$this->model_vendor_vendor->dellProducts($vendor_id);
				
				foreach($rows as $row){
					
					$pd_info = $this->model_vendor_import->getVendorProduct($vendor_id, $row['product_id']);
					
					if($pd_info){
						$this->model_vendor_import->editVendorProduct($pd_info['pd_id'], $row);
					}else{
						$this->model_vendor_import->addVendorProduct($row);
					}
					
				}
				
			}
			
		}
	}
	
	public function export_products(){
		
		$this->session->data['import_error'] = array();
		
		$this->load->language('import/import');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('vendor/vendor');
		$this->load->model('vendor/import');
		//$this->load->model('catalog/product');
	
		
		$post = $this->request->post;
		
		//$categoryes = $this->_getCategories(0);
		
		$categoryes[0] = array(
							   'category_id' => 0,
							   'name' => '',
							   'name2' => '',
							   'parent_id' => 0
							   );
		
		$vendors = $this->model_vendor_vendor->getVendors();
		
		$statuses = array(
						  0 => 'Disabled',
						  1 => 'Enabled',
						  );
		
		
		$ABC = $this->_getABC();
		
		$export = array();
		
		require_once (DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
	
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		$i = 1;
		$ab = 0;
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'VendorId');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'VendorName');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'CodeManual');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'VendorCodeManual');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'VendorFishbowl N');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Cost');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'ProductName');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Quantity');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Status');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Comment');
		
		foreach($vendors as $vendor){
			
			$products = $this->model_vendor_vendor->getProducts($vendor['vendor_id']);
			
			if($products){
				foreach($products as $product){
					
					$i++;
					$ab = 0;
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['vendor_id']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['firstname']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $product['model']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $product['vendor_model']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $product['vendor_sku']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $product['cost']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $product['name']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $product['quantity']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $product['status']);
					$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $product['comment']);
				}
			}
			
		}
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="vendor_products.xls"'); 
		header('Cache-Control: max-age=0'); 

		$objWriter->save('php://output'); 
		exit();
	}
		
	public function export(){
		
		$this->session->data['import_error'] = array();
		
		$this->load->language('import/import');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('vendor/vendor');
		$this->load->model('vendor/import');
		//$this->load->model('catalog/product');
	
		
		$post = $this->request->post;
		
		//$categoryes = $this->_getCategories(0);
		
		$categoryes[0] = array(
							   'category_id' => 0,
							   'name' => '',
							   'name2' => '',
							   'parent_id' => 0
							   );
		
		$vendors = $this->model_vendor_vendor->getVendors();
		
		$statuses = array(
						  0 => 'Disabled',
						  1 => 'Enabled',
						  );
		
		
		$ABC = $this->_getABC();
		
		$export = array();
		
		require_once (DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
	
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		$i = 1;
		$ab = 0;
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'VendorId');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Name');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Email');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Main Phone number');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Fax');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Prefix');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'AccountNumber');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Mobile');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Web');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Home');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Work');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Country');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'State');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'City');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Zip');
		$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, 'Address');
		
		foreach($vendors as $vendor){
			
			$address = $this->model_vendor_vendor->getAddresses($vendor['vendor_id']);
			$affiliate = $this->model_vendor_vendor->getAffiliate($vendor['vendor_id']);
			$fields = $this->model_vendor_vendor->getAffiliate($vendor['vendor_id']);
			
			
			if(is_array($address) AND count($address) > 0){
				$address = array_shift($address);
			}else{
				$address = array(
								 'vendor_address_1' => '',
								 'city' => '',
								 'postcode' => '',
								 'country' => '',
								 );
			}
			
			if(!$affiliate){
				$affiliate = array(
								 'website' => '',
								 'city' => '',
								 'postcode' => '',
								 'country' => '',
								 );
			}
			
			$customs = json_decode($vebdor['vendor_field'], true);
			$state = $this->model_vendor_import->getStateByZoneId($address['zone_id']);
			
			if(!$state){
				$state = array('name' => '');
			}
			
			$i++;
			$ab = 0;
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['vendor_id']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['firstname']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['email']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['telephone']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['fax']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['prefix']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['account_number']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $vendor['telephone2']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $affiliate['website']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $customs[0]);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $customs[1]);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $address['country']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $state['name']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $address['city']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $address['postcode']);
			$objPHPExcel->getActiveSheet()->setCellValue($ABC[$ab++].$i, $address['vendor_address_1']);
			
		}
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="vendors.xls"'); 
		header('Cache-Control: max-age=0'); 

		$objWriter->save('php://output'); 
		exit();
	}
			
	public function import(){
		
		$this->load->model('vendor/vendor');
		$this->load->model('vendor/import');
		
		$this->initABC();
		
		$data = array();

		$imported_product_ids = array();
		
		$category_product_count = array();
		
		$product_option_list = array();
		$product_image_list = array();
		$product_attribute_list = array();
		
		if(isset($_FILES['upload']['tmp_name']) AND $_FILES['upload']['tmp_name'] != ''){
			
			$filename = $_FILES['upload']['name'];
			
			$tmpFilename = $_FILES['upload']['tmp_name'];

			include DIR_SYSTEM.'library/PHPExcel/PHPExcel.php';
			
			$worksheet = PHPExcel_IOFactory::load($tmpFilename)->getSheet(0);
			
			$worksheetRows = $worksheet->getHighestRow();

			$rows = array();
			$i = 2;
			while($i < ($worksheetRows + 1)){
				
				foreach($this->ABC as $abc){
					$rows[$i][trim((string)$worksheet->getCell($abc.'1' )->getValue())] = trim((string)$worksheet->getCell($abc.$i )->getValue());
				}
				$i++;	
			}
	

			$products = array();
			$count = 1;
			//проверка и поиск данных
			foreach($rows as $index => $row){
				
				//Проверяем на формулы
				foreach($row as $ind => $val){
					
					if(isset($val[0]) AND $val[0] == '='){
						$this->error['warning'] .= 'row: <b>' . $index . '</b> col: <b>' . $ind . '</b> - <b>formula</b> ('.(string)$val.')<br>';
					}
					
				}
				
				if(trim($row['Name']) == '') continue;
				
				if(!isset($row['VendorId'])){
					$row['vendor_id'] = false;
				}else{
					$row['vendor_id'] = (int)$row['VendorId'];
				}
				
				if(!$row['vendor_id']){
					$row['vendor_id'] = $this->model_vendor_vendor->getVendorIdOnEmail(trim($row['Email']));
				}
				
				if(!$row['vendor_id']){
					$row['vendor_id'] = $this->model_vendor_vendor->getVendorOnName(trim($row['Name']));	
				}
				
				$row['vendor_group_id'] = 9;
				$row['store_id'] = 0;
				$row['language_id'] = 0;
				$row['firstname'] = trim($row['Name']);
				$row['lastname'] = trim($row['Name']);
				$row['email'] = trim($row['Email']);
				$row['telephone'] = trim($row['Main Phone number']);
				$row['fax'] = trim($row['Fax']);
				$row['prefix'] = trim($row['Prefix']);
				$row['password'] = '';
				$row['salt'] = '';
				$row['cart'] = array();
				$row['wishlist'] = array();
				$row['newsletter'] = 0;
				$row['vendor_address_id'] =
				$row['vendor_field'] =
				$row['ip'] = '';
				$row['status'] = 1;
				$row['safe'] = 1;
				$row['token'] = '';
				$row['code'] = '';
				$row['account_number'] = trim($row['AccountNumber']);
				$row['shipping'] = '';
				$row['telephone2'] = trim($row['Mobile']);
				$row['website'] = trim($row['Web']);
				
				$row['vendor_field'] = array(
											 1 => trim($row['Home']),
											 2 => trim($row['Work']),
											 );
				
				
				$country_id = 223;
				
				$country_info = $this->model_vendor_import->getCountryByName(trim($row['Country']));
				
				if(isset($country_info['country_id'])){
					$country_id = (int)$country_info['country_id'];
				}
				
				$zone_id = 0;
				$zone_info = $this->model_vendor_import->getStateByName($country_id, trim($row['State']));
				if(isset($zone_info['zone_id'])){
					$zone_id = (int)$zone_info['zone_id'];
				}
				
				$row['vendor_address'] = array();
				$row['vendor_address'][] = array(
												'vendor_address_id' => '',
												'firstname' => trim($row['Name']),
												'lastname' => trim($row['Name']),
												'company' => trim($row['Web']),
												'vendor_address_1' => trim($row['Address']),
												'vendor_address_2' => '',
												'city' =>  trim($row['City']),
												'postcode' => trim($row['Zip']),
												'country_id' => $country_id,
												'zone_id' => $zone_id,
												 );
			
			
				$products[] = $row;
			}
				
			$this->session->data['success'] = 'Проверил ' . count($rows) . ' строк';
			
			
				
			$count = 0;
			
			$report = array();
			
			foreach($products as $index => $row){
				
					
				//$row['is_import'] = true;
					
				if((int)$row['vendor_id'] > 0){
					
					$this->model_vendor_vendor->editVendor((int)$row['vendor_id'], $row);
					$imported_product_ids[] = (int)$row['vendor_id'];
				}else{
	
					$imported_product_ids[] = $this->model_vendor_vendor->addVendor($row);
				}
				
				$report[] = $row['firstname'].' '. $row['lastname'];
				if($count++ > 3){
					//die('<br>111111111111');
				}
			}
				
			$this->session->data['success'] = 'Imported - ' . count($rows) . ' rows<br><br>' . implode('<br>', $report);
			
			$this->index();
		
				
		}
	}
	
	
}