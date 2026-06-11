<?php

use ConvergeGatewayEu\catalog\service\CartService;
use ConvergeGatewayEu\ConvergeGatewayEu;
use ConvergeGatewayEu\core\mvc\controller\FrontControllerTrait;
use ConvergeGatewayEu\Shared\ConvergeValidation\ViolationRenderer;
use ConvergeGatewayEu\Shared\ConvergeValidation\CheckoutInputValidator;
use ConvergeGatewayEu\shared\gateway\Wrapper;
use ConvergeGatewayEu\core\bridge\OpenCart;
use Elavon\Converge2\Schema\Converge2Schema;

class ControllerExtensionPaymentConvergeGatewayEu extends Controller {
	use FrontControllerTrait;

	public function validateCheckout(&$route, &$data, &$output) {
		if (empty($this->session->data['payment_method']['code']) || $this->session->data['payment_method']['code'] != ConvergeGatewayEu::EXTENSION_NAME) {
			return;
		}

		$this->load->language('common/header');
		$this->load->language('extension/payment/' . ConvergeGatewayEu::EXTENSION_NAME);
		ConvergeGatewayEu::requireAutoload();
		OpenCart::context($this);

		$response_output = json_decode($this->response->getOutput());
		if (!empty($response_output->error)) {
			return;
		}

		$violation_renderer = new ViolationRenderer($this->getRegistry());
		$checkout_input_validator = new CheckoutInputValidator($violation_renderer);

		$input = array('telephone' => $this->customer->getTelephone());
		foreach (array(
			         'payment_address',
			         'shipping_address',
			         'comment')
		         as $key) {

			if (isset($this->session->data[$key])) {
				$input[$key] = $this->session->data[$key];
			}
		}

		if (!empty($this->session->data['guest']['telephone'])) {
			$input['payment_address']['telephone_guest'] = $this->session->data['guest']['telephone'];
		}

		$error = FALSE;
		$checkout_input_validator->validate($input);
		if ($checkout_input_validator->hasViolations()) {
			$error_messages = $checkout_input_validator->getErrorMessages();
			$error = $error_messages[0];
		} else if (count(CartService::getCartItems()) > Converge2Schema::getInstance()->getOrderMaxItems()) {
			$error = sprintf($this->language->get('max_order_items_error_message'), Converge2Schema::getInstance()->getOrderMaxItems());
		} else if ($this->getCartTotal() <= 0) {
			$error = $this->language->get('zero_amount_error_message');
		} else if (!Wrapper::instance()->canConnect()) {
			$error = $this->language->get('converge_down_error_message');
		}

		if ($error) {
			$response_output = array(
				'error' => array(
					'warning' => $error,
				)
			);
			$this->response->setOutput(json_encode($response_output));
		}
	}

	protected function getCartTotal() {
		$cart_data = CartService::getData();
		return isset($cart_data['total']) ? $cart_data['total'] : 0;
	}
}
