<?php
class ModelExtensionModuleSmartMarketingTemplate extends Model {
	public function addTemplate($data) {
		$template_id = '';

		// STEP 1 - CREATE TRANSACTIONAL TEMPLATE
		$template_data = array(
			'name' => html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8'),
		);

		$sg_query_template = $this->sendgrid->client->templates()->post($template_data);

		if ($sg_query_template->isCreated()) {
			$template_info = $sg_query_template->getArrayBody();

			if ($template_info) {
				// STEP 2: CREATE VERSION FOR CURRENT TEMPLATE

				$version_data = array(
					'template_id'   => $template_info['id'],
					'name'          => 'version_' . date('Ymd_His'),
					'subject'       => '<%subject%>',
					'html_content'  => $this->convertHtmlTags(html_entity_decode($data['html_content'], ENT_QUOTES, 'UTF-8'), 'ocx2regular'),
					'plain_content' => html_entity_decode($data['plain_content'], ENT_QUOTES, 'UTF-8'),
					'active'        => 1,
					'editor'        => 'code'
				);

				$template_id = $template_info['id'];

				$sg_query_version = $this->sendgrid->client->templates()->_($template_id)->versions()->post($version_data);
			}

			$this->cache->delete('sendgrid.templates');
		}

		return $template_id;
	}

	public function editTemplate($template_id, $data) {
		// STEP 1 - EDIT TRANSACTIONAL TEMPLATE
		$template_data = array(
			'name' => html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8'),
		);

		$sg_query_template = $this->sendgrid->client->templates()->_($template_id)->patch($template_data);

		if ($sg_query_template->isOK()) {
			$template_info = $sg_query_template->getArrayBody();

			if ($template_info) {
				// STEP 2: EDIT VERSION FOR CURRENT TEMPLATE

				$version_data = array(
					'name'          => 'version_' . date('Ymd_His'),
					'subject'       => '<%subject%>',
					'html_content'  => $this->convertHtmlTags(html_entity_decode($data['html_content'], ENT_QUOTES, 'UTF-8'), 'ocx2regular'),
					'plain_content' => html_entity_decode($data['plain_content'], ENT_QUOTES, 'UTF-8'),
					'active'        => 1
				);

				$template_id = $template_info['id'];
				$version_id = $template_info['versions'][0]['id'];

				$sg_query_version = $this->sendgrid->client->templates()->_($template_id)->versions()->_($version_id)->patch($version_data);
			}

			$this->cache->delete('sendgrid.templates');
			$this->cache->delete('sendgrid.template' . $template_id);
		}
	}

	public function deleteTemplate($template_id) {
		$template_info = $this->getTemplate($template_id);

		if ($template_info) {
			$version_id = $template_info['version_id'];
		} else {
			$version_id = false;
		}

		if ($template_id && $version_id) {
			// first delete version
			$sg_query_version = $this->sendgrid->client->templates()->_($template_id)->versions()->_($version_id)->delete();

			// delete template
			if ($sg_query_version->isDeleted()) {
				$sg_query_template = $this->sendgrid->client->templates()->_($template_id)->delete();
			}
		}

		$this->cache->delete('sendgrid.templates');
		$this->cache->delete('sendgrid.template' . $template_id);
	}

	public function getTemplates($data = array()) {
		$templates = $this->cache->get('sendgrid.templates');

		// case not available in cache
		if (!$templates) {
			$sg_query = $this->sendgrid->client->templates()->get();

			if ($sg_query->isOk()) {
				$templates = $sg_query->getArrayBody('templates');

				$this->cache->set('sendgrid.templates', $templates);
			}
		}

		return $templates;
	}

	public function getTemplate($template_id) {
		$template_info = $this->cache->get('sendgrid.template.' . $template_id);

		// case not available in cache
		if (!$template_info) {
			$sg_query = $this->sendgrid->client->templates()->_($template_id)->get();

			if ($sg_query->isOk()) {
				$sg_template_info = $sg_query->getArrayBody();

				if ($sg_template_info) {
					foreach ($sg_template_info['versions'] as $sg_template_version) {
						if ($sg_template_version['active']) {
							$template_info = array(
								'template_id'    => $sg_template_info['id'],
								'name'           => $sg_template_info['name'],
								'version_id'     => $sg_template_version['id'],
								'subject'        => $sg_template_version['subject'],
								'html_content'   => $this->convertHtmlTags(html_entity_decode($sg_template_version['html_content'], ENT_QUOTES, 'UTF-8')),
								'plain_content'  => html_entity_decode($sg_template_version['plain_content'], ENT_QUOTES, 'UTF-8'),
								'date_modified'  => $sg_template_version['updated_at'],
								'blocks'         => false,
							);
						}
					}

					// no reason to add in cache if template has no version yet
					if ($template_info) {
						$this->cache->set('sendgrid.template.' . $template_id, $template_info);
					}
				}
			}
		}

		return $template_info;
	}

	public function getOCXTemplates() {
		$templates = $this->cache->get('ocx.templates');

		// case not available in cache
		if (!$templates) {
			$this->load->model('extension/module/smart_marketing/api');

			$ocx_response = $this->model_extension_module_smart_marketing_api->getOCXTemplates();

			if (isset($ocx_response['error'])) {
				$this->clearTemplatesCache();
			}

			if (isset($ocx_response['response']['templates'])) {
				$templates = $ocx_response['response']['templates'];

				$this->cache->set('ocx.templates', $templates);
			}
		}

		return $templates;
	}

