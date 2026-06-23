<?php
class ControllerExtensionModuleSmartMarketingBlock extends Controller {
	private $error = array();

	public function index() {
		$json = array();

		$this->load->model('extension/module/smart_marketing/block');
		$this->load->model('extension/module/smart_marketing/template');

		$blocks = $this->model_extension_module_smart_marketing_block->getOCXBlocks();

		if ($blocks) {
			foreach ($blocks as $block) {
				$json['blocks'][] = array(
					'block_id'      => $block['block_id'],
					'code'          => $block['code'],
					'label'         => $block['label'],
					'category'      => $block['category'],
					'attributes'    => $block['attributes'],
					'content'       => $this->model_extension_module_smart_marketing_template->convertSpecialKeywords($block['content'])
				);
			}

			$json['success'] = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
