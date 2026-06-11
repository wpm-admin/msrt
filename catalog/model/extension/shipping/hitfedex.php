<?php
class ModelExtensionShippinghitfedex extends Model {
	function getQuote($address) {
		
		$this->load->language('extension/shipping/hitfedex');
		
		if($this->config->get('shipping_hitfedex_realtime_rates') == true)
		{
			$status = true;
		}
		else
		{
			$status = false;
		}
		
		$error = '';

		$quote_data = array();
		
		if ($status) {
			
			$products = $this->cart->getProducts();
			$county_code_to = $address['iso_code_2'];
			$fedex_country = $county_code_to;//$this->config->get('shipping_hitfedex_country_code');
			$total_config_values = $this->hit_get_currency();
			$selected_currency = $total_config_values[$fedex_country]['currency'];
			$get_product = array();
			foreach($products as $sing_product)
			{
				if(isset($sing_product['shipping']) && $sing_product['shipping'] == 1)
				{
					$get_product[] = $sing_product;
				}
			}
			
			
			$fedex_packs		=	$this->hit_get_fedex_packages( $get_product,$selected_currency );
			$total_value= 0;
			foreach($fedex_packs as $pack)
			{
				$total_value += $pack['InsuredValue']['Amount'];
			}
			
			if (!$this->config->get('shipping_hitfedex_test')) {
				$url = 'https://ws.fedex.com:443/web-services/rate';
			} else {
				$url = 'https://wsbeta.fedex.com:443/web-services/rate';
			}
			
			$pieces = $this->hit_get_package_piece($fedex_packs);
			$weight_unit = ($this->config->get('shipping_hitfedex_weight') == true) ? 'LB' : 'KG';
			$dim_unit = ($this->config->get('shipping_hitfedex_weight') == true) ? 'IN' : 'CM';
			$fetch_accountrates = ($this->config->get('shipping_hitfedex_rate_type') == 'ACCOUNT') ? "<PaymentAccountNumber>" . $this->config->get('shipping_hitfedex_account') . "</PaymentAccountNumber>" : "";
		
			$mailing_date = date('Y-m-d');
			$mailing_datetime = date('c');
			$origin_postcode_city = $this->hit_get_postcode_city($this->config->get('shipping_hitfedex_country_code'), $this->config->get('shipping_hitfedex_city'), $this->config->get('shipping_hitfedex_postcode'));
			//$total_value = $this->cart->get_total();
			$is_dutiable = ($address['iso_code_2'] == $this->config->get('shipping_hitfedex_country_code') || $this->hit_fedex_is_eu_country($this->config->get('shipping_hitfedex_country_code'), $address['iso_code_2'])) ? "N" : "Y";
			$dutiable_content = ($is_dutiable == "Y") ? "<Dutiable><DeclaredCurrency>{$selected_currency}</DeclaredCurrency><DeclaredValue>{$total_value}</DeclaredValue></Dutiable>" : "";
			
			$insurance_details = ($this->config->get('shipping_hitfedex_insurance') == true) ? "<InsuredValue>". $total_value ."</InsuredValue><InsuredCurrency>". $this->config->get('config_currency') ."</InsuredCurrency>" : "";
			$additional_insurance_details = ($this->config->get('shipping_hitfedex_insurance') == true)  ? "<QtdShp><QtdShpExChrg><SpecialServiceType>II</SpecialServiceType><LocalSpecialServiceType>XCH</LocalSpecialServiceType></QtdShpExChrg></QtdShp>" : ""; //
			
			$destination_postcode_city = $this->hit_get_postcode_city($address['iso_code_2'], $address['city'], $address['postcode']);
			// echo "<pre>";
			
			// print_r($origin_postcode_city);
			// print_r($destination_postcode_city);
			// $res=$this->config->get('shipping_hitfedex_city');
			// print_r($address['iso_code_2']);
		
			//  die();
		
			// Whoever introduced xml to shipping companies should be flogged
			///////
			$xmlRequest = file_get_contents(dirname(__FILE__).'/rate.xml');
		
			$xmlRequest = str_replace('{key}',$this->config->get('shipping_hitfedex_key'),$xmlRequest);
			$xmlRequest = str_replace('{pwd}',$this->config->get('shipping_hitfedex_password'),$xmlRequest);
			$xmlRequest = str_replace('{Account_num}',$this->config->get('shipping_hitfedex_account'),$xmlRequest);
			$xmlRequest = str_replace('{meter_number}',$this->config->get('shipping_hitfedex_meter'),$xmlRequest);
			$xmlRequest = str_replace('{timestramp}',date( 'c' , strtotime( '+1 Weekday' ) ),$xmlRequest);
			$xmlRequest = str_replace('{s_postal}',$this->config->get('shipping_hitfedex_postcode'),$xmlRequest);
			$xmlRequest = str_replace('{s_countr}',$this->config->get('shipping_hitfedex_country_code'),$xmlRequest);
			
			if(isset($general_settings['del_home']) && $general_settings['del_home'] == true)
			{
				$xmlRequest = str_replace('{r_res}','true',$xmlRequest);
			
			}else
			{
				$xmlRequest = str_replace('{r_res}','false',$xmlRequest);
			}
			//##############################################
			// $address_id = $this->customer->getAddressId();
// $this->load->model('account/address');
// $address = $this->model_account_address->getAddress($address_id); 
//$r_postal = $address['iso_code_2'];
			// print_r($address);
			// echo 'hiiiiiiiii';
			
			
			$rate_type=$this->config->get('shipping_hitfedex_rate_type');
			//############################################
			//$state_details = $this->db->query('SELECT `iso_code` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)($this->customer->getAddressId()));
		
			$request_type = ($rate_type = 'LIST') ? 'LIST' : 'NONE';
			
			$xmlRequest = str_replace('{ac_rate}',$request_type,$xmlRequest);
			//$xmlRequest = str_replace('{r_city}',strtoupper( $customerAddress->city ),$xmlRequest);
			//$xmlRequest = str_replace('{r_state}',$state_details['iso_code'],$xmlRequest);
			$xmlRequest = str_replace('{r_postal}',str_replace( ' ', '', strtoupper( $address['postcode'] ) ),$xmlRequest);
			$xmlRequest = str_replace('{r_county}',$address['iso_code_2'],$xmlRequest);
			$xmlRequest = str_replace('{currency}',$selected_currency,$xmlRequest);
			$xmlRequest = str_replace('{weightunit}',$weight_unit,$xmlRequest);
			
			$xml_line_item ='';
			$total_weight = 0;
			$total_packages = 0;
			if ($fedex_packs) {
				foreach ($fedex_packs as $key => $parcel) {
					$xml_line_item .= '<v22:RequestedPackageLineItems>
					<v22:SequenceNumber>'.($key+1).'</v22:SequenceNumber>
					<v22:GroupNumber>'.$parcel['GroupNumber'].'</v22:GroupNumber>
					<v22:GroupPackageCount>'.$parcel['GroupPackageCount'].'</v22:GroupPackageCount>
					<v22:Weight>
					<v22:Units>'.$parcel['Weight']['Units'].'</v22:Units>
					<v22:Value>'.$parcel['Weight']['Value'].'</v22:Value>
					</v22:Weight>';
					if(isset($parcel['Dimensions']))
					{
						$xml_line_item .='<v22:Dimensions>
						<v22:Length>'.$parcel['Dimensions']['Length'].'</v22:Length>
						<v22:Width>'.$parcel['Dimensions']['Width'].'</v22:Width>
						<v22:Height>'.$parcel['Dimensions']['Height'].'</v22:Height>
						<v22:Units>'.$parcel['Dimensions']['Units'].'</v22:Units>
					</v22:Dimensions>';
					}
									   
					$xml_line_item .='</v22:RequestedPackageLineItems>';
					$total_weight += $parcel['Weight']['Value'];
					$total_packages += $parcel['GroupPackageCount'];			
				}
			}
			$xmlRequest = str_replace('{totalweight}',$total_weight,$xmlRequest);
			$xmlRequest = str_replace('{package_count}',$total_packages,$xmlRequest);
			$xmlRequest = str_replace('{line_items}',$xml_line_item,$xmlRequest);
			//////
			
			$request = $xmlRequest;
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING       => "",
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_HEADER         => false,
				CURLOPT_TIMEOUT        => 60,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => 'POST',
			));	
			
