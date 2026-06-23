<?php
class ModelExtensionModuleSmartMarketingBlock extends Model {
	public function getOCXBlocks() {
		$blocks = $this->cache->get('ocx.blocks');

		// case not available in cache
		if (!$blocks) {
			$this->load->model('extension/module/smart_marketing/api');

			$ocx_response = $this->model_extension_module_smart_marketing_api->getOCXBlocks();

			if (isset($ocx_response['error'])) {
				$this->clearBlocksCache();
			}

			if (isset($ocx_response['response']['blocks'])) {
				$blocks = $ocx_response['response']['blocks'];

				$this->cache->set('ocx.blocks', $blocks);
			}
		}

		return $blocks;
	}

	public function clearBlocksCache() {
		$this->cache->delete('ocx.blocks');
	}
}
