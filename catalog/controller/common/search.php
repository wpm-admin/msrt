<?php
class ControllerCommonSearch extends Controller {
	public function index() {
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');
		$data['home_url'] = $_SERVER['REQUEST_URI'];

		$lang = '';
		$this->load->model('localisation/language');
		$language = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
		if($language['url'] != ''){
			$lang = $language['url'].'/';
		}
		$data['search_href'] = HTTPS_SERVER.$lang.'search';
		$data['autocomplete_href'] = HTTPS_SERVER.$lang.'product_autocomplete';
		
		if (isset($this->request->get['search'])) {
			$data['search'] = $this->request->get['search'];
		} else {
			$data['search'] = '';
		}

		$data['is_mobile'] = $this->isMobile();
		
		$this->load->language('product/search');
		$text_view_all_results = $this->config->get('live_search_view_all_results');

		$liveSearch = [
			'text_view_all_results'               => htmlspecialchars($text_view_all_results[$this->config->get('config_language_id')]['name']),
			'text_empty'                          => $this->language->get('text_empty'),
			'module_live_search_show_image'       => $this->config->get('live_search_show_image'),
		'search_href'       => $data['search_href'],
		'autocomplete_href'       => $data['autocomplete_href'],
			'module_live_search_show_price'       => $this->config->get('live_search_show_price'),
			'module_live_search_show_description' => $this->config->get('live_search_show_description'),
			'module_live_search_min_length'       => $this->config->get('live_search_min_length'),
			'module_live_search_show_add_button'  => $this->config->get('live_search_show_add_button'),
		];
		// $this->document->addStyle('catalog/view/javascript/live_search/live_search.css');
		// $this->document->addScript('catalog/view/javascript/live_search/live_search.js');

		$liveSearchJS = '<script type="text/javascript"><!--'."\n";
		$liveSearchJS .= 'var search_href = "'.$data['search_href'].'";'."\n";
		$liveSearchJS .= 'var autocomplete_href = "'.$data['autocomplete_href'].'";'."\n";
		$liveSearchJS .= '//--></script>'."\n";
		$liveSearchJS .= '<link href="catalog/view/javascript/live_search/live_search.css" rel="stylesheet" type="text/css">'."\n";
		$liveSearchJS .= '<!--script src="catalog/view/javascript/live_search/live_search.js" type="text/javascript"></script-->'."\n";
		$liveSearchJS .= '<script type="text/javascript"><!--'."\n";
		$liveSearchJS .= '//$(document).ready(function() {'."\n";
		$liveSearchJS .= 'var options = '.json_encode($liveSearch).';'."\n";
		$liveSearchJS .= 'LiveSearchJs.init(options); '."\n";
		$liveSearchJS .= '//});'."\n";
		$liveSearchJS .= '//--></script>'."\n";
		$liveSearchJS .= '</head>'."\n";
		$data['liveSearchJS'] = $liveSearchJS;
		
		return $this->load->view('common/search', $data);
	}
	
	public function isMobile() { 
		
		return IS_MOBILE;
	}
}