			$result = utf8_encode(curl_exec($curl));
			$result_xml = str_replace(array(':','-'), '', $result);
			// echo "<pre>";
			// print_r($result);
			// die();
			
				$xmlRequest = '';
				libxml_use_internal_errors(true);
				if(!empty($result))
				{
					$xmlRequest = simplexml_load_string(utf8_encode($result_xml));
				}
				 $result = $xmlRequest;
				//  echo "<pre>";
				//  print_r($result);
				//  die();	
			if($this->config->get('shipping_hitfedex_front_end_logs') == true)
			{
				echo "<pre>";
				print_r(htmlspecialchars($request));
				print_r($result);
				die();
			}
				//echo "<PRE>";		print_r($result);		die();	//	die();	
			if ($result && !empty($result->SOAPENVBody->RateReply->RateReplyDetails)) {
				$rate_cost=$rate_taxes=$rate_title=array();
			
				foreach ($result->SOAPENVBody->RateReply->RateReplyDetails as $quote) {
					
					//$rate_code = ((string) $quote->GlobalProductCode);
					$rate_title = ((string) $quote->ServiceType);
					//$delivery_date = ((string) $quote->DeliveryDate);
					$rate_cost = (float)((string) $quote->RatedShipmentDetails->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes->Amount);
					$rate_taxes = (float)((string) $quote->RatedShipmentDetails->ShipmentRateDetail->TotalTaxes->Amount);
					//echo "<pre>";
					// print_r($rate_cost);
					// print_r($rate_title);
					$timestamp = ((string) $quote->DeliveryTimestamp);
					//die();
					$etd = '';
					if($this->config->get('shipping_hitfedex_show_etd') == true){

						$dttm = $timestamp;
						$dt = strstr($dttm, 'T', true);
						$tm = substr($dttm, strripos($dttm,'T')+strlen('T'));
						$formated_date = DateTime::createFromFormat('Ymd', $dt);
						$formated_time = DateTime::createFromFormat('His',$tm);
						$etd_date = $formated_date->format('d/m/Y');
						$etd_time = $formated_time->format('H:i:s');
						$etd = " (Etd.Delivery ".$etd_date.", ".$etd_time.")";
					}
					$selected_services_aaray = $this->config->get('shipping_hitfedex_service');
					if(in_array($rate_title,$selected_services_aaray))
					{
					$quote_data[$rate_title] = array(
							'code'         => 'hitfedex.' . $rate_title,
							'title'        => ''.str_replace("_",' ',$rate_title).$etd,
							'cost'         => $this->currency->convert($rate_cost, $selected_currency, $this->config->get('config_currency')),
							'tax_class_id' => '',
							'text'         => $this->currency->format($this->currency->convert($rate_cost, $selected_currency, $this->session->data['currency']), $this->session->data['currency'], 1.0000000)
						);
						// print_r($quote_data);
						// die();
						
					}
				}
			}
		}
