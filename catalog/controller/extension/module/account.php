<?php
class ControllerExtensionModuleAccount extends Controller {
	public function index() {
		$this->load->language('extension/module/account');

		$data['logged'] = $this->customer->isLogged();
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['forgotten'] = $this->url->link('account/forgotten', '', true);
		$data['account'] = $this->url->link('account/account', '', true);
		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['password'] = $this->url->link('account/password', '', true);
		$data['address'] = $this->url->link('account/address', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['garage'] = $this->url->link('account/garage');
		$data['auction'] = $this->url->link('account/auction');
		$data['product'] = $this->url->link('catalog/product');
		
		
		$data['classified'] = $this->url->link('account/consignment', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['reward'] = $this->url->link('account/reward', '', true);
		$data['return'] = $this->url->link('account/return', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['recurring'] = $this->url->link('account/recurring', '', true);

		
		
		$data['active'] = '';		
		if(isset($this->request->get['route'])){
			if($this->request->get['route'] == 'account/register'){
				$data['active'] = 'register';
			}elseif($this->request->get['route'] == 'account/login'){
				$data['active'] = 'login';
			}elseif($this->request->get['route'] == 'account/logout'){
				$data['active'] = 'logout';
			}elseif($this->request->get['route'] == 'account/forgotten'){
				$data['active'] = 'forgotten';
			}elseif($this->request->get['route'] == 'account/account'){
				$data['active'] = 'account';
			}elseif($this->request->get['route'] == 'account/edit'){
				$data['active'] = 'edit';
			}elseif($this->request->get['route'] == 'account/simpleedit'){
				$data['active'] = 'edit';
			}elseif($this->request->get['route'] == 'account/password'){
				$data['active'] = 'password';
			}elseif($this->request->get['route'] == 'account/address'){
				$data['active'] = 'address';
			}elseif($this->request->get['route'] == 'account/wishlist'){
				$data['active'] = 'wishlist';
			}elseif($this->request->get['route'] == 'account/garage'){
				$data['active'] = 'garage';
			}elseif($this->request->get['route'] == 'account/auction'){
				$data['active'] = 'auction';
			}elseif($this->request->get['route'] == 'catalog/product'){
				$data['active'] = 'product';
			}elseif(strpos($this->request->get['route'], 'account/order') !== false){
				$data['active'] = 'order';
			}elseif($this->request->get['route'] == 'account/download'){
				$data['active'] = 'download';
			}elseif($this->request->get['route'] == 'account/reward'){
				$data['active'] = 'reward';
			}elseif($this->request->get['route'] == 'account/return'){
				$data['active'] = 'return';
			}elseif($this->request->get['route'] == 'account/transaction'){
				$data['active'] = 'transaction';
			}elseif($this->request->get['route'] == 'account/newsletter'){
				$data['active'] = 'newsletter';
			}elseif($this->request->get['route'] == 'account/recurring'){
				$data['active'] = 'recurring';
			}
			
			
		}

		
		$this->load->model('account/auction');
		$auction_total = $this->model_account_auction->getCustomerAuctionProductTotal();
	
		if($auction_total > 0){
			$data['text_auction'] = $this->language->get('text_auction') . ' ('.$auction_total.')';
		}

		$this->load->model('account/notify');
		$data['total_notify'] = $this->model_account_notify->getNotifyTotal();
		
		$this->load->model('account/wishlist');
		$wishlist_total = $this->model_account_wishlist->getTotalWishlist();
		
		if($wishlist_total > 0){
			$data['text_wishlist'] = $this->language->get('text_wishlist') . ' ('.$wishlist_total.')';
		}
	
		return $this->load->view('extension/module/account', $data);
	}
}