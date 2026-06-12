<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$config_meta_title = $this->config->get('config_meta_title');
		$config_meta_description = $this->config->get('config_meta_description');
		$config_meta_keyword = $this->config->get('config_meta_keyword');

		$this->document->setTitle($config_meta_title[$this->config->get('config_language_id')]);
		$this->document->setDescription($config_meta_description[$this->config->get('config_language_id')]);
		$this->document->setKeywords($config_meta_keyword[$this->config->get('config_language_id')]);

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$this->load->language('common/header');
		$this->load->language('common/footer');
		$this->load->language('common/language');
		$this->load->language('product/blog_product');

		$server = $this->request->server['HTTPS'] ? $this->config->get('config_ssl') : $this->config->get('config_url');

		$data['base'] = $server;
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$theme_assets = $server . 'catalog/view/theme/technics/assets/';
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['name'] = $this->config->get('config_name');
		$data['config_email'] = $this->config->get('config_email');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->model('catalog/mark');
		$this->load->model('tool/image');

		// === 1. Marks for logo slider ===
		$marks = $this->model_catalog_mark->getMarks(0);
		$data['lm_marks'] = array();
		foreach ($marks as $mark) {
			$data['lm_marks'][$mark['mark_id']] = array(
				'mark_id' => $mark['mark_id'],
				'name'    => $mark['name'],
				'image'   => $mark['image'] ? $this->model_tool_image->resize($mark['image'], 100, 100) : '',
				'href'    => $this->url->link('product/mark', 'mark_id=' . $mark['mark_id'])
			);
		}

		// === 2. Marks for mobile menu ===
		$data['marks'] = $marks;
		foreach ($data['marks'] as $index => $row) {
			$data['marks'][$index]['image'] = $row['image'];
			$data['marks'][$index]['href'] = $this->url->link('product/mark', 'mark_id=' . $row['mark_id'], true);
		}

		// === 3. First mark → models (initial load) ===
		$data['lm_first_models'] = array();
		$first_mark = reset($marks);
		if ($first_mark) {
			$models = $this->model_catalog_mark->getMarks($first_mark['mark_id']);
			foreach ($models as $m) {
				$data['lm_first_models'][] = array(
					'model_id' => $m['mark_id'],
					'name'     => $m['name'],
					'image'    => $m['image'] ? $this->model_tool_image->resize($m['image'], 50, 50) : '',
					'href'     => $this->url->link('product/mark', 'mark_id=' . $m['mark_id'])
				);
			}
		}

		// === 4. First model → categories (initial load) ===
		$data['lm_first_categories'] = array();
		if (!empty($data['lm_first_models'])) {
			$first_mark_id = $first_mark['mark_id'];
			$first_model = $data['lm_first_models'][0];
			$model_id = $first_model['model_id'];
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');
			$categories = $this->model_catalog_category->getCategories(0);
			foreach ($categories as $category) {
				$filter_data = array(
					'filter_category_id'  => $category['category_id'],
					'filter_sub_category' => true,
					'filter_model'        => true,
					'model_id'            => $model_id,
					'start'               => 0,
					'limit'               => 1
				);
				if ($this->model_catalog_product->getTotalProducts($filter_data) > 0) {
					$data['lm_first_categories'][] = array(
						'category_id' => $category['category_id'],
						'name'        => $category['name'],
						'href'        => $this->_buildCategoryUrl($category['category_id'], $first_mark_id, $model_id)
					);
				}
			}
		}

		// === 5. Category bottom — model-filtered categories with images ===
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$data['lm_category_bottom'] = array();
		$model_id = !empty($data['lm_first_models']) ? $data['lm_first_models'][0]['model_id'] : 0;
		$mark_id = $first_mark ? $first_mark['mark_id'] : 0;
		$categories = $this->model_catalog_category->getCategories(0);
		$bottom_count = 0;
		foreach ($categories as $category) {
			if ($bottom_count >= 6) break;
			if ($model_id) {
				$filter_data = array(
					'filter_category_id'  => $category['category_id'],
					'filter_sub_category' => true,
					'filter_model'        => true,
					'model_id'            => $model_id,
					'start'               => 0,
					'limit'               => 1
				);
				if ($this->model_catalog_product->getTotalProducts($filter_data) == 0) continue;
			}
			$data['lm_category_bottom'][] = array(
				'name'  => $category['name'],
				'thumb' => $category['image'] ? $this->model_tool_image->resize($category['image'], 265, 184) : '',
				'href'  => $this->_buildCategoryUrl($category['category_id'], $mark_id, $model_id)
			);
			$bottom_count++;
		}

		$data['all_categories_href'] = $this->url->link('product/category', 'path=0');

		// === 6. Blog / Club articles ===
		$this->load->model('catalog/blog_product');
		$data['blog_products'] = array();

		$filter_data = array(
			'filter_blog_category_id'     => 0,
			'filter_sub_blog_category'    => true,
			'filter_model'                => true,
			'sort'                        => 'p.date_available',
			'order'                       => 'DESC',
			'start'                       => 0,
			'limit'                       => 6
		);

		$data['blog_href'] = $this->url->link('product/blog_category', 'blogpath=3');

		$results = $this->model_catalog_blog_product->getProducts($filter_data);

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], 372, 264);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 372, 264);
			}

			$data['blog_products'][] = array(
				'blog_product_id'  => $result['blog_product_id'],
				'thumb'            => $image,
				'name'             => $result['name'],
				'description'      => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, 120) . '..',
				'date_available'   => $result['date_available'],
				'href'             => $this->url->link('product/blog_product', 'blog_product_id=' . $result['blog_product_id'])
			);
		}

		// === 7. Auction products ===
		$this->load->model('catalog/product');
		$data['auction_products'] = array();
		$auction_results = $this->model_catalog_product->getAuctionProducts(6);
		foreach ($auction_results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], 373, 226);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 373, 226);
			}
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = false;
			}
			$data['auction_products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'      => $image,
				'name'       => $result['name'],
				'price'      => $price,
				'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id'])
			);
		}
		$data['auction_href'] = $this->url->link('product/category', 'path=234');

		// === 8. About Us (info page ID 4) ===
		$this->load->model('catalog/information');
		$about_info = $this->model_catalog_information->getInformation(4);
		$data['about_title'] = $about_info ? $about_info['title'] : 'About Us';
		$data['about_description'] = $about_info ? html_entity_decode($about_info['description'], ENT_QUOTES, 'UTF-8') : '';
		$data['about_image'] = $theme_assets . 'img/Subtract.jpg';

		// === 9. Hero slideshow ===
		$this->load->model('design/banner');
		$data['lm_hero_slides'] = array();
		$hero_banners = $this->model_design_banner->getBanner(0);
		if ($hero_banners) {
			foreach ($hero_banners as $banner) {
				if (is_file(DIR_IMAGE . $banner['image'])) {
					$data['lm_hero_slides'][] = array(
						'title' => $banner['title'],
						'image' => $this->model_tool_image->resize($banner['image'], 1920, 900),
						'link'  => $banner['link']
					);
				}
			}
		}
		if (empty($data['lm_hero_slides'])) {
			$data['lm_hero_slides'] = array(
				array('image' => $server . 'catalog/view/theme/technics/assets/img/hero/hero-bg.jpg', 'title' => ''),
				array('image' => $server . 'catalog/view/theme/technics/assets/img/hero/hero-bg1.jpg', 'title' => '')
			);
		}

		// === 10. Banners (promo + maserati) ===
		$data['lm_banner_promo'] = array(
			'image' => $theme_assets . 'img/banner-legends.jpg',
			'title' => 'Legends Belong on the Road! Enjoy 15% Off this spring',
			'code'  => 'SPRING15'
		);
		$data['lm_banner_maserati'] = array(
			'image' => $theme_assets . 'img/baner-maserati.jpg',
			'title' => 'Maserati 450s',
			'subtitle' => 'Rebirth of a Champion'
		);

		// === 11. SEO text ===
		$data['lm_seo'] = '';
		$this->load->model('setting/module');
		$seo_modules = array();
		$seo_module_id = 0;
		if ($seo_module_id) {
			$seo_module_info = $this->model_setting_module->getModule($seo_module_id);
			if ($seo_module_info && isset($seo_module_info['code'])) {
				$data['lm_seo'] = $this->load->controller('extension/module/' . $seo_module_info['code'], $seo_module_info);
			}
		}

		// === 12. News section ===
		$data['lm_news'] = array(
			array(
				'title' => 'Viale Ciro Menotti (VCM)',
				'thumb' => $theme_assets . 'img/slider-bottom/1.jpg'
			),
			array(
				'title' => 'MIE FACILITIES',
				'thumb' => $theme_assets . 'img/slider-bottom/2.jpg'
			),
			array(
				'title' => 'Ebay store',
				'thumb' => $theme_assets . 'img/slider-bottom/3.jpg'
			),
			array(
				'title' => 'MASERATI CLASSIFIEDS',
				'thumb' => $theme_assets . 'img/slider-bottom/4.jpg'
			)
		);

		// === 13. Languages (for footer custom switcher) ===
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$lm_languages = array();
		foreach ($languages as $lang) {
			$code_prefix = $lang['code'] == 'en-gb' ? '' : substr($lang['code'], 0, 2) . '/';
			$lm_languages[] = array(
				'code'      => $lang['code'],
				'name'      => $lang['name'],
				'long_name' => $lang['name'],
				'href'      => $server . $code_prefix
			);
		}
		$data['lm_languages'] = $lm_languages;
		$data['lm_current_lang_code'] = $this->session->data['language'];

		// === 14. Homepage text strings ===
		$data['text_home_title'] = 'Supplying quality parts for iconic cars since 1976';
		$data['text_select_model'] = $this->language->get('text_select_modeli');
		$data['text_select_category'] = 'Select Category';
		$data['text_promo_code'] = 'Use code:';
		$data['text_copy'] = 'Copy';
		$data['text_club_title'] = $this->language->get('text_club_news');
		$data['text_about_signature'] = 'Jacques Pozzo di Borgo';
		$data['text_about_status'] = 'President';
		$data['text_auctions_title'] = 'Auctions';
		$data['text_all_auctions'] = 'To Auctions Page';
		$data['text_all_categories'] = 'View All Categories';
		$data['text_footer_info'] = 'Maseratinet, MIE Corporation is a privately owned and not connected with the Factory or any of its distributors or dealers.';
		$data['text_blog'] = 'Blog';
		$data['text_about_us'] = 'About Us';
		$data['text_shipping'] = 'Shipping';
		$data['text_returns'] = $this->language->get('text_return');
		$data['text_faq'] = 'FAQ';
		$data['text_terms'] = 'Terms & Conditions';
		$data['text_privacy'] = 'Privacy & Security Policy';
		$data['text_contact_us'] = $this->language->get('text_contact');
		$data['text_contacts'] = $this->language->get('text_contact');
		$data['text_copyright'] = '&copy; 1976-' . date('Y') . ' MIE Corporation. All rights reserved.';
		$data['text_powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y'));
		$data['text_language'] = $this->language->get('text_language');
		$data['text_car_parts'] = $this->language->get('text_car_parts');
		$data['text_club_store'] = $this->language->get('text_club_store');
		$data['text_auction'] = $this->language->get('text_auction');
		$data['text_club_store_bttn'] = 'To Club News';
		$data['text_home'] = $this->language->get('text_home');
		$data['text_search'] = $this->language->get('text_search');
		$data['about_href'] = $this->url->link('information/information', 'information_id=4');
		$data['contact_href'] = $this->url->link('information/contact');
		$data['customer_service_href'] = $this->url->link('information/information', 'information_id=5');

		// === Live search setup ===
		$this->load->language('common/search');
		$this->load->language('product/search');
		$this->load->model('localisation/language');
		$lang_code = '';
		$language = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
		if ($language['url'] != '') {
			$lang_code = $language['url'] . '/';
		}
		$data['search_href'] = HTTPS_SERVER . $lang_code . 'search';
		$data['autocomplete_href'] = HTTPS_SERVER . $lang_code . 'product_autocomplete';

		$text_view_all_results = $this->config->get('live_search_view_all_results');
		$data['live_search_options'] = array(
			'text_view_all_results'               => htmlspecialchars($text_view_all_results[$this->config->get('config_language_id')]['name']),
			'text_empty'                          => $this->language->get('text_empty'),
			'module_live_search_show_image'       => $this->config->get('live_search_show_image'),
			'module_live_search_show_price'       => $this->config->get('live_search_show_price'),
			'module_live_search_show_description' => $this->config->get('live_search_show_description'),
			'module_live_search_min_length'       => $this->config->get('live_search_min_length'),
			'module_live_search_show_add_button'  => $this->config->get('live_search_show_add_button'),
			'search_href'                         => $data['search_href'],
			'autocomplete_href'                   => $data['autocomplete_href'],
		);
		$this->document->addStyle('catalog/view/javascript/live_search/live_search.css');

		// === 15. Content modules (for styles/scripts side effects) ===
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['lm_styles'] = $this->document->getStyles();
		$data['lm_scripts'] = $this->document->getScripts('footer');

		$data['lm_theme_assets'] = $theme_assets;

		$data['home'] = $this->url->link('common/home');

		$this->response->setOutput($this->load->view('common/home', $data));
	}

	public function getModels() {
		$json = array();
		if (isset($this->request->get['mark_id'])) {
			$this->load->model('catalog/mark');
			$this->load->model('tool/image');
			$models = $this->model_catalog_mark->getMarks((int)$this->request->get['mark_id']);
			foreach ($models as $model) {
				$json[] = array(
					'model_id' => $model['mark_id'],
					'name'     => $model['name'],
					'image'    => $model['image'] ? $this->model_tool_image->resize($model['image'], 50, 50) : ''
				);
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getCategories() {
		$json = array();
		if (isset($this->request->get['mark_id']) && isset($this->request->get['model_id'])) {
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');
			$mark_id  = (int)$this->request->get['mark_id'];
			$model_id = (int)$this->request->get['model_id'];
			$categories = $this->model_catalog_category->getCategories(0);
			foreach ($categories as $category) {
				$filter_data = array(
					'filter_category_id'  => $category['category_id'],
					'filter_sub_category' => true,
					'filter_model'        => true,
					'model_id'            => $model_id,
					'start'               => 0,
					'limit'               => 1
				);
				if ($this->model_catalog_product->getTotalProducts($filter_data) > 0) {
					$json[] = array(
						'category_id' => $category['category_id'],
						'name'        => $category['name'],
						'href'        => $this->_buildCategoryUrl($category['category_id'], $mark_id, $model_id)
					);
				}
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getCategoryBottom() {
		$json = array();
		if (isset($this->request->get['mark_id']) && isset($this->request->get['model_id'])) {
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			$mark_id  = (int)$this->request->get['mark_id'];
			$model_id = (int)$this->request->get['model_id'];
			$categories = $this->model_catalog_category->getCategories(0);
			$count = 0;
			foreach ($categories as $category) {
				if ($count >= 6) break;
				$filter_data = array(
					'filter_category_id'  => $category['category_id'],
					'filter_sub_category' => true,
					'filter_model'        => true,
					'model_id'            => $model_id,
					'start'               => 0,
					'limit'               => 1
				);
				if ($this->model_catalog_product->getTotalProducts($filter_data) > 0) {
					$json[] = array(
						'name'  => $category['name'],
						'thumb' => $category['image'] ? $this->model_tool_image->resize($category['image'], 265, 184) : '',
						'href'  => $this->_buildCategoryUrl($category['category_id'], $mark_id, $model_id)
					);
					$count++;
				}
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function _buildCategoryUrl($category_id, $mark_id, $model_id) {
		$language_id = (int)$this->config->get('config_language_id');
		$store_id = (int)$this->config->get('config_store_id');

		$this->load->model('localisation/language');
		$lang_info = $this->model_localisation_language->getLanguage($language_id);
		$lang_prefix = ($lang_info['url'] != '') ? '/' . $lang_info['url'] : '';

		$path = '';

		$query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query='mark_id=" . (int)$mark_id . "' AND store_id=" . $store_id . " AND language_id=" . $language_id);
		if ($query->num_rows) {
			$path .= '/' . $query->row['keyword'];
		}

		$query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query='mark_id=" . (int)$model_id . "' AND store_id=" . $store_id . " AND language_id=" . $language_id);
		if ($query->num_rows) {
			$path .= '/' . $query->row['keyword'];
		}

		$query = $this->db->query("SELECT ua.keyword FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('category_id=', cp.path_id) WHERE cp.category_id=" . (int)$category_id . " AND ua.language_id=" . $language_id . " ORDER BY cp.level");
		foreach ($query->rows as $row) {
			$path .= '/' . $row['keyword'];
		}

		return rtrim(HTTPS_SERVER, '/') . $lang_prefix . $path;
	}
}