//echo "<pre>";		//	print_r($result);		//	die();
		$method_data = array();

		if ($quote_data || $error) {
			$title = "FEDEX";//$this->language->get('text_title');

		//	if ($this->config->get('shipping_fedex_display_weight')) {
			//	$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('shipping_fedex_weight_class_id')) . ')';
		//	}

			$method_data = array(
				'code'       => 'fedex',
				'title'      => $title,
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_hitfedex_sort_order'),
				'error'      => $error
			);
		}
		return $method_data;
	}

	
	// public function hit_get_fedex_packages($package,$general_settings,$orderCurrency,$chk = false)
	// {
	// 	switch ($general_settings['packing_type']) {
	// 		case 'box' :
	// 			return $this->box_shipping($package,$general_settings,$orderCurrency,$chk);
	// 			break;
	// 		case 'weight_based' :
	// 			return $this->weight_based_shipping($package,$general_settings,$orderCurrency,$chk);
	// 			break;
	// 		case 'per_item' :
	// 		default :
	// 			return $this->per_item_shipping($package,$general_settings,$orderCurrency,$chk);
	// 			break;
	// 	}
	// }

	private function hit_create_soap_client( $wsdl ){
		
		if (!extension_loaded('soap'))
				return false;
					
		ini_set("soap.wsdl_cache_enabled", "0");
 


		try {  
				$soapclient = $client = new SoapClient($wsdl, array('trace' => 1,'keep_alive' => false,
            'soap_version'           => SOAP_1_1,
            'cache_wsdl'             => WSDL_CACHE_NONE,'stream_context' => stream_context_create(
    array(
      'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
      )
    ))));
		} catch (SoapFault $E) {  
		//	echo $E->faultstring; 
			
		}
		
		return $soapclient;
	}

	public function hit_fedex_is_eu_country ($countrycode, $destinationcode) {
		$eu_countrycodes = array(
			'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 
			'ES', 'FI', 'FR', 'GB', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV',
			'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
			'HR', 'GR'

		);
		return(in_array($countrycode, $eu_countrycodes) && in_array($destinationcode, $eu_countrycodes));
	}
	public function hit_get_fedex_packages($package,$orderCurrency,$chk = false)
	{
		switch ($this->config->get('shipping_hitfedex_packing_type')) {
			case 'weight_based' :
				return $this->weight_based_shipping($package,$orderCurrency,$chk);
				break;
			case 'per_item' :
			default :
				return $this->per_item_shipping($package,$orderCurrency,$chk);
				break;
		}
	}
	
	public function weight_based_shipping($package,$orderCurrency,$chk='')
		{
			$maximum_weight = ($this->config->get('shipping_hitfedex_wight_b') !='') ? $this->config->get('shipping_hitfedex_wight_b') : '50';
								
			if ( ! class_exists( 'WeightPack' ) ) {
				include_once 'class-hit-weight-packing.php';
			}
			
			$weight_pack=new WeightPack('simple');
			$weight_pack->set_max_weight($maximum_weight);
			
			$package_total_weight = 0;
			$insured_value = 0;
			$ctr = 0;
			foreach ($package as $item_id => $values) {
				$ctr++;
				
				
				if (!$values['weight']) {
					$values['weight'] = 0.001;
					
				}
				
				$chk_qty = $values['quantity'];

				$weight_pack->add_item($values['weight'], $values, 1);
			}

			$pack   =   $weight_pack->pack_items();  
			$errors =   $pack->get_errors();
			if( !empty($errors) ){
				//do nothing
				return;
			} else {
				$boxes    =   $pack->get_packed_boxes();
				$unpacked_items =   $pack->get_unpacked_items();

				$insured_value        =   0;

				$packages      =   array_merge( $boxes, $unpacked_items ); // merge items if unpacked are allowed
				$package_count  =   sizeof($packages);
				// get all items to pass if item info in box is not distinguished
				$packable_items =   $weight_pack->get_packable_items();
				$all_items    =   array();
				if(is_array($packable_items)){
					foreach($packable_items as $packable_item){
						$all_items[]    =   $packable_item['data'];
					}
				}
				//pre($packable_items);
				$order_total = '';

				$to_ship  = array();
				$group_id = 1;
				foreach($packages as $package){//pre($package);
					$packed_products = array();
					
					if(($package_count  ==  1) && isset($order_total)){
						$insured_value  =   $values['price'] * $chk_qty;
					}else{
						$insured_value  =   0;
						if(!empty($package['items'])){
							foreach($package['items'] as $item){               

								$insured_value        =   $insured_value; //+ $item->price;
							}
						}else{
							if( isset($order_total) && $package_count){
								$insured_value  =   $order_total/$package_count;
							}
						}
					}
					$packed_products    =   isset($package['items']) ? $package['items'] : $all_items;
					// Creating package request
					$package_total_weight   = $package['weight'];

					$insurance_array = array(
						'Amount' => $insured_value,
						'Currency' => $orderCurrency
					);

					$group = array(
						'GroupNumber' => $group_id,
						'GroupPackageCount' => 1,
						'Weight' => array(
						'Value' => round($package_total_weight, 3),
						'Units' => ($this->config->get('shipping_hitfedex_weight') == true) ? 'LB' : 'KG'
					),
						'packed_products' => $packed_products,
					);
					$group['InsuredValue'] = $insurance_array;
					$group['packtype'] = 'OD';

					$to_ship[] = $group;
					$group_id++;
				}
			}
			return $to_ship;
		}
	
	private function per_item_shipping($package,$orderCurrency,$chk = false) {
		$to_ship = array();
		$group_id = 1;

		// Get weight of order
		foreach ($package as $item_id => $values) {
		

			if (!$values['weight']) {
				$values['weight'] = 0.001;
			}

			$group = array();
			$insurance_array = array(
				'Amount' => round($values['price']),
				'Currency' => $orderCurrency
			);

			if($values['weight'] < 0.001){
				$fedex_per_item_weight = 0.001;
			}else{
				$fedex_per_item_weight = round(($values['weight']/$values['quantity']), 3);
			}
			$group = array(
				'GroupNumber' => $group_id,
				'GroupPackageCount' => 1,
				'Weight' => array(
				'Value' => $fedex_per_item_weight,
				'Units' => ($this->config->get('shipping_hitfedex_weight') == true) ? 'LB' : 'KG'
			),
				'packed_products' => $package
			);

			if ($values['width'] && $values['height'] && $values['length']) {

				$group['Dimensions'] = array(
					'Length' => max(1, round($values['length'],3)),
					'Width' => max(1, round($values['width'],3)),
					'Height' => max(1, round($values['height'],3)),
					'Units' => ($this->config->get('shipping_hitfedex_weight') == true) ? 'IN' : 'CM'
				);
			}
			$group['packtype'] = $this->config->get('shipping_hitfedex_per_item');
			$group['InsuredValue'] = $insurance_array;

			$chk_qty = $chk ? $values['quantity'] : $values['quantity'];

			for ($i = 0; $i < $chk_qty; $i++)
				$to_ship[] = $group;

			$group_id++;
		}

		return $to_ship;
	}
	private function hit_get_postcode_city($country, $city, $postcode) {
		$no_postcode_country = array('AE', 'AF', 'AG', 'AI', 'AL', 'AN', 'AO', 'AW', 'BB', 'BF', 'BH', 'BI', 'BJ', 'BM', 'BO', 'BS', 'BT', 'BW', 'BZ', 'CD', 'CF', 'CG', 'CI', 'CK',
									 'CL', 'CM', 'CO', 'CR', 'CV', 'DJ', 'DM', 'DO', 'EC', 'EG', 'ER', 'ET', 'FJ', 'FK', 'GA', 'GD', 'GH', 'GI', 'GM', 'GN', 'GQ', 'GT', 'GW', 'GY', 'HK', 'HN', 'HT', 'IE', 'IQ', 'IR',
									 'JM', 'JO', 'KE', 'KH', 'KI', 'KM', 'KN', 'KP', 'KW', 'KY', 'LA', 'LB', 'LC', 'LK', 'LR', 'LS', 'LY', 'ML', 'MM', 'MO', 'MR', 'MS', 'MT', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'NI',
									 'NP', 'NR', 'NU', 'OM', 'PA', 'PE', 'PF', 'PY', 'QA', 'RW', 'SA', 'SB', 'SC', 'SD', 'SL', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'TC', 'TD', 'TG', 'TL', 'TO', 'TT', 'TV', 'TZ',
									 'UG', 'UY', 'VC', 'VE', 'VG', 'VN', 'VU', 'WS', 'XA', 'XB', 'XC', 'XE', 'XL', 'XM', 'XN', 'XS', 'YE', 'ZM', 'ZW');

		$postcode_city = !in_array( $country, $no_postcode_country ) ? $postcode_city = "<Postalcode>{$postcode}</Postalcode>" : '';
		if( !empty($city) ){
			$postcode_city .= "<City>{$city}</City>";
		}
		return $postcode_city;
	}
	private function hit_get_package_total_value($fedex_packages) {
		$total_value = 0;
		if ($fedex_packages) {
			foreach ($fedex_packages as $key => $parcel) {
				$total_value += $parcel['InsuredValue']['Amount'] * $parcel['GroupPackageCount'];
			}
		}
		return $total_value;
	}
	private function hit_get_package_piece($fedex_packages) {
		$pieces = "";
		if ($fedex_packages) {
			foreach ($fedex_packages as $key => $parcel) {
				$pack_type = $this->hit_get_pack_type($parcel['packtype']);
				$index = $key + 1;
				$pieces .= '<Piece><PieceID>' . $index . '</PieceID>';
				$pieces .= '<PackageTypeCode>'.$pack_type.'</PackageTypeCode>';
				if( !empty($parcel['Dimensions']['Height']) && !empty($parcel['Dimensions']['Length']) && !empty($parcel['Dimensions']['Width']) ){
					$pieces .= '<Height>' . $parcel['Dimensions']['Height'] . '</Height>';
					$pieces .= '<Depth>' . $parcel['Dimensions']['Length'] . '</Depth>';
					$pieces .= '<Width>' . $parcel['Dimensions']['Width'] . '</Width>';
				}
				$package_total_weight   =(string) $parcel['Weight']['Value'];
				$package_total_weight   = str_replace(',','.',$package_total_weight);
				if($package_total_weight<0.001){
					$package_total_weight = 0.001;
				}else{
					$package_total_weight = round((float)$package_total_weight,3);
				}
				$pieces .= '<Weight>' . $package_total_weight . '</Weight></Piece>';
			}
		}
		return $pieces;
	}
	private function hit_get_pack_type($selected) {
		$pack_type = 'BOX';
		if ($selected == 'FLY') {
			$pack_type = 'FLY';
		} 
		return $pack_type;    
	}
	
	public function hit_get_currency()
	{
		
		$value = array();
	$value['AD'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['AE'] = array('region' => 'AP', 'currency' =>'AED', 'weight' => 'KG_CM');
	$value['AF'] = array('region' => 'AP', 'currency' =>'AFN', 'weight' => 'KG_CM');
	$value['AG'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
	$value['AI'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
	$value['AL'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['AM'] = array('region' => 'AP', 'currency' =>'AMD', 'weight' => 'KG_CM');
	$value['AN'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'KG_CM');
	$value['AO'] = array('region' => 'AP', 'currency' =>'AOA', 'weight' => 'KG_CM');
	$value['AR'] = array('region' => 'AM', 'currency' =>'ARS', 'weight' => 'KG_CM');
	$value['AS'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['AT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['AU'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
	$value['AW'] = array('region' => 'AM', 'currency' =>'AWG', 'weight' => 'LB_IN');
	$value['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
	$value['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
	$value['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
	$value['BA'] = array('region' => 'AP', 'currency' =>'BAM', 'weight' => 'KG_CM');
	$value['BB'] = array('region' => 'AM', 'currency' =>'BBD', 'weight' => 'LB_IN');
	$value['BD'] = array('region' => 'AP', 'currency' =>'BDT', 'weight' => 'KG_CM');
	$value['BE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['BF'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
	$value['BG'] = array('region' => 'EU', 'currency' =>'BGN', 'weight' => 'KG_CM');
	$value['BH'] = array('region' => 'AP', 'currency' =>'BHD', 'weight' => 'KG_CM');
	$value['BI'] = array('region' => 'AP', 'currency' =>'BIF', 'weight' => 'KG_CM');
	$value['BJ'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
	$value['BM'] = array('region' => 'AM', 'currency' =>'BMD', 'weight' => 'LB_IN');
	$value['BN'] = array('region' => 'AP', 'currency' =>'BND', 'weight' => 'KG_CM');
	$value['BO'] = array('region' => 'AM', 'currency' =>'BOB', 'weight' => 'KG_CM');
	$value['BR'] = array('region' => 'AM', 'currency' =>'BRL', 'weight' => 'KG_CM');
	$value['BS'] = array('region' => 'AM', 'currency' =>'BSD', 'weight' => 'LB_IN');
	$value['BT'] = array('region' => 'AP', 'currency' =>'BTN', 'weight' => 'KG_CM');
	$value['BW'] = array('region' => 'AP', 'currency' =>'BWP', 'weight' => 'KG_CM');
	$value['BY'] = array('region' => 'AP', 'currency' =>'BYR', 'weight' => 'KG_CM');
	$value['BZ'] = array('region' => 'AM', 'currency' =>'BZD', 'weight' => 'KG_CM');
	$value['CA'] = array('region' => 'AM', 'currency' =>'CAD', 'weight' => 'LB_IN');
	$value['CF'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
	$value['CG'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
	$value['CH'] = array('region' => 'EU', 'currency' =>'CHF', 'weight' => 'KG_CM');
	$value['CI'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
	$value['CK'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
	$value['CL'] = array('region' => 'AM', 'currency' =>'CLP', 'weight' => 'KG_CM');
	$value['CM'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
	$value['CN'] = array('region' => 'AP', 'currency' =>'CNY', 'weight' => 'KG_CM');
	$value['CO'] = array('region' => 'AM', 'currency' =>'COP', 'weight' => 'KG_CM');
	$value['CR'] = array('region' => 'AM', 'currency' =>'CRC', 'weight' => 'KG_CM');
	$value['CU'] = array('region' => 'AM', 'currency' =>'CUC', 'weight' => 'KG_CM');
	$value['CV'] = array('region' => 'AP', 'currency' =>'CVE', 'weight' => 'KG_CM');
	$value['CY'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['CZ'] = array('region' => 'EU', 'currency' =>'CZF', 'weight' => 'KG_CM');
	$value['DE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['DJ'] = array('region' => 'EU', 'currency' =>'DJF', 'weight' => 'KG_CM');
	$value['DK'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
	$value['DM'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
	$value['DO'] = array('region' => 'AP', 'currency' =>'DOP', 'weight' => 'LB_IN');
	$value['DZ'] = array('region' => 'AM', 'currency' =>'DZD', 'weight' => 'KG_CM');
	$value['EC'] = array('region' => 'EU', 'currency' =>'USD', 'weight' => 'KG_CM');
	$value['EE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['EG'] = array('region' => 'AP', 'currency' =>'EGP', 'weight' => 'KG_CM');
	$value['ER'] = array('region' => 'EU', 'currency' =>'ERN', 'weight' => 'KG_CM');
	$value['ES'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['ET'] = array('region' => 'AU', 'currency' =>'ETB', 'weight' => 'KG_CM');
	$value['FI'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['FJ'] = array('region' => 'AP', 'currency' =>'FJD', 'weight' => 'KG_CM');
	$value['FK'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
	$value['FM'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['FO'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
	$value['FR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['GA'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
	$value['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
	$value['GD'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
	$value['GE'] = array('region' => 'AM', 'currency' =>'GEL', 'weight' => 'KG_CM');
	$value['GF'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['GG'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
	$value['GH'] = array('region' => 'AP', 'currency' =>'GBS', 'weight' => 'KG_CM');
	$value['GI'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
	$value['GL'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
	$value['GM'] = array('region' => 'AP', 'currency' =>'GMD', 'weight' => 'KG_CM');
	$value['GN'] = array('region' => 'AP', 'currency' =>'GNF', 'weight' => 'KG_CM');
	$value['GP'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['GQ'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
	$value['GR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['GT'] = array('region' => 'AM', 'currency' =>'GTQ', 'weight' => 'KG_CM');
	$value['GU'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['GW'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
	$value['GY'] = array('region' => 'AP', 'currency' =>'GYD', 'weight' => 'LB_IN');
	$value['HK'] = array('region' => 'AM', 'currency' =>'HKD', 'weight' => 'KG_CM');
	$value['HN'] = array('region' => 'AM', 'currency' =>'HNL', 'weight' => 'KG_CM');
	$value['HR'] = array('region' => 'AP', 'currency' =>'HRK', 'weight' => 'KG_CM');
	$value['HT'] = array('region' => 'AM', 'currency' =>'HTG', 'weight' => 'LB_IN');
	$value['HU'] = array('region' => 'EU', 'currency' =>'HUF', 'weight' => 'KG_CM');
	$value['IC'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['ID'] = array('region' => 'AP', 'currency' =>'IDR', 'weight' => 'KG_CM');
	$value['IE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['IL'] = array('region' => 'AP', 'currency' =>'ILS', 'weight' => 'KG_CM');
	$value['IN'] = array('region' => 'AP', 'currency' =>'INR', 'weight' => 'KG_CM');
	$value['IQ'] = array('region' => 'AP', 'currency' =>'IQD', 'weight' => 'KG_CM');
	$value['IR'] = array('region' => 'AP', 'currency' =>'IRR', 'weight' => 'KG_CM');
	$value['IS'] = array('region' => 'EU', 'currency' =>'ISK', 'weight' => 'KG_CM');
	$value['IT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['JE'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
	$value['JM'] = array('region' => 'AM', 'currency' =>'JMD', 'weight' => 'KG_CM');
	$value['JO'] = array('region' => 'AP', 'currency' =>'JOD', 'weight' => 'KG_CM');
	$value['JP'] = array('region' => 'AP', 'currency' =>'JPY', 'weight' => 'KG_CM');
	$value['KE'] = array('region' => 'AP', 'currency' =>'KES', 'weight' => 'KG_CM');
	$value['KG'] = array('region' => 'AP', 'currency' =>'KGS', 'weight' => 'KG_CM');
	$value['KH'] = array('region' => 'AP', 'currency' =>'KHR', 'weight' => 'KG_CM');
	$value['KI'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
	$value['KM'] = array('region' => 'AP', 'currency' =>'KMF', 'weight' => 'KG_CM');
	$value['KN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
	$value['KP'] = array('region' => 'AP', 'currency' =>'KPW', 'weight' => 'LB_IN');
	$value['KR'] = array('region' => 'AP', 'currency' =>'KRW', 'weight' => 'KG_CM');
	$value['KV'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['KW'] = array('region' => 'AP', 'currency' =>'KWD', 'weight' => 'KG_CM');
	$value['KY'] = array('region' => 'AM', 'currency' =>'KYD', 'weight' => 'KG_CM');
	$value['KZ'] = array('region' => 'AP', 'currency' =>'KZF', 'weight' => 'LB_IN');
	$value['LA'] = array('region' => 'AP', 'currency' =>'LAK', 'weight' => 'KG_CM');
	$value['LB'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
	$value['LC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'KG_CM');
	$value['LI'] = array('region' => 'AM', 'currency' =>'CHF', 'weight' => 'LB_IN');
	$value['LK'] = array('region' => 'AP', 'currency' =>'LKR', 'weight' => 'KG_CM');
	$value['LR'] = array('region' => 'AP', 'currency' =>'LRD', 'weight' => 'KG_CM');
	$value['LS'] = array('region' => 'AP', 'currency' =>'LSL', 'weight' => 'KG_CM');
	$value['LT'] = array('region' => 'EU', 'currency' =>'LTL', 'weight' => 'KG_CM');
	$value['LU'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['LV'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['LY'] = array('region' => 'AP', 'currency' =>'LYD', 'weight' => 'KG_CM');
	$value['MA'] = array('region' => 'AP', 'currency' =>'MAD', 'weight' => 'KG_CM');
	$value['MC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['MD'] = array('region' => 'AP', 'currency' =>'MDL', 'weight' => 'KG_CM');
	$value['ME'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['MG'] = array('region' => 'AP', 'currency' =>'MGA', 'weight' => 'KG_CM');
	$value['MH'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['MK'] = array('region' => 'AP', 'currency' =>'MKD', 'weight' => 'KG_CM');
	$value['ML'] = array('region' => 'AP', 'currency' =>'COF', 'weight' => 'KG_CM');
	$value['MM'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
	$value['MN'] = array('region' => 'AP', 'currency' =>'MNT', 'weight' => 'KG_CM');
	$value['MO'] = array('region' => 'AP', 'currency' =>'MOP', 'weight' => 'KG_CM');
	$value['MP'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['MQ'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['MR'] = array('region' => 'AP', 'currency' =>'MRO', 'weight' => 'KG_CM');
	$value['MS'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
	$value['MT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['MU'] = array('region' => 'AP', 'currency' =>'MUR', 'weight' => 'KG_CM');
	$value['MV'] = array('region' => 'AP', 'currency' =>'MVR', 'weight' => 'KG_CM');
	$value['MW'] = array('region' => 'AP', 'currency' =>'MWK', 'weight' => 'KG_CM');
	$value['MX'] = array('region' => 'AM', 'currency' =>'MXN', 'weight' => 'KG_CM');
	$value['MY'] = array('region' => 'AP', 'currency' =>'MYR', 'weight' => 'KG_CM');
	$value['MZ'] = array('region' => 'AP', 'currency' =>'MZN', 'weight' => 'KG_CM');
	$value['NA'] = array('region' => 'AP', 'currency' =>'NAD', 'weight' => 'KG_CM');
	$value['NC'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
	$value['NE'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
	$value['NG'] = array('region' => 'AP', 'currency' =>'NGN', 'weight' => 'KG_CM');
	$value['NI'] = array('region' => 'AM', 'currency' =>'NIO', 'weight' => 'KG_CM');
	$value['NL'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['NO'] = array('region' => 'EU', 'currency' =>'NOK', 'weight' => 'KG_CM');
	$value['NP'] = array('region' => 'AP', 'currency' =>'NPR', 'weight' => 'KG_CM');
	$value['NR'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
	$value['NU'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
	$value['NZ'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
	$value['OM'] = array('region' => 'AP', 'currency' =>'OMR', 'weight' => 'KG_CM');
	$value['PA'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
	$value['PE'] = array('region' => 'AM', 'currency' =>'PEN', 'weight' => 'KG_CM');
	$value['PF'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
	$value['PG'] = array('region' => 'AP', 'currency' =>'PGK', 'weight' => 'KG_CM');
	$value['PH'] = array('region' => 'AP', 'currency' =>'PHP', 'weight' => 'KG_CM');
	$value['PK'] = array('region' => 'AP', 'currency' =>'PKR', 'weight' => 'KG_CM');
	$value['PL'] = array('region' => 'EU', 'currency' =>'PLN', 'weight' => 'KG_CM');
	$value['PR'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['PT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['PW'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
	$value['PY'] = array('region' => 'AM', 'currency' =>'PYG', 'weight' => 'KG_CM');
	$value['QA'] = array('region' => 'AP', 'currency' =>'QAR', 'weight' => 'KG_CM');
	$value['RE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['RO'] = array('region' => 'EU', 'currency' =>'RON', 'weight' => 'KG_CM');
	$value['RS'] = array('region' => 'AP', 'currency' =>'RSD', 'weight' => 'KG_CM');
	$value['RU'] = array('region' => 'AP', 'currency' =>'RUB', 'weight' => 'KG_CM');
	$value['RW'] = array('region' => 'AP', 'currency' =>'RWF', 'weight' => 'KG_CM');
	$value['SA'] = array('region' => 'AP', 'currency' =>'SAR', 'weight' => 'KG_CM');
	$value['SB'] = array('region' => 'AP', 'currency' =>'SBD', 'weight' => 'KG_CM');
	$value['SC'] = array('region' => 'AP', 'currency' =>'SCR', 'weight' => 'KG_CM');
	$value['SD'] = array('region' => 'AP', 'currency' =>'SDG', 'weight' => 'KG_CM');
	$value['SE'] = array('region' => 'EU', 'currency' =>'SEK', 'weight' => 'KG_CM');
	$value['SG'] = array('region' => 'AP', 'currency' =>'SGD', 'weight' => 'KG_CM');
	$value['SH'] = array('region' => 'AP', 'currency' =>'SHP', 'weight' => 'KG_CM');
	$value['SI'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['SK'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['SL'] = array('region' => 'AP', 'currency' =>'SLL', 'weight' => 'KG_CM');
	$value['SM'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['SN'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
	$value['SO'] = array('region' => 'AM', 'currency' =>'SOS', 'weight' => 'KG_CM');
	$value['SR'] = array('region' => 'AM', 'currency' =>'SRD', 'weight' => 'KG_CM');
	$value['SS'] = array('region' => 'AP', 'currency' =>'SSP', 'weight' => 'KG_CM');
	$value['ST'] = array('region' => 'AP', 'currency' =>'STD', 'weight' => 'KG_CM');
	$value['SV'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
	$value['SY'] = array('region' => 'AP', 'currency' =>'SYP', 'weight' => 'KG_CM');
	$value['SZ'] = array('region' => 'AP', 'currency' =>'SZL', 'weight' => 'KG_CM');
	$value['TC'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['TD'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
	$value['TG'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
	$value['TH'] = array('region' => 'AP', 'currency' =>'THB', 'weight' => 'KG_CM');
	$value['TJ'] = array('region' => 'AP', 'currency' =>'TJS', 'weight' => 'KG_CM');
	$value['TL'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
	$value['TN'] = array('region' => 'AP', 'currency' =>'TND', 'weight' => 'KG_CM');
	$value['TO'] = array('region' => 'AP', 'currency' =>'TOP', 'weight' => 'KG_CM');
	$value['TR'] = array('region' => 'AP', 'currency' =>'TRY', 'weight' => 'KG_CM');
	$value['TT'] = array('region' => 'AM', 'currency' =>'TTD', 'weight' => 'LB_IN');
	$value['TV'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
	$value['TW'] = array('region' => 'AP', 'currency' =>'TWD', 'weight' => 'KG_CM');
	$value['TZ'] = array('region' => 'AP', 'currency' =>'TZS', 'weight' => 'KG_CM');
	$value['UA'] = array('region' => 'AP', 'currency' =>'UAH', 'weight' => 'KG_CM');
	$value['UG'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
	$value['US'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['UY'] = array('region' => 'AM', 'currency' =>'UYU', 'weight' => 'KG_CM');
	$value['UZ'] = array('region' => 'AP', 'currency' =>'UZS', 'weight' => 'KG_CM');
	$value['VC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
	$value['VE'] = array('region' => 'AM', 'currency' =>'VEF', 'weight' => 'KG_CM');
	$value['VG'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['VI'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
	$value['VN'] = array('region' => 'AP', 'currency' =>'VND', 'weight' => 'KG_CM');
	$value['VU'] = array('region' => 'AP', 'currency' =>'VUV', 'weight' => 'KG_CM');
	$value['WS'] = array('region' => 'AP', 'currency' =>'WST', 'weight' => 'KG_CM');
	$value['XB'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
	$value['XC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
	$value['XE'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
	$value['XM'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
	$value['XN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
	$value['XS'] = array('region' => 'AP', 'currency' =>'SIS', 'weight' => 'KG_CM');
	$value['XY'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
	$value['YE'] = array('region' => 'AP', 'currency' =>'YER', 'weight' => 'KG_CM');
	$value['YT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
	$value['ZA'] = array('region' => 'AP', 'currency' =>'ZAR', 'weight' => 'KG_CM');
	$value['ZM'] = array('region' => 'AP', 'currency' =>'ZMW', 'weight' => 'KG_CM');
	$value['ZW'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');

	return $value;
	}
	
}
