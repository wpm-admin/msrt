<?php
class ControllerExtensionShippinghitfedex extends Controller {
	private $error = array();
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hittech_fedex_details_new` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` text NOT NULL,
		  `tracking_num` text NOT NULL,
		  `shipping_label` text COLLATE utf8_bin NOT NULL,
		  `invoice` text COLLATE utf8_bin NOT NULL,
		  `return_label` text COLLATE utf8_bin  NULL,
		  `return_invoice` text COLLATE utf8_bin  NULL,
		  `one` text COLLATE utf8_bin  NULL,
		  `two` text COLLATE utf8_bin  NULL,
		  `three` text COLLATE utf8_bin  NULL,
		  PRIMARY KEY (`id`)
		)");
	}
	public function index() {
		$this->install();
		$this->load->language('extension/shipping/hitfedex');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_hitfedex', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}



		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['account'])) {
			$data['error_account'] = $this->error['account'];
		} else {
			$data['error_account'] = '';
		}

		if (isset($this->error['meter'])) {
			$data['error_meter'] = $this->error['meter'];
		} else {
			$data['error_meter'] = '';
		}
		
		if (isset($this->error['postcode'])) {
			$data['error_postcode'] = $this->error['postcode'];
		} else {
			$data['error_postcode'] = '';
		}

		if (isset($this->error['dimension'])) {
			$data['error_dimension'] = $this->error['dimension'];
		} else {
			$data['error_dimension'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/hitfedex', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/hitfedex', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		
		if (isset($this->request->post['shipping_hitfedex_test'])) {
			$data['shipping_hitfedex_test'] = $this->request->post['shipping_hitfedex_test'];
		} else {
			$data['shipping_hitfedex_test'] = $this->config->get('shipping_hitfedex_test');
		}
		
		if (isset($this->request->post['shipping_hitfedex_key'])) {
			$data['shipping_hitfedex_key'] = $this->request->post['shipping_hitfedex_key'];
		} else {
			$data['shipping_hitfedex_key'] = $this->config->get('shipping_hitfedex_key');
		}

		if (isset($this->request->post['shipping_hitfedex_password'])) {
			$data['shipping_hitfedex_password'] = $this->request->post['shipping_hitfedex_password'];
		} else {
			$data['shipping_hitfedex_password'] = $this->config->get('shipping_hitfedex_password');
		}

		if (isset($this->request->post['shipping_hitfedex_account'])) {
			$data['shipping_hitfedex_account'] = $this->request->post['shipping_hitfedex_account'];
		} else {
			$data['shipping_hitfedex_account'] = $this->config->get('shipping_hitfedex_account');
		}

		if (isset($this->request->post['shipping_hitfedex_meter'])) {
			$data['shipping_hitfedex_meter'] = $this->request->post['shipping_hitfedex_meter'];
		} else {
			$data['shipping_hitfedex_meter'] = $this->config->get('shipping_hitfedex_meter');
		}
		
		if (isset($this->request->post['shipping_hitfedex_status'])) {
			$data['shipping_hitfedex_status'] = $this->request->post['shipping_hitfedex_status'];
		} else {
			$data['shipping_hitfedex_status'] = $this->config->get('shipping_hitfedex_status');
		}
		
		if (isset($this->request->post['shipping_hitfedex_sort_order'])) {
			$data['shipping_hitfedex_sort_order'] = $this->request->post['shipping_hitfedex_sort_order'];
		} else {
			$data['shipping_hitfedex_sort_order'] = $this->config->get('shipping_hitfedex_sort_order');
		}

		if (isset($this->request->post['shipping_hitfedex_shipper_name'])) {
			$data['shipping_hitfedex_shipper_name'] = $this->request->post['shipping_hitfedex_shipper_name'];
		} else {
			$data['shipping_hitfedex_shipper_name'] = $this->config->get('shipping_hitfedex_shipper_name');
		}
		
		if (isset($this->request->post['shipping_hitfedex_company_name'])) {
			$data['shipping_hitfedex_company_name'] = $this->request->post['shipping_hitfedex_company_name'];
		} else {
			$data['shipping_hitfedex_company_name'] = $this->config->get('shipping_hitfedex_company_name');
		}
		
		if (isset($this->request->post['shipping_hitfedex_phone_num'])) {
			$data['shipping_hitfedex_phone_num'] = $this->request->post['shipping_hitfedex_phone_num'];
		} else {
			$data['shipping_hitfedex_phone_num'] = $this->config->get('shipping_hitfedex_phone_num');
		}
		
		if (isset($this->request->post['shipping_hitfedex_email_addr'])) {
			$data['shipping_hitfedex_email_addr'] = $this->request->post['shipping_hitfedex_email_addr'];
		} else {
			$data['shipping_hitfedex_email_addr'] = $this->config->get('shipping_hitfedex_email_addr');
		}
		
		if (isset($this->request->post['shipping_hitfedex_address1'])) {
			$data['shipping_hitfedex_address1'] = $this->request->post['shipping_hitfedex_address1'];
		} else {
			$data['shipping_hitfedex_address1'] = $this->config->get('shipping_hitfedex_address1');
		}
		
		if (isset($this->request->post['shipping_hitfedex_address2'])) {
			$data['shipping_hitfedex_address2'] = $this->request->post['shipping_hitfedex_address2'];
		} else {
			$data['shipping_hitfedex_address2'] = $this->config->get('shipping_hitfedex_address2');
		}
		
		
		if (isset($this->request->post['shipping_hitfedex_city'])) {
			$data['shipping_hitfedex_city'] = $this->request->post['shipping_hitfedex_city'];
		} else {
			$data['shipping_hitfedex_city'] = $this->config->get('shipping_hitfedex_city');
		}
		
		
		if (isset($this->request->post['shipping_hitfedex_state'])) {
			$data['shipping_hitfedex_state'] = $this->request->post['shipping_hitfedex_state'];
		} else {
			$data['shipping_hitfedex_state'] = $this->config->get('shipping_hitfedex_state');
		}
		
		
		if (isset($this->request->post['shipping_hitfedex_country_code'])) {
			$data['shipping_hitfedex_country_code'] = $this->request->post['shipping_hitfedex_country_code'];
		} else {
			$data['shipping_hitfedex_country_code'] = $this->config->get('shipping_hitfedex_country_code');
		}
		$data['countrylist'] = array(
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BQ' => 'Bonaire, Saint Eustatius and Saba',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'VG' => 'British Virgin Islands',
			'BN' => 'Brunei',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'Curacao',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'CD' => 'Democratic Republic of the Congo',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'TL' => 'East Timor',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island and McDonald Islands',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'CI' => 'Ivory Coast',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'XK' => 'Kosovo',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'KP' => 'North Korea',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'CG' => 'Republic of the Congo',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'KR' => 'South Korea',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syria',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'VI' => 'U.S. Virgin Islands',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Minor Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VA' => 'Vatican',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);

		
		if (isset($this->request->post['shipping_hitfedex_postcode'])) {
			$data['shipping_hitfedex_postcode'] = $this->request->post['shipping_hitfedex_postcode'];
		} else {
			$data['shipping_hitfedex_postcode'] = $this->config->get('shipping_hitfedex_postcode');
		}
		
		if (isset($this->request->post['shipping_hitfedex_realtime_rates'])) {
			$data['shipping_hitfedex_realtime_rates'] = $this->request->post['shipping_hitfedex_realtime_rates'];
		} else {
			$data['shipping_hitfedex_realtime_rates'] = $this->config->get('shipping_hitfedex_realtime_rates');
		}
		if (isset($this->request->post['shipping_hitfedex_insurance'])) {
			$data['shipping_hitfedex_insurance'] = $this->request->post['shipping_hitfedex_insurance'];
		} else {
			$data['shipping_hitfedex_insurance'] = $this->config->get('shipping_hitfedex_insurance');
		}
		
		if (isset($this->request->post['shipping_hitfedex_display_time'])) {
			$data['shipping_hitfedex_display_time'] = $this->request->post['shipping_hitfedex_display_time'];
		} else {
			$data['shipping_hitfedex_display_time'] = $this->config->get('shipping_hitfedex_display_time');
		}
		if (isset($this->request->post['shipping_hitfedex_front_end_logs'])) {
			$data['shipping_hitfedex_front_end_logs'] = $this->request->post['shipping_hitfedex_front_end_logs'];
		} else {
			$data['shipping_hitfedex_front_end_logs'] = $this->config->get('shipping_hitfedex_front_end_logs');
		}
			
		if (isset($this->request->post['shipping_hitfedex_rate_type'])) {
			$data['shipping_hitfedex_rate_type'] = $this->request->post['shipping_hitfedex_rate_type'];
		} else {
			$data['shipping_hitfedex_rate_type'] = $this->config->get('shipping_hitfedex_rate_type');
		}
		
		
		if (isset($this->request->post['shipping_hitfedex_service'])) {
			$data['shipping_hitfedex_service'] = $this->request->post['shipping_hitfedex_service'];
		} elseif ($this->config->has('shipping_hitfedex_service')) {
			$data['shipping_hitfedex_service'] = $this->config->get('shipping_hitfedex_service');
		} else {
			$data['shipping_hitfedex_service'] = array();
		}

		$data['services'] = array();

		$data['services'][] = array(
			'text'  => $this->language->get('FIRST_OVERNIGHT'),
			'value' => 'FIRST_OVERNIGHT'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('PRIORITY_OVERNIGHT'),
			'value' => 'PRIORITY_OVERNIGHT'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('STANDARD_OVERNIGHT'),
			'value' => 'STANDARD_OVERNIGHT'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_2_DAY_AM'),
			'value' => 'FEDEX_2_DAY_AM'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_2_DAY'),
			'value' => 'FEDEX_2_DAY'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('SAME_DAY'),
			'value' => 'SAME_DAY'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('SAME_DAY_CITY'),
			'value' => 'SAME_DAY_CITY'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('SAME_DAY_METRO_AFTERNOON'),
			'value' => 'SAME_DAY_METRO_AFTERNOON'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('SAME_DAY_METRO_MORNING'),
			'value' => 'SAME_DAY_METRO_MORNING'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('SAME_DAY_METRO_RUSH'),
			'value' => 'SAME_DAY_METRO_RUSH'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_EXPRESS_SAVER'),
			'value' => 'FEDEX_EXPRESS_SAVER'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('GROUND_HOME_DELIVERY'),
			'value' => 'GROUND_HOME_DELIVERY'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_GROUND'),
			'value' => 'FEDEX_GROUND'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_ECONOMY'),
			'value' => 'INTERNATIONAL_ECONOMY'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_ECONOMY_DISTRIBUTION'),
			'value' => 'INTERNATIONAL_ECONOMY_DISTRIBUTION'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_FIRST'),
			'value' => 'INTERNATIONAL_FIRST'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_GROUND'),
			'value' => 'INTERNATIONAL_GROUND'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_PRIORITY'),
			'value' => 'INTERNATIONAL_PRIORITY'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_PRIORITY_DISTRIBUTION'),
			'value' => 'INTERNATIONAL_PRIORITY_DISTRIBUTION'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('EUROPE_FIRST_INTERNATIONAL_PRIORITY'),
			'value' => 'EUROPE_FIRST_INTERNATIONAL_PRIORITY'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_PRIORITY_EXPRESS'),
			'value' => 'INTERNATIONAL_PRIORITY_EXPRESS'
		);
		
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_INTERNATIONAL_PRIORITY_PLUS'),
			'value' => 'FEDEX_INTERNATIONAL_PRIORITY_PLUS'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_DISTRIBUTION_FREIGHT'),
			'value' => 'INTERNATIONAL_DISTRIBUTION_FREIGHT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_1_DAY_FREIGHT'),
			'value' => 'FEDEX_1_DAY_FREIGHT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_2_DAY_FREIGHT'),
			'value' => 'FEDEX_2_DAY_FREIGHT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_3_DAY_FREIGHT'),
			'value' => 'FEDEX_3_DAY_FREIGHT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_ECONOMY_FREIGHT'),
			'value' => 'INTERNATIONAL_ECONOMY_FREIGHT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('INTERNATIONAL_PRIORITY_FREIGHT'),
			'value' => 'INTERNATIONAL_PRIORITY_FREIGHT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('SMART_POST'),
			'value' => 'SMART_POST'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_FIRST_FREIGHT'),
			'value' => 'FEDEX_FIRST_FREIGHT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_FREIGHT_ECONOMY'),
			'value' => 'FEDEX_FREIGHT_ECONOMY'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_FREIGHT_PRIORITY'),
			'value' => 'FEDEX_FREIGHT_PRIORITY'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CARGO_AIRPORT_TO_AIRPORT'),
			'value' => 'FEDEX_CARGO_AIRPORT_TO_AIRPORT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CARGO_FREIGHT_FORWARDING'),
			'value' => 'FEDEX_CARGO_FREIGHT_FORWARDING'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CARGO_INTERNATIONAL_EXPRESS_FREIGHT'),
			'value' => 'FEDEX_CARGO_INTERNATIONAL_EXPRESS_FREIGHT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CARGO_INTERNATIONAL_PREMIUM'),
			'value' => 'FEDEX_CARGO_INTERNATIONAL_PREMIUM'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CARGO_MAIL'),
			'value' => 'FEDEX_CARGO_MAIL'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CARGO_REGISTERED_MAIL'),
			'value' => 'FEDEX_CARGO_REGISTERED_MAIL'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CARGO_SURFACE_MAIL'),
			'value' => 'FEDEX_CARGO_SURFACE_MAIL'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_AIR_EXPEDITE_EXCLUSIVE_USE'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_AIR_EXPEDITE_EXCLUSIVE_USE'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_AIR_EXPEDITE_NETWORK'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_AIR_EXPEDITE_NETWORK'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_CHARTER_AIR'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_CHARTER_AIR'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_POINT_TO_POINT'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_POINT_TO_POINT'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_SURFACE_EXPEDITE'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_SURFACE_EXPEDITE'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_SURFACE_EXPEDITE_EXCLUSIVE_USE'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_SURFACE_EXPEDITE_EXCLUSIVE_USE'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_TEMP_ASSURE_AIR'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_TEMP_ASSURE_AIR'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_TEMP_ASSURE_VALIDATED_AIR'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_TEMP_ASSURE_VALIDATED_AIR'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_CUSTOM_CRITICAL_WHITE_GLOVE_SERVICES'),
			'value' => 'FEDEX_CUSTOM_CRITICAL_WHITE_GLOVE_SERVICES'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('TRANSBORDER_DISTRIBUTION_CONSOLIDATION'),
			'value' => 'TRANSBORDER_DISTRIBUTION_CONSOLIDATION'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_DISTANCE_DEFERRED'),
			'value' => 'FEDEX_DISTANCE_DEFERRED'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_NEXT_DAY_EARLY_MORNING'),
			'value' => 'FEDEX_NEXT_DAY_EARLY_MORNING'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_NEXT_DAY_MID_MORNING'),
			'value' => 'FEDEX_NEXT_DAY_MID_MORNING'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_NEXT_DAY_AFTERNOON'),
			'value' => 'FEDEX_NEXT_DAY_AFTERNOON'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_NEXT_DAY_END_OF_DAY'),
			'value' => 'FEDEX_NEXT_DAY_END_OF_DAY'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('FEDEX_NEXT_DAY_FREIGHT'),
			'value' => 'FEDEX_NEXT_DAY_FREIGHT'
		);
		

		
		if (isset($this->request->post['shipping_hitfedex_weight'])) {
			$data['shipping_hitfedex_weight'] = $this->request->post['shipping_hitfedex_weight'];
		} else {
			$data['shipping_hitfedex_weight'] = $this->config->get('shipping_hitfedex_weight');
		}
		
		if (isset($this->request->post['shipping_hitfedex_packing_type'])) {
			$data['shipping_hitfedex_packing_type'] = $this->request->post['shipping_hitfedex_packing_type'];
		} else {
			$data['shipping_hitfedex_packing_type'] = $this->config->get('shipping_hitfedex_packing_type');
		}
		
		if (isset($this->request->post['shipping_hitfedex_per_item'])) {
			$data['shipping_hitfedex_per_item'] = $this->request->post['shipping_hitfedex_per_item'];
		} else {
			$data['shipping_hitfedex_per_item'] = $this->config->get('shipping_hitfedex_per_item');
		}
		
		
			
		if (isset($this->request->post['shipping_hitfedex_wight_b'])) {
			$data['shipping_hitfedex_wight_b'] = $this->request->post['shipping_hitfedex_wight_b'];
		} else {
			$data['shipping_hitfedex_wight_b'] = $this->config->get('shipping_hitfedex_wight_b');
		}
		
				
		if (isset($this->request->post['shipping_hitfedex_weight_c'])) {
			$data['shipping_hitfedex_weight_c'] = $this->request->post['shipping_hitfedex_weight_c'];
		} else {
			$data['shipping_hitfedex_weight_c'] = $this->config->get('shipping_hitfedex_weight_c');
		}
		
				
		if (isset($this->request->post['shipping_hitfedex_plt'])) {
			$data['shipping_hitfedex_plt'] = $this->request->post['shipping_hitfedex_plt'];
		} else {
			$data['shipping_hitfedex_plt'] = $this->config->get('shipping_hitfedex_plt');
		}
		
				
		if (isset($this->request->post['shipping_hitfedex_sat'])) {
			$data['shipping_hitfedex_sat'] = $this->request->post['shipping_hitfedex_sat'];
		} else {
			$data['shipping_hitfedex_sat'] = $this->config->get('shipping_hitfedex_sat');
		}
		
				
		if (isset($this->request->post['shipping_hitfedex_email_trach'])) {
			$data['shipping_hitfedex_email_trach'] = $this->request->post['shipping_hitfedex_email_trach'];
		} else {
			$data['shipping_hitfedex_email_trach'] = $this->config->get('shipping_hitfedex_email_trach');
		}
				
		if (isset($this->request->post['shipping_hitfedex_airway'])) {
			$data['shipping_hitfedex_airway'] = $this->request->post['shipping_hitfedex_airway'];
		} else {
			$data['shipping_hitfedex_airway'] = $this->config->get('shipping_hitfedex_airway');
		}
				
		if (isset($this->request->post['shipping_hitfedex_dropoff_type'])) {
			$data['shipping_hitfedex_dropoff_type'] = $this->request->post['shipping_hitfedex_dropoff_type'];
		} else {
			$data['shipping_hitfedex_dropoff_type'] = $this->config->get('shipping_hitfedex_dropoff_type');
		}
				
		if (isset($this->request->post['shipping_hitfedex_duty_type'])) {
			$data['shipping_hitfedex_duty_type'] = $this->request->post['shipping_hitfedex_duty_type'];
		} else {
			$data['shipping_hitfedex_duty_type'] = $this->config->get('shipping_hitfedex_duty_type');
		}
		if (isset($this->request->post['shipping_hitfedex_output_type'])) {
			$data['shipping_hitfedex_output_type'] = $this->request->post['shipping_hitfedex_output_type'];
		} else {
			$data['shipping_hitfedex_output_type'] = $this->config->get('shipping_hitfedex_output_type');
		}
		
		if (isset($this->request->post['shipping_hitfedex_shipment_content'])) {
			$data['shipping_hitfedex_shipment_content'] = $this->request->post['shipping_hitfedex_shipment_content'];
		} else {
			$data['shipping_hitfedex_shipment_content'] = $this->config->get('shipping_hitfedex_shipment_content');
		}
		if (isset($this->request->post['shipping_hitfedex_logo'])) {
			$data['shipping_hitfedex_logo'] = $this->request->post['shipping_hitfedex_logo'];
		} else {
			$data['shipping_hitfedex_logo'] = $this->config->get('shipping_hitfedex_logo');
		}
		
			if (isset($this->request->post['shipping_hitfedex_picper'])) {
			$data['shipping_hitfedex_picper'] = $this->request->post['shipping_hitfedex_picper'];
		} else {
			$data['shipping_hitfedex_picper'] = $this->config->get('shipping_hitfedex_picper');
		}
			if (isset($this->request->post['shipping_hitfedex_piccon'])) {
			$data['shipping_hitfedex_piccon'] = $this->request->post['shipping_hitfedex_piccon'];
		} else {
			$data['shipping_hitfedex_piccon'] = $this->config->get('shipping_hitfedex_piccon');
		}
			if (isset($this->request->post['shipping_hitfedex_pickup_time'])) {
			$data['shipping_hitfedex_pickup_time'] = $this->request->post['shipping_hitfedex_pickup_time'];
		} else {
			$data['shipping_hitfedex_pickup_time'] = $this->config->get('shipping_hitfedex_pickup_time');
		}
			if (isset($this->request->post['shipping_hitfedex_close_time'])) {
			$data['shipping_hitfedex_close_time'] = $this->request->post['shipping_hitfedex_close_time'];
		} else {
			$data['shipping_hitfedex_close_time'] = $this->config->get('shipping_hitfedex_close_time');
		}


		//thilak
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/shipping/hitfedex', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/hitfedex')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['shipping_hitfedex_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}

		if (!$this->request->post['shipping_hitfedex_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['shipping_hitfedex_account']) {
			$this->error['account'] = $this->language->get('error_account');
		}

		if (!$this->request->post['shipping_hitfedex_meter']) {
			$this->error['meter'] = $this->language->get('error_meter');
		}

		if (!$this->request->post['shipping_hitfedex_postcode']) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}

		return !$this->error;
	}
}
