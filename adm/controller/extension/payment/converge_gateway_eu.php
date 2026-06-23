<?php

use ConvergeGatewayEu\ConvergeGatewayEu;
use ConvergeGatewayEu\core\mvc\controller\FrontControllerTrait;

class ControllerExtensionPaymentConvergeGatewayEu extends Controller
{
    use FrontControllerTrait {
    	index as traitIndex;
    }

    public function index() {
	    $this->registerEvents();
    	return $this->traitIndex();
    }

    protected function registerEvents() {
	    $this->load->model('setting/event');

	    $trigger = 'catalog/controller/checkout/payment_method/save/after';
	    $code =  ConvergeGatewayEu::EXTENSION_NAME . '_validate_checkout';
	    $event = $this->model_setting_event->getEventByCode($code);

	    if (!$event) {
		    $this->model_setting_event->addEvent(
		    	$code,
			    $trigger,
			    'extension/payment/' . ConvergeGatewayEu::EXTENSION_NAME . '/validateCheckout');
	    }
    }
}