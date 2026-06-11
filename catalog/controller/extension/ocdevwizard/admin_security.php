<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##
class ControllerExtensionOcdevwizardAdminSecurity extends Controller {
  private $_name = 'admin_security';
  private $_code = 'ocdw_admin_security';

  public function actions() {
    $data = [];

    $models = [
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));

    $data['breadcrumbs'] = [];

    $data['breadcrumbs'][] = [
      'text'      => $this->language->get('text_home'),
      'href'      => $this->url->link('common/home'),
      'separator' => false
    ];

    $form_data = $this->model_extension_ocdevwizard_helper->getSettingData($this->_name.'_form_data',(int)$this->config->get('config_store_id'));

    $token = (isset($this->request->get['token']) && $this->request->get['token']) ? $this->request->get['token'] : '';
    $type  = (isset($this->request->get['type']) && $this->request->get['type']) ? $this->request->get['type'] : '';

    $record_info = $this->{'model_extension_ocdevwizard_'.$this->_name}->getRecordByToken($token);

    if (isset($form_data['activate']) && $form_data['activate'] && $record_info && $type) {
      $this->document->setTitle($this->language->get('heading_title_actions'));

      $data['breadcrumbs'][] = [
        'text'      => $this->language->get('heading_title_actions'),
        'href'      => $this->url->link('extension/ocdevwizard/'.$this->_name.'/actions'),
        'separator' => (version_compare(VERSION,'2.0.0.0','<')) ? $this->language->get('text_separator') : ''
      ];

      $data['heading_title'] = $this->language->get('heading_title_actions');

      if ($type == 1) {
        $data['text_result_message'] = $this->language->get('text_record_permanent_ban_user_success');

        $this->{'model_extension_ocdevwizard_'.$this->_name}->addBanned($record_info['ip']);
      } else if ($type == 2) {
        $data['text_result_message'] = $this->language->get('text_record_disable_user_success');

        $this->{'model_extension_ocdevwizard_'.$this->_name}->disableUser($record_info['user_id']);
      }

      $data['_name'] = $this->_name;
      $data['_code'] = $this->_code;

      if (version_compare(VERSION,'2.0.0.0','>=')) {
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['column_right']   = $this->load->controller('common/column_right');
        $data['content_top']    = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');
      } else {
        $this->children = [
          'common/column_left',
          'common/column_right',
          'common/content_top',
          'common/content_bottom',
          'common/footer',
          'common/header'
        ];
      }

      if (version_compare(VERSION,'2.1.0.2','<=')) {
        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/extension/ocdevwizard/'.$this->_name.'/actions.tpl')) {
          $view = $this->load->view($this->config->get('config_template').'/template/extension/ocdevwizard/'.$this->_name.'/actions.tpl',$data);
        } else {
          $view = $this->load->view('default/template/extension/ocdevwizard/'.$this->_name.'/actions.tpl',$data);
        }

        $this->response->setOutput($view);
      } else if (version_compare(VERSION,'3.0.0.0','>=')) {
        $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/actions',$data));
      } else if (version_compare(VERSION,'2.0.0.0','<')) {
        $this->data = $data;

        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/extension/ocdevwizard/'.$this->_name.'/actions.tpl')) {
          $this->template = $this->config->get('config_template').'/template/extension/ocdevwizard/'.$this->_name.'/actions.tpl';
        } else {
          $this->template = 'default/template/extension/ocdevwizard/'.$this->_name.'/actions.tpl';
        }

        $this->response->setOutput($this->render());
      } else {
        $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/actions.tpl',$data));
      }
    } else {
      $data['breadcrumbs'][] = [
        'text'      => $this->language->get('error_actions'),
        'href'      => $this->url->link('extension/ocdevwizard/'.$this->_name.'/actions'),
        'separator' => (version_compare(VERSION,'2.0.0.0','<')) ? $this->language->get('text_separator') : ''
      ];

      $this->document->setTitle($this->language->get('error_actions'));

      $data['heading_title'] = $this->language->get('error_actions');

      $data['text_error'] = $this->language->get('error_actions');

      $data['button_continue'] = $this->language->get('button_continue');

      $data['continue'] = $this->url->link('common/home');

      if (version_compare(VERSION,'2.0.0.0','>=')) {
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['column_right']   = $this->load->controller('common/column_right');
        $data['content_top']    = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');
      } else {
        $this->children = [
          'common/column_left',
          'common/column_right',
          'common/content_top',
          'common/content_bottom',
          'common/footer',
          'common/header'
        ];
      }

      if (version_compare(VERSION,'2.1.0.2','<=')) {
        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/error/not_found.tpl')) {
          $view = $this->load->view($this->config->get('config_template').'/template/error/not_found.tpl',$data);
        } else {
          $view = $this->load->view('default/template/error/not_found.tpl',$data);
        }

        $this->response->setOutput($view);
      } else if (version_compare(VERSION,'3.0.0.0','>=')) {
        $this->response->setOutput($this->load->view('error/not_found',$data));
      } else if (version_compare(VERSION,'2.0.0.0','<')) {
        $this->data = $data;

        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/extension/ocdevwizard/'.$this->_name.'/not_found.tpl')) {
          $this->template = $this->config->get('config_template').'/template/extension/ocdevwizard/'.$this->_name.'/not_found.tpl';
        } else {
          $this->template = 'default/template/extension/ocdevwizard/'.$this->_name.'/not_found.tpl';
        }

        $this->response->setOutput($this->render());
      } else {
        $this->response->setOutput($this->load->view('error/not_found.tpl',$data));
      }
    }
  }
}

?>