	public function getOCXTemplate($template_id) {
		$template_info = $this->cache->get('ocx.template.' . $template_id);

		if (!$template_info) {
			$this->load->model('extension/module/smart_marketing/api');

			$ocx_response = $this->model_extension_module_smart_marketing_api->getOCXTemplate($template_id);

			if (isset($ocx_response['error'])) {
				$this->cache->delete('ocx.template.' . $template_id);
			}

			if (isset($ocx_response['response']['template'])) {
				$template_info = $ocx_response['response']['template'];

				$this->cache->set('ocx.template.' . $template_id, $template_info);
			}
		}

		return $template_info;
	}

	public function getInUseTemplates() {
		$query = $this->db->query("SELECT DISTINCT c.template_id FROM " . DB_PREFIX . "sm_campaign c LEFT JOIN " . DB_PREFIX . "sm_campaign_task ct ON (c.campaign_id = ct.campaign_id) WHERE ct.sent = '0'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	public function clearTemplatesCache() {
		$this->cache->delete('sendgrid.templates');
		$this->cache->delete('sendgrid.template');
		$this->cache->delete('ocx.templates');
		$this->cache->delete('ocx.template');
	}

	public function convertHtmlTags($html, $direction = 'regular2ocx') {
		$converted_html = '';

		// For newsletters is recommended (mailchimp/campaignmonitor blog) to use Transitional DOCTYPE
		$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

		// List of tags to replace
		$tags = array('html', 'head', 'body');

		$search_prefix = ($direction == 'regular2ocx') ? '' : 'ocx-template-';
		$regex_search = '/(<)(\/?)(' . $search_prefix . (($direction == 'regular2ocx') ? implode('|' . $search_prefix, $tags) : '') . ')/i';

		$replace_prefix = ($direction == 'regular2ocx') ? 'ocx-template-' : '';
		$regex_replace = '${1}${2}' . $replace_prefix . (($direction == 'regular2ocx') ? '${3}' : '');

		$regex_doctype = '/<!DOCTYPE.+?>/i';

		$converted_html = preg_replace($regex_search, $regex_replace, $html);

		if ($direction == 'regular2ocx') {
			$converted_html = preg_replace($regex_doctype, '', $converted_html);
		} else {
			if (strpos($converted_html, $doctype) === false) {
				$converted_html = $doctype . $converted_html;
			}
		}

		return $converted_html;
	}

	public function convertSpecialKeywords($html) {
		$store_base_url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$store_logo = $store_base_url . 'image/' . $this->config->get('config_logo');
		} else {
			$store_logo = 'https://via.placeholder.com/250x50/f7f7f7/555555?text=YOUR+LOGO+HERE';
		}

		$find = array(
			'{store.name}',
			'{store.url}',
			'{store.logo}',
			'{store.telephone}',
			'{store.email}',
			'{currency.symbol_left}',
			'{currency.symbol_right}',
			'{shipping.free}',
			'{shipping.flat}',
			'{first_order.discount}'
		);

		$money_placeholder = $this->currency->getSymbolLeft($this->config->get('config_currency')) . 'XX' . $this->currency->getSymbolRight($this->config->get('config_currency'));

		$replace = array(
			'store.name'            => $this->config->get('config_name'),
			'store.url'             => $store_base_url,
			'store.logo'            => $store_logo,
			'store.telephone'       => $this->config->get('config_telephone'),
			'store.email'           => $this->config->get('config_email'),
			'currency.symbol_left'  => $this->currency->getSymbolLeft($this->config->get('config_currency')),
			'currency.symbol_right' => $this->currency->getSymbolRight($this->config->get('config_currency')),
			'shipping.free'         => $this->config->get('free_status') ? $this->currency->format($this->config->get('free_total'), $this->config->get('config_currency')) : $money_placeholder,
			'shipping.flat'     		=> $this->config->get('flat_status') ? $this->currency->format($this->config->get('flat_cost'), $this->config->get('config_currency')) : $money_placeholder,
			'first_order.discount'  => $this->config->get('first_order_discount_status') ? (($this->config->get('first_order_discount_type') == 'P') ? $this->config->get('first_order_discount_amount') . '%' : $this->currency->format($this->config->get('first_order_discount_amount'), $this->config->get('config_currency'))) : 'X%'
		);

		$social_channels = $this->config->get('module_smart_marketing_social_channel');

		if ($social_channels) {
			foreach ($social_channels as $social_channel) {
				$find[] = '{social.' . $social_channel['code'] . '}';
				$replace['social.' . $social_channel['code']] = $social_channel['link'];
			}
		}

		$converted_html = str_replace($find, $replace, $html);

		return $converted_html;
	}

	public function processTemplateBlocks($blocks) {
		$this->load->model('tool/image');

		$processed_blocks = array();

		if ($blocks) {
			foreach ($blocks as $block) {
				$block['content'] = $this->convertSpecialKeywords($block['content']);
				$block['attributes']['data-lazy'] = $this->model_tool_image->resize('sm-block-loading.gif', 620, 220);

				$processed_blocks[] = $block;
			}
		}

		return $processed_blocks;
	}
}
