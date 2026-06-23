<?php

/*
    Module: Watermark for OpenCart 2.x.x.x
    Author: webengenier@gmail.com
    Version: 1.0
    Date: Y.2015 M.11 D.24
    Filelist:

        /admin/controller/module/watermark.php
        /admin/language/english/module/watermark.php
        /admin/model/module/watermark.php
        /admin/view/template/module/watermark.tpl

        /image/catalog/watermark_default.png

        /catalog/model/module/watermark/image.watermark.php

        /system/watermark.ocmod.xml

    Modif:

        /catalog/model/tool/image.php

        /module/featured.php        
        /module/bestseller.php      
        /module/ebay_listing.php    
        /module/carousel.php        
        /module/latest.php          
        /module/special.php         
        /module/slideshow.php       
        /module/banner.php          

        /feed/google_sitemap.php    
        /feed/google_base.php       

        /product/product.php        
        /product/special.php        
        /product/manufacturer.php   
        /product/search.php         
        /product/compare.php        
        /product/category.php       

        /common/cart.php            
        /account/wishlist.php       
        /payment/pp_express.php     
        /checkout/cart.php          
*/

class ControllerExtensionModuleWatermark extends Controller {

    private $error = array();

    public function index() {

        $this->load->language('extension/module/watermark');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/watermark');

        if( $this->request->post )
        {
            //We need image size
            $info = getimagesize(DIR_IMAGE . $this->request->post['image']);
            $data['size_x'] = $info[0];
            $data['size_y'] = $info[1];
            $data['active'] = (int)$this->request->post['active'];
            $data['image'] = $this->request->post['image'];
            $data['zoom'] = (string)$this->request->post['zoom'];
            $data['pos_x'] = (int)$this->request->post['pos_x'];
            $data['pos_y'] = (int)$this->request->post['pos_y'];
            $data['pos_x_center'] = (int)$this->request->post['pos_x_center'];
            $data['pos_y_center'] = (int)$this->request->post['pos_y_center'];
            $data['opacity'] = (string)round((float)$this->request->post['opacity'],1);
            $data['category_image'] = (int)$this->request->post['category_image'];
            $data['product_thumb'] = (int)$this->request->post['product_thumb'];
            $data['product_popup'] = (int)$this->request->post['product_popup'];
            $data['product_list'] = (int)$this->request->post['product_list'];
            $data['product_additional'] = (int)$this->request->post['product_additional'];
            $data['product_related'] = (int)$this->request->post['product_related'];
            $data['product_in_compare'] = (int)$this->request->post['product_in_compare'];
            $data['product_in_wish_list'] = (int)$this->request->post['product_in_wish_list'];
            $data['product_in_cart'] = (int)$this->request->post['product_in_cart'];

            foreach ($data as $key => $value) {
                $this->model_extension_module_watermark->change( $key, $value );
            }
            $this->error['success'] = $this->language->get('text_success');
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['success'])) {
            $data['error_success'] = $this->error['success'];
        } else {
            $data['error_success'] = '';
        }

        $data['heading_title'] = $this->language->get('heading_title_for_opencart');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/watermark', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['options'] = $this->model_extension_module_watermark->getOptions();
        unset($data['options']['size_x']);
        unset($data['options']['size_y']);
        $data['options_lang'] = array();
        foreach ($data['options'] as $key => $value) {
            $data['options_lang'][$key] = $this->language->get($key);
        }

        $data['active'] = $data['options']['active'];

        $this->load->model('tool/image');
        $data['thumb'] = $this->model_tool_image->resize($data['options']['image'], 100, 100);
        $data['placeholder'] = $this->model_tool_image->resize($data['options']['image'], 250, 250);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/watermark', $data));
    }

    public function install() {

        $this->load->model('extension/module/watermark');
        $this->model_extension_module_watermark->install();

    }

    public function uninstall() {

        $this->load->model('extension/module/watermark');
        $this->model_extension_module_watermark->uninstall();

    }

}