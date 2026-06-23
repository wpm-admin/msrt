<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##
class ControllerExtensionOcdevwizardAdminSecurity extends Controller {
  private $error         = [];
  private $_name         = 'admin_security';
  private $_name_ucfirst = 'AdminSecurity';
  private $_code         = 'ocdw_admin_security';
  private $_version;
  private $_session_token;
  private $_ssl_code;

  public function __construct($registry) {
    parent::__construct($registry);

    if (version_compare(VERSION,'2.2.0.0','>=')) {
      $this->_ssl_code = true;
    } else {
      $this->_ssl_code = 'SSL';
    }

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->_session_token = 'user_token='.$this->session->data['user_token'];
    } else {
      $this->_session_token = 'token='.$this->session->data['token'];
    }

    if (file_exists(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name) && is_dir(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name)) {
      if (file_exists(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/module.ocdw')) {
        $version_array = json_decode(file_get_contents(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/module.ocdw'),true);

        if ($version_array) {
          $this->_version = $version_array['module'];
        }
      }
    }
  }

  public function index() {
    $data = [];

    $models = [
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));
    $this->document->setTitle($this->language->get('heading_title'));

    $scripts = [
      '//ocdevwizard.com/helper/magnific-popup/jquery.magnific-popup.min.js',
      '//ocdevwizard.com/helper/growl-notifications/growl-notification.min.js'
    ];

    foreach ($scripts as $script) {
      if ($script) {
        $this->document->addScript($script);
      }
    }

    $styles = [
      '//ocdevwizard.com/helper/main/helper.min.css',
      '//ocdevwizard.com/helper/magnific-popup/magnific-popup.min.css',
      '//ocdevwizard.com/helper/growl-notifications/colored-theme.min.css'
    ];

    foreach ($styles as $style) {
      $this->document->addStyle($style);
    }

    $links = [
      ($this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG).'/image/catalog/ocdevwizard/'.$this->_name.'/favicon.ico'
    ];

    foreach ($links as $link) {
      if ($link) {
        $this->document->addLink($link,'icon');
      }
    }

    $data['store_id'] = $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;

    $this->{'model_extension_ocdevwizard_'.$this->_name}->createDBTables();

    $data['breadcrumbs'] = [
      0 => [
        'text' => $this->language->get('text_home'),
        'href' => (version_compare(VERSION,'2.0.0.0','>=')) ? $this->url->link('common/dashboard',$this->_session_token,$this->_ssl_code) : $this->url->link('common/home',$this->_session_token,$this->_ssl_code)
      ],
      1 => [
        'text' => $this->language->get('text_page_extensions'),
        'href' => $this->url->link('extension/ocdevwizard/helper',$this->_session_token,$this->_ssl_code)
      ],
      2 => [
        'text'   => $this->language->get('heading_title'),
        'href'   => false,
        'active' => true
      ]
    ];

    $data['cancel'] = $this->url->link('extension/ocdevwizard/helper',$this->_session_token,$this->_ssl_code);

    $data['_name']    = $this->_name;
    $data['token']    = $this->_session_token;
    $data['_version'] = $this->_version;

    $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,$store_id);

    $data['license_key'] = $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

    $response_info = $this->send_curl([],'validate_access',$license_key);

    if ($response_info['status'] == 200 && !empty($response_info['response'])) {
      if (version_compare(VERSION,'2.0.0.0','<')) {
        $this->redirect($this->url->link('extension/ocdevwizard/'.$this->_name.'/base',$this->_session_token.'&store_id='.$store_id,$this->_ssl_code));
      } else {
        $this->response->redirect($this->url->link('extension/ocdevwizard/'.$this->_name.'/base',$this->_session_token.'&store_id='.$store_id,$this->_ssl_code));
      }
    }

    $data['column_left'] = '';

    if (version_compare(VERSION,'2.0.0.0','>=')) {
      $data['header']      = $this->load->controller('common/header');
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['footer']      = $this->load->controller('common/footer');
    }

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/unlicensed',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/unlicensed.tpl';

      $this->children = [
        'extension/ocdevwizard/helper/header',
        'extension/ocdevwizard/helper/footer'
      ];

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/unlicensed.tpl',$data));
    }
  }

  public function index_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      if (!$this->check_remote_file()) {
        $json['error']['warning']['license_server'] = $this->language->get('error_license_server');
      }

      if (empty($this->request->post[$this->_name.'_license'])) {
        $json['error']['license_key'] = $this->language->get('error_license_key');
      }

      if (!isset($json['error'])) {
        $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;

        $response_info = $this->send_curl([],'validate_access',$this->request->post[$this->_name.'_license']);

        if ($response_info['status'] == 200 && !empty($response_info['response'])) {
          $response_info = $this->send_curl([],'default',$this->request->post[$this->_name.'_license'],$store_id);

          $this->send_curl([],'license_key',$this->request->post[$this->_name.'_license'],$store_id);

          if ($response_info['status'] == 200 && !empty($response_info['response'])) {
            $this->send_curl([],'default',$this->request->post[$this->_name.'_license'],$store_id);
          }

          $json['redirect'] = 'index.php?route=extension/ocdevwizard/'.$this->_name.'/base&'.$this->_session_token.'&store_id='.$store_id;
        } else {
          $json['error']['license_key'] = $this->language->get('error_license_key_not_valid');
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function base() {
    $data = [];

    $models = [
      'setting/store',
      'localisation/language',
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    if (version_compare(VERSION,'2.0.3.1','<=')) {
      $models[] = 'sale/customer_group';
    } else {
      $models[] = 'customer/customer_group';
    }

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));
    $this->document->setTitle($this->language->get('heading_title'));

    $scripts = [
      '//ocdevwizard.com/helper/codemirror/lib/codemirror.js',
      '//ocdevwizard.com/helper/codemirror/lib/css.js'
    ];

    if (version_compare(VERSION,'2.0.0.0','>=')) {
      $scripts[] = 'view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js';
    }

    $scripts[] = '//ocdevwizard.com/helper/magnific-popup/jquery.magnific-popup.min.js';
    $scripts[] = '//ocdevwizard.com/helper/growl-notifications/growl-notification.min.js';
    $scripts[] = '//ocdevwizard.com/helper/pickr/pickr.min.js';

    if (version_compare(VERSION,'2.3.0.2.3','<')) {
      $scripts[] = '//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.js';
    }

    if (version_compare(VERSION,'2.3.0.2','>=')) {
      $scripts[] = 'view/javascript/summernote/summernote.js';
      $scripts[] = 'view/javascript/summernote/opencart.js';
    }

    $scripts[] = '//ocdevwizard.com/helper/patternlock/patternlock.min.js';

    foreach ($scripts as $script) {
      if ($script) {
        $this->document->addScript($script);
      }
    }

    $styles = [
      '//ocdevwizard.com/helper/main/helper.min.css',
      '//ocdevwizard.com/helper/codemirror/lib/codemirror.min.css'
    ];

    if (version_compare(VERSION,'2.0.0.0','>=')) {
      $styles[] = 'view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css';
    }

    $styles[] = '//ocdevwizard.com/helper/magnific-popup/magnific-popup.min.css';
    $styles[] = '//ocdevwizard.com/helper/growl-notifications/colored-theme.min.css';
    $styles[] = '//ocdevwizard.com/helper/pickr/themes/monolith.min.css';

    if (version_compare(VERSION,'2.3.0.2.3','<')) {
      $styles[] = '//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.css';
    }

    if (version_compare(VERSION,'2.3.0.2','>=')) {
      $styles[] = 'view/javascript/summernote/summernote.css';
    }

    $styles[] = '//ocdevwizard.com/helper/patternlock/patternlock.min.css';

    foreach ($styles as $style) {
      $this->document->addStyle($style);
    }

    $links = [
      ($this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG).'/image/catalog/ocdevwizard/'.$this->_name.'/favicon.ico'
    ];

    foreach ($links as $link) {
      if ($link) {
        $this->document->addLink($link,'icon');
      }
    }

    $data['store_id'] = $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;

    $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,$store_id);

    $data['license_key'] = $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

    $response_info = $this->send_curl([],'validate_access',$license_key);

    if ($response_info['status'] != 200 || empty($response_info['response'])) {
      if (version_compare(VERSION,'2.0.0.0','<')) {
        $this->redirect($this->url->link('extension/ocdevwizard/'.$this->_name,$this->_session_token.'&store_id='.$store_id,$this->_ssl_code));
      } else {
        $this->response->redirect($this->url->link('extension/ocdevwizard/'.$this->_name,$this->_session_token.'&store_id='.$store_id,$this->_ssl_code));
      }
    }

    $data['license_expire']        = '';
    $data['license_expire_status'] = 0;

    if ($response_info['status'] == 200 || !empty($response_info['response'])) {
      if ($response_info['response']['date_end'] == '0000-00-00') {
        $data['license_expire'] = $this->language->get('text_license_expire_forever');
      } else {
        $license_expire_days1 = strtotime(date('Y-m-d'));
        $license_expire_days2 = strtotime($response_info['response']['date_end']);

        $license_expire_diff = $license_expire_days2 - $license_expire_days1;

        if (floor($license_expire_diff / 3600 / 24) < 0) {
          $data['license_expire']        = $this->language->get('text_license_end');
          $data['license_expire_status'] = 1;
        } else {
          $data['license_expire']        = sprintf($this->language->get('text_license_date_end'),date("F j, Y",strtotime($response_info['response']['date_end'])),floor($license_expire_diff / 3600 / 24).' '.$this->day_formatting(floor($license_expire_diff / 3600 / 24),$this->language->get('text_license_expire_day_1'),$this->language->get('text_license_expire_day_2')));
          $data['license_expire_status'] = 2;
        }
      }

      $data['license_type']   = $response_info['response']['type'];
      $data['license_holder'] = $response_info['response']['holder'];
    }

    $data['breadcrumbs'] = [
      0 => [
        'text' => $this->language->get('text_home'),
        'href' => (version_compare(VERSION,'2.0.0.0','>=')) ? $this->url->link('common/dashboard',$this->_session_token,$this->_ssl_code) : $this->url->link('common/home',$this->_session_token,$this->_ssl_code)
      ],
      1 => [
        'text' => $this->language->get('text_page_extensions'),
        'href' => $this->url->link('extension/ocdevwizard/helper',$this->_session_token,$this->_ssl_code)
      ],
      2 => [
        'text'   => $this->language->get('heading_title'),
        'href'   => false,
        'active' => true
      ]
    ];

    $data['cancel'] = $this->url->link('extension/ocdevwizard/helper',$this->_session_token,$this->_ssl_code);

    $data['_name']            = $this->_name;
    $data['_code']            = $this->_code;
    $data['_version']         = $this->_version;
    $data['token']            = $this->_session_token;
    $data['is_opencart_1']    = (version_compare(VERSION,'2.0.0.0','<')) ? 1 : 0;
    $data['is_opencart_3']    = (version_compare(VERSION,'3.0.0.0','>=')) ? 1 : 0;
    $data['config_store_url'] = $config_store_url = $this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER;

    $form_data = isset($this->request->post['form_data']) ? $this->request->post['form_data'] : $this->model_extension_ocdevwizard_helper->getSettingData($this->_name.'_form_data',$store_id);

    $data['form_data'] = [];

    $response_info = $this->send_curl(['array' => $form_data],'form_data',$license_key);

    if ($response_info['status'] == 200 && !empty($response_info['response'])) {
      $data['form_data'] = $response_info['response'];
    }

    $text_data = isset($this->request->post['text_data']) ? $this->request->post['text_data'] : $this->model_extension_ocdevwizard_helper->getSettingData($this->_name.'_text_data',$store_id);

    $data['text_data'] = [];

    $response_info = $this->send_curl(['array' => $text_data],'text_data',$license_key);

    if ($response_info['status'] == 200 && !empty($response_info['response'])) {
      $data['text_data'] = $response_info['response'];
    }

    $default_store = [
      0 => [
        'store_id' => 0,
        'name'     => $this->config->get('config_name').' (Default)'
      ]
    ];

    $all_stores = array_merge($this->model_setting_store->getStores(),$default_store);

    $data['all_stores'] = [];

    foreach ($all_stores as $store) {
      $data['all_stores'][] = [
        'href'     => $this->url->link('extension/ocdevwizard/'.$this->_name.'/base',$this->_session_token.'&store_id='.$store['store_id'],$this->_ssl_code),
        'store_id' => $store['store_id'],
        'name'     => $store['name']
      ];
    }

    $backup_folders = [
      'config',
      'record',
      'banned',
      'email_template'
    ];

    foreach ($backup_folders as $backup_folder) {
      $data[$backup_folder.'_backup_files'] = [];

      if ($this->get_backup_files($backup_folder)) {
        foreach ($this->get_backup_files($backup_folder) as $backup_file) {
          $name_string                            = explode("/",$backup_file);
          $name                                   = array_pop($name_string);
          $data[$backup_folder.'_backup_files'][] = ['name' => $name];
        }
      }
    }

    $data['backgrounds'] = [];

    if ($this->get_background()) {
      foreach ($this->get_background() as $background) {
        $name_string           = explode("/",$background);
        $name                  = array_pop($name_string);
        $data['backgrounds'][] = [
          'src'  => $background,
          'name' => $name
        ];
      }
    }

    $data['languages'] = [];

    foreach ($this->model_localisation_language->getLanguages() as $language) {
      if (version_compare(VERSION,'2.1.0.2.1','<=')) {
        $data['languages'][] = [
          'language_id' => $language['language_id'],
          'code'        => $language['code'],
          'name'        => $language['name'],
          'image'       => 'view/image/flags/'.$language['image']
        ];
      } else {
        $data['languages'][] = [
          'language_id' => $language['language_id'],
          'code'        => $language['code'],
          'name'        => $language['name'],
          'image'       => 'language/'.$language['code'].'/'.$language['code'].'.png'
        ];
      }
    }

    $data['email_templates_1'] = $this->{'model_extension_ocdevwizard_'.$this->_name}->getEmailTemplates(['filter_status' => 1,'filter_assignment' => 1]);
    $data['email_templates_2'] = $this->{'model_extension_ocdevwizard_'.$this->_name}->getEmailTemplates(['filter_status' => 1,'filter_assignment' => 2]);

    $data['technical_url_bacup'] = ((isset($form_data['secret_key']) && $form_data['secret_key']) && (isset($form_data['secret_password']) && $form_data['secret_password'])) ? ($config_store_url.'?'.$form_data['secret_key'].'='.$form_data['secret_password']) : $this->language->get('error_technical_url');

    $data['stylesheet_code'] = (is_file(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/stylesheet.css')) ? file_get_contents(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/stylesheet.css') : $this->language->get('error_failed_load_stylesheet');

    $data['stylesheet_code_rtl'] = (is_file(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/stylesheet_rtl.css')) ? file_get_contents(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/stylesheet_rtl.css') : $this->language->get('error_failed_load_stylesheet');

    $data['column_left'] = '';

    if (version_compare(VERSION,'2.0.0.0','>=')) {
      $data['header']      = $this->load->controller('common/header');
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['footer']      = $this->load->controller('common/footer');
    }

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/index',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/index.tpl';

      $this->children = [
        'extension/ocdevwizard/helper/header',
        'extension/ocdevwizard/helper/footer'
      ];

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/index.tpl',$data));
    }
  }

  public function base_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    $models = [
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!$this->check_remote_file()) {
      $json['error']['warning']['license_server'] = $this->language->get('error_license_server');
    }

    if (!isset($json['error'])) {
      $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;

      $response_errors = $this->send_curl($this->request->post['form_data'],'validate_data',$this->request->post[$this->_name.'_license'],$store_id,'config');

      if ($response_errors['status'] == 200 && !empty($response_errors['response'])) {
        foreach ($response_errors['response'] as $key => $error) {
          $json['error'][$key] = $this->language->get($error);
        }
      }

      $post_mixed = [
        'access_type' => (isset($this->request->post['form_data']['access_type'])) ? $this->request->post['form_data']['access_type'] : '',
        'text_data'   => (isset($this->request->post['text_data'])) ? $this->request->post['text_data'] : []
      ];

      $response_errors = $this->send_curl($post_mixed,'validate_data',$this->request->post[$this->_name.'_license'],$store_id,'config_description');

      if ($response_errors['status'] == 200 && !empty($response_errors['response'])) {
        foreach ($response_errors['response'] as $key => $language) {
          foreach ($language as $language_id => $error) {
            $json['error']['text_data_language'][$key][$language_id] = $this->language->get($error);
          }
        }
      }

      if (!isset($json['error'])) {
        $response_info = $this->send_curl($this->request->post,'base',$this->request->post[$this->_name.'_license']);

        $this->cache(true);

        if ($response_info['status'] == 200 && !empty($response_info['response'])) {
          $this->send_curl($response_info['response'],'edit_setting',$this->request->post[$this->_name.'_license'],$store_id);

          $json['success'] = $this->language->get('text_success');
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function uninstall() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;
      $type     = (isset($this->request->get['type']) && $this->request->get['type']) ? $this->request->get['type'] : '';

      $models = [
        'user/user_group',
        'extension/ocdevwizard/helper',
        'extension/ocdevwizard/'.$this->_name
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      if (version_compare(VERSION,'2.0.0.0','>=')) {
        $modules = ['extension/ocdevwizard/'.$this->_name];

        foreach ($modules as $module) {
          $this->model_user_user_group->removePermission($this->user->getId(),'access',$module);
          $this->model_user_user_group->removePermission($this->user->getId(),'modify',$module);
        }
      }

      $this->{'model_extension_ocdevwizard_'.$this->_name}->deleteDBTables();

      $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,$store_id);

      $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

      $this->send_curl([],'delete_setting',$license_key,$store_id);

      if ($type == 'remove_files') {
        $files = [];

        $files1  = glob(DIR_APPLICATION.'view/*/extension/ocdevwizard/'.$this->_name.'*');
        $files2  = glob(DIR_APPLICATION.'model/extension/ocdevwizard/'.$this->_name.'*');
        $files3  = glob(DIR_APPLICATION.'language/*/extension/ocdevwizard/'.$this->_name.'*');
        $files4  = glob(DIR_CATALOG.'view/javascript/ocdevwizard/'.$this->_name.'*');
        $files5  = glob(DIR_CATALOG.'view/theme/*/stylesheet/ocdevwizard/'.$this->_name.'*');
        $files6  = glob(DIR_CATALOG.'view/theme/*/template/extension/ocdevwizard/'.$this->_name.'*');
        $files7  = glob(DIR_CATALOG.'model/extension/ocdevwizard/'.$this->_name.'*');
        $files8  = glob(DIR_CATALOG.'model/api/ocdevwizard/'.$this->_name.'*');
        $files9  = glob(DIR_CATALOG.'language/*/extension/ocdevwizard/'.$this->_name.'*');
        $files10 = glob(DIR_CATALOG.'controller/extension/ocdevwizard/'.$this->_name.'*');
        $files11 = glob(DIR_CATALOG.'controller/api/ocdevwizard/'.$this->_name.'*');
        $files12 = glob(DIR_IMAGE.'catalog/ocdevwizard/'.$this->_name.'*');
        $files13 = glob(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'*');

        $files = array_merge($files1,$files2,$files3,$files4,$files5,$files6,$files7,$files8,$files9,$files10,$files11,$files12,$files13);

        if ($files) {
          foreach ($files as $file) {
            if (is_dir($file) && is_readable($file)) {
              $this->removeDir($file);
            }

            if (is_file($file) && is_readable($file)) {
              @unlink($file);
            }
          }
        }

        if (version_compare(VERSION,'2.0.0.0','>=')) {
          if (file_exists(DIR_SYSTEM.'ocdevwizard_'.$this->_name.'.ocmod.xml')) {
            @unlink(DIR_SYSTEM.'ocdevwizard_'.$this->_name.'.ocmod.xml');

            if (version_compare(VERSION,'2.0.0.0','>=')) {
              $this->load->controller('marketplace/modification/refresh');
            }
          }
        }

        if (version_compare(VERSION,'2.0.0.0','<')) {
          if (file_exists(substr_replace(DIR_SYSTEM,'/vqmod/xml/',-8).'ocdevwizard_'.$this->_name.'.xml')) {
            $files2 = glob(substr_replace(DIR_SYSTEM,'/vqmod/xml/',-8).'ocdevwizard_'.$this->_name.'.xml');

            $vqmod_files = glob(substr_replace(DIR_SYSTEM,'/vqmod/vqcache/vq*',-8));

            if ($vqmod_files) {
              foreach ($vqmod_files as $vqmod_file) {
                if (file_exists($vqmod_file)) {
                  unlink($vqmod_file);
                }
              }
            }

            if (file_exists(substr_replace(DIR_SYSTEM,'/vqmod/mods.cache',-8))) {
              unlink(substr_replace(DIR_SYSTEM,'/vqmod/mods.cache',-8));
            }
          }
        }

        $files = glob(DIR_APPLICATION.'controller/extension/ocdevwizard/'.$this->_name.'*');

        if ($files) {
          foreach ($files as $file) {
            if (is_dir($file) && is_readable($file)) {
              $this->removeDir($file);
            }

            if (is_file($file) && is_readable($file)) {
              @unlink($file);
            }
          }
        }
      }

      $json['success'] = $this->language->get('text_success_uninstall');

      $json['redirect'] = 'index.php?route=extension/ocdevwizard/helper&'.$this->_session_token;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function removeDir($dir) {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir."/".$object) == "dir") {
            $this->removeDir($dir."/".$object);
          } else {
            @unlink($dir."/".$object);
          }
        }
      }

      reset($objects);
      rmdir($dir);
    }
  }

  public function restore() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;

      $models = [
        'setting/store',
        'extension/ocdevwizard/helper'
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $default_store = [
        0 => [
          'store_id' => 0,
          'name'     => $this->config->get('config_name').' (Default)'
        ]
      ];

      $all_stores = array_merge($this->model_setting_store->getStores(),$default_store);

      $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,$store_id);

      $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

      $response_info = $this->send_curl([],'validate_access',$license_key,$store_id);

      foreach ($all_stores as $store) {
        if ($response_info['status'] == 200 && !empty($response_info['response'])) {
          $this->send_curl([],'restore',$license_key,$store['store_id']);
        }
      }

      $json['success'] = $this->language->get('text_success_config_restored');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function cache($clear_only = false) {
    if ($clear_only) {
      $files = [];

      $files1 = glob(DIR_CATALOG.'view/javascript/ocdevwizard/ocdevwizard.js');
      $files2 = glob(DIR_CATALOG.'view/javascript/ocdevwizard/'.$this->_name.'/main.js');

      $files = array_merge($files1,$files2);

      if ($files) {
        foreach ($files as $file) {
          if (is_file($file) && is_readable($file)) {
            @unlink($file);
          }
        }
      }
    } else {
      $json = [];

      $this->language->load('extension/ocdevwizard/'.$this->_name);

      if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
        $json['error']['warning']['permission'] = $this->language->get('error_permission');
      }

      if (!isset($json['error'])) {
        $type = (isset($this->request->get['type']) && $this->request->get['type']) ? $this->request->get['type'] : '';

        if ($type == 'cache_backup') {
          $files1 = glob(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/config/*');
          $files2 = glob(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/banned/*');
          $files3 = glob(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/email_template/*');
          $files4 = glob(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/record/*');

          $files = array_merge($files1,$files2,$files3,$files4);

          if ($files) {
            foreach ($files as $file) {
              if (is_file($file) && is_readable($file)) {
                if (!preg_match('/(index.html|index.htm|index.php|.htaccess)/',$file)) {
                  @unlink($file);
                }
              }
            }
          }

          $json['success'] = $this->language->get('text_success_cache_backup');
        } else {
          $files = [];

          $files1 = glob(DIR_CATALOG.'view/javascript/ocdevwizard/ocdevwizard.js');
          $files2 = glob(DIR_CATALOG.'view/javascript/ocdevwizard/'.$this->_name.'/main.js');

          $files = array_merge($files1,$files2);

          if ($files) {
            foreach ($files as $file) {
              if (is_file($file) && is_readable($file)) {
                @unlink($file);
              }
            }
          }

          $json['success'] = $this->language->get('text_success_cache');
        }
      }

      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode($json));
    }
  }

  public function email_template_index() {
    $data = [];

    $models = [
      'localisation/language',
      'extension/ocdevwizard/'.$this->_name
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));

    $data['text_modal_heading'] = (isset($this->request->get['template_id']) && $this->request->get['template_id']) ? $this->language->get('text_edit_email_template') : $this->language->get('text_add_email_template');

    $data['default_language_id'] = $this->config->get('config_language_id');
    $data['_name']               = $this->_name;
    $data['token']               = $this->_session_token;

    $data['template_id'] = $template_id = (isset($this->request->get['template_id'])) ? $this->request->get['template_id'] : 0;

    $email_template_info = ((isset($this->request->get['template_id']) && $this->request->get['template_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) ? $this->{'model_extension_ocdevwizard_'.$this->_name}->getEmailTemplate($this->request->get['template_id']) : [];

    $data['status']      = ($email_template_info) ? $email_template_info['status'] : 1;
    $data['assignment']  = ($email_template_info) ? $email_template_info['assignment'] : '';
    $data['system_name'] = ($email_template_info) ? $email_template_info['system_name'] : $this->language->get('default_email_template_name');

    if ($email_template_info) {
      $data['template_description'] = $this->{'model_extension_ocdevwizard_'.$this->_name}->getEmailTemplateDescription($template_id);
    } else {
      foreach ($this->model_localisation_language->getLanguages() as $language) {
        $data['template_description'][$language['language_id']] = [
          'subject'  => $this->language->get('default_email_template_subject'),
          'template' => ''
        ];
      }
    }

    $data['languages'] = [];

    foreach ($this->model_localisation_language->getLanguages() as $language) {
      if (version_compare(VERSION,'2.1.0.2.1','<=')) {
        $data['languages'][] = [
          'language_id' => $language['language_id'],
          'code'        => $language['code'],
          'name'        => $language['name'],
          'image'       => 'view/image/flags/'.$language['image']
        ];
      } else {
        $data['languages'][] = [
          'language_id' => $language['language_id'],
          'code'        => $language['code'],
          'name'        => $language['name'],
          'image'       => 'language/'.$language['code'].'/'.$language['code'].'.png'
        ];
      }
    }

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/email_template_index',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/email_template_index.tpl';

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/email_template_index.tpl',$data));
    }
  }

  public function email_template_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $models = ['extension/ocdevwizard/helper'];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

        $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

        $response_errors = $this->send_curl($this->request->post,'validate_data',$license_key,0,'email_template');

        if ($response_errors['status'] == 200 && !empty($response_errors['response'])) {
          foreach ($response_errors['response'] as $key => $error) {
            $json['error'][$key] = $this->language->get($error);
          }
        }

        $response_errors = $this->send_curl($this->request->post,'validate_data',$license_key,0,'email_template_description');

        if ($response_errors['status'] == 200 && !empty($response_errors['response'])) {
          foreach ($response_errors['response'] as $key => $language) {
            foreach ($language as $language_id => $error) {
              $json['error']['template_description_language'][$key][$language_id] = $this->language->get($error);
            }
          }
        }

        if (!isset($json['error'])) {
          if (isset($this->request->post['template_id']) && $this->request->post['template_id']) {
            $this->send_curl($this->request->post,'email_template',$license_key,0,'edit');

            $json['success'] = $this->language->get('text_success_email_template_edit');
          } else {
            $this->send_curl($this->request->post,'email_template',$license_key,0,'add');

            $json['success'] = $this->language->get('text_success_email_template_add');
          }
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function email_template_list() {
    $data = [];

    $models = [
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data              = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));
    $page              = (isset($this->request->get['page'])) ? $this->request->get['page'] : 1;
    $sort              = (isset($this->request->get['sort'])) ? $this->request->get['sort'] : 'et.date_added';
    $order             = (isset($this->request->get['order'])) ? $this->request->get['order'] : 'DESC';
    $limit             = (isset($this->request->get['limit'])) ? $this->request->get['limit'] : 10;
    $data['_name']     = $this->_name;
    $data['token']     = $this->_session_token;
    $data['histories'] = [];

    $filter_data = [
      'filter_name'          => (isset($this->request->get['filter_name'])) ? trim($this->request->get['filter_name']) : '',
      'filter_date_added'    => (isset($this->request->get['filter_date_added'])) ? $this->request->get['filter_date_added'] : '',
      'filter_date_modified' => (isset($this->request->get['filter_date_modified'])) ? $this->request->get['filter_date_modified'] : '',
      'filter_status'        => (isset($this->request->get['filter_status'])) ? $this->request->get['filter_status'] : '*',
      'start'                => ($page - 1) * $limit,
      'limit'                => $limit,
      'sort'                 => $sort,
      'order'                => $order
    ];

    $results = $this->{'model_extension_ocdevwizard_'.$this->_name}->getEmailTemplates($filter_data);

    foreach ($results as $result) {
      $data['histories'][] = [
        'template_id'   => $result['template_id'],
        'name'          => $result['system_name'],
        'date_added'    => $result['date_added'],
        'date_modified' => ($result['date_modified'] != '0000-00-00 00:00:00') ? $result['date_modified'] : $this->language->get('text_not_changed'),
        'status'        => $result['status'] ? $this->language->get('text_status_enabled') : $this->language->get('text_status_disabled')
      ];
    }

    $history_total = $this->{'model_extension_ocdevwizard_'.$this->_name}->getTotalEmailTemplates($filter_data);

    $filter_data = [
      'total' => $history_total,
      'page'  => $page,
      'limit' => $limit,
      'token' => $this->_session_token
    ];

    $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

    $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

    $response_info = $this->send_curl($filter_data,'pagination',$license_key,0,'email_template');

    $data['pagination'] = ($response_info['status'] == 200 && !empty($response_info['response'])) ? $response_info['response'] : '';

    $data['results'] = sprintf($this->language->get('text_pagination'),($history_total) ? (($page - 1) * $limit) + 1 : 0,((($page - 1) * $limit) > ($history_total - $limit)) ? $history_total : ((($page - 1) * $limit) + $limit),$history_total,ceil($history_total / $limit));

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/email_template_list',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/email_template_list.tpl';

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/email_template_list.tpl',$data));
    }
  }

  public function preview_email_template() {
    $data = [];

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));

    if (isset($this->request->get['template_id']) && isset($this->request->get['language_id'])) {
      if (!empty($this->request->get['template_id']) && !empty($this->request->get['language_id'])) {
        $models = ['extension/ocdevwizard/'.$this->_name];

        foreach ($models as $model) {
          $this->load->model($model);
        }

        $template_info = $this->{'model_extension_ocdevwizard_'.$this->_name}->getEmailTemplate($this->request->get['template_id'],$this->request->get['language_id']);

        $data['name']     = ($template_info) ? html_entity_decode($template_info['subject'],ENT_QUOTES,'UTF-8') : $this->language->get('error_template_preview');
        $data['template'] = ($template_info) ? html_entity_decode($template_info['template'],ENT_QUOTES,'UTF-8') : $this->language->get('error_template_preview');
      }
    } else {
      $data['name']     = $this->language->get('error_template_preview');
      $data['template'] = $this->language->get('error_template_preview');
    }

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/preview_email_template',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/preview_email_template.tpl';

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/preview_email_template.tpl',$data));
    }
  }

  public function banned_index() {
    $data = [];

    $models = ['extension/ocdevwizard/'.$this->_name];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));

    $data['text_modal_heading'] = (isset($this->request->get['banned_id']) && $this->request->get['banned_id']) ? $this->language->get('text_edit_banned') : $this->language->get('text_add_banned');

    $data['default_language_id'] = $this->config->get('config_language_id');
    $data['_name']               = $this->_name;
    $data['token']               = $this->_session_token;

    $data['banned_id'] = $banned_id = (isset($this->request->get['banned_id'])) ? $this->request->get['banned_id'] : 0;

    $banned_info = ((isset($this->request->get['banned_id']) && $this->request->get['banned_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) ? $this->{'model_extension_ocdevwizard_'.$this->_name}->getBanned($this->request->get['banned_id']) : [];

    $data['status'] = ($banned_info) ? $banned_info['status'] : '1';
    $data['ip']     = ($banned_info) ? $banned_info['ip'] : '';

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/banned_index',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/banned_index.tpl';

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/banned_index.tpl',$data));
    }
  }

  public function banned_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $models = [
        'extension/ocdevwizard/'.$this->_name,
        'extension/ocdevwizard/helper'
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

        $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

        $response_errors = $this->send_curl($this->request->post,'validate_data',$license_key,0,'banned');

        if ($response_errors['status'] == 200 && !empty($response_errors['response'])) {
          foreach ($response_errors['response'] as $key => $error) {
            $json['error'][$key] = $this->language->get($error);
          }
        }

        if (!isset($json['error'])) {
          if (isset($this->request->post['banned_id']) && $this->request->post['banned_id']) {
            $this->send_curl($this->request->post,'banned',$license_key,0,'edit');

            $json['success'] = $this->language->get('text_success_banned_edit');
          } else {
            $this->send_curl($this->request->post,'banned',$license_key,0,'add');

            $json['success'] = $this->language->get('text_success_banned_add');
          }
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function banned_list() {
    $data = [];

    $models = [
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data          = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));
    $page          = (isset($this->request->get['page'])) ? $this->request->get['page'] : 1;
    $sort          = (isset($this->request->get['sort'])) ? $this->request->get['sort'] : 'b.date_added';
    $order         = (isset($this->request->get['order'])) ? $this->request->get['order'] : 'DESC';
    $limit         = (isset($this->request->get['limit'])) ? $this->request->get['limit'] : 10;
    $data['_name'] = $this->_name;
    $data['token'] = $this->_session_token;

    $data['histories'] = [];

    $filter_data = [
      'filter_ip'            => (isset($this->request->get['filter_ip'])) ? trim($this->request->get['filter_ip']) : '',
      'filter_date_added'    => (isset($this->request->get['filter_date_added'])) ? $this->request->get['filter_date_added'] : '',
      'filter_date_modified' => (isset($this->request->get['filter_date_modified'])) ? $this->request->get['filter_date_modified'] : '',
      'filter_status'        => (isset($this->request->get['filter_status'])) ? $this->request->get['filter_status'] : '*',
      'start'                => ($page - 1) * $limit,
      'limit'                => $limit,
      'sort'                 => $sort,
      'order'                => $order
    ];

    $results = $this->{'model_extension_ocdevwizard_'.$this->_name}->getBanneds($filter_data);

    foreach ($results as $result) {
      $data['histories'][] = [
        'banned_id'     => $result['banned_id'],
        'ip'            => $result['ip'],
        'date_added'    => $result['date_added'],
        'date_modified' => ($result['date_modified'] != '0000-00-00 00:00:00') ? $result['date_modified'] : $this->language->get('text_not_changed'),
        'status'        => $result['status'] ? $this->language->get('text_status_enabled') : $this->language->get('text_status_disabled')
      ];
    }

    $history_total = $this->{'model_extension_ocdevwizard_'.$this->_name}->getTotalBanneds($filter_data);

    $filter_data = [
      'total' => $history_total,
      'page'  => $page,
      'limit' => $limit,
      'token' => $this->_session_token
    ];

    $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

    $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

    $response_info = $this->send_curl($filter_data,'pagination',$license_key,0,'banned');

    $data['pagination'] = ($response_info['status'] == 200 && !empty($response_info['response'])) ? $response_info['response'] : '';

    $data['results'] = sprintf($this->language->get('text_pagination'),($history_total) ? (($page - 1) * $limit) + 1 : 0,((($page - 1) * $limit) > ($history_total - $limit)) ? $history_total : ((($page - 1) * $limit) + $limit),$history_total,ceil($history_total / $limit));

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/banned_list',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/banned_list.tpl';

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/banned_list.tpl',$data));
    }
  }

  public function record_index() {
    $data = [];

    $models = [
      'tool/image',
      'tool/upload',
      'extension/ocdevwizard/'.$this->_name
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));

    $data['text_modal_heading'] = $this->language->get('text_info_record');

    $data['default_language_id'] = $this->config->get('config_language_id');
    $data['_name']               = $this->_name;
    $data['token']               = $this->_session_token;

    $data['record_id'] = $record_id = (isset($this->request->get['record_id'])) ? $this->request->get['record_id'] : 0;

    $record_info = ((isset($this->request->get['record_id']) && $this->request->get['record_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) ? $this->{'model_extension_ocdevwizard_'.$this->_name}->getRecord($this->request->get['record_id']) : [];

    $data['ip'] = ($record_info) ? $record_info['ip'] : '';

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/record_index',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/record_index.tpl';

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/record_index.tpl',$data));
    }
  }

  public function record_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $models = [
        'extension/ocdevwizard/'.$this->_name,
        'extension/ocdevwizard/helper'
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

        $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

        $response_errors = $this->send_curl($this->request->post,'validate_data',$license_key,0,'record');

        if ($response_errors['status'] == 200 && !empty($response_errors['response'])) {
          foreach ($response_errors['response'] as $key => $error) {
            $json['error'][$key] = $this->language->get($error);
          }
        }

        if (!isset($json['error'])) {
          $this->send_curl($this->request->post,'record',$license_key,0,'add');

          $json['success'] = $this->language->get('text_success_record_send');
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function record_list() {
    $data = [];

    $models = [
      'tool/image',
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data          = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));
    $page          = (isset($this->request->get['page'])) ? $this->request->get['page'] : 1;
    $sort          = (isset($this->request->get['sort'])) ? $this->request->get['sort'] : 'r.date_added';
    $order         = (isset($this->request->get['order'])) ? $this->request->get['order'] : 'DESC';
    $limit         = (isset($this->request->get['limit'])) ? $this->request->get['limit'] : 10;
    $data['_name'] = $this->_name;
    $data['token'] = $this->_session_token;

    $data['histories'] = [];

    $filter_data = [
      'filter_ip'         => (isset($this->request->get['filter_ip'])) ? trim($this->request->get['filter_ip']) : '',
      'filter_date_added' => (isset($this->request->get['filter_date_added'])) ? $this->request->get['filter_date_added'] : '',
      'start'             => ($page - 1) * $limit,
      'limit'             => $limit,
      'sort'              => $sort,
      'order'             => $order
    ];

    $results = $this->{'model_extension_ocdevwizard_'.$this->_name}->getRecords($filter_data);

    foreach ($results as $result) {
      $data['histories'][] = [
        'record_id'     => $result['record_id'],
        'banned_status' => $result['banned_status'],
        'ip'            => $result['ip'],
        'date_added'    => $result['date_added']
      ];
    }

    $history_total = $this->{'model_extension_ocdevwizard_'.$this->_name}->getTotalRecords($filter_data);

    $filter_data = [
      'total' => $history_total,
      'page'  => $page,
      'limit' => $limit,
      'token' => $this->_session_token
    ];

    $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

    $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

    $response_info = $this->send_curl($filter_data,'pagination',$license_key,0,'record');

    $data['pagination'] = ($response_info['status'] == 200 && !empty($response_info['response'])) ? $response_info['response'] : '';

    $data['results'] = sprintf($this->language->get('text_pagination'),($history_total) ? (($page - 1) * $limit) + 1 : 0,((($page - 1) * $limit) > ($history_total - $limit)) ? $history_total : ((($page - 1) * $limit) + $limit),$history_total,ceil($history_total / $limit));

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/record_list',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/record_list.tpl';

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/record_list.tpl',$data));
    }
  }

  public function page_index() {
    $data = [];

    $models = [
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $form_data = (array)$this->model_extension_ocdevwizard_helper->getSettingData($this->_name.'_form_data',(int)$this->config->get('config_store_id'));
    $text_data = (array)$this->model_extension_ocdevwizard_helper->getSettingData($this->_name.'_text_data',(int)$this->config->get('config_store_id'));

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name),$text_data,$form_data);

    $data['_name']            = $this->_name;
    $data['_code']            = $this->_code;
    $data['config_store_url'] = $config_store_url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;

    $language_id = $this->{'model_extension_ocdevwizard_'.$this->_name}->getLanguageIdByCode($this->config->get('config_admin_language'));

    if ($form_data['access_type'] == 1) {
      $scripts = [
        'view/javascript/ocdevwizard/'.$this->_name.'/patternlock/patternlock.min.js'
      ];

      foreach ($scripts as $script) {
        if ($script) {
          $this->document->addScript($script);
        }
      }
    }

    if ($form_data['access_type'] == 1) {
      $styles = [
        'view/stylesheet/ocdevwizard/'.$this->_name.'/stylesheet.css',
        'view/javascript/ocdevwizard/'.$this->_name.'/patternlock/patternlock.min.css'
      ];

      if (isset($form_data['direction_type'][$language_id]) && $form_data['direction_type'][$language_id] == '2') {
        $styles[] = 'view/stylesheet/ocdevwizard/'.$this->_name.'/stylesheet_rtl.css';
      }

      foreach ($styles as $style) {
        if ($style) {
          $this->document->addStyle($style);
        }
      }
    }

    if (isset($text_data[$language_id])) {
      $this->document->setTitle((isset($text_data[$language_id])) ? html_entity_decode($text_data[$language_id]['meta_title'],ENT_QUOTES,'UTF-8') : '');
      $data['heading_title'] = (isset($text_data[$language_id])) ? html_entity_decode($text_data[$language_id]['name'],ENT_QUOTES,'UTF-8') : '';
      $data['title']         = (isset($text_data[$language_id])) ? html_entity_decode($text_data[$language_id]['meta_title'],ENT_QUOTES,'UTF-8') : '';
      $data['description']   = (isset($text_data[$language_id])) ? html_entity_decode($text_data[$language_id]['description'],ENT_QUOTES,'UTF-8') : '';
    }

    $data['styles']  = $this->document->getStyles();
    $data['scripts'] = $this->document->getScripts();
    $data['lang']    = $this->language->get('code');

    $data['direction'] = (isset($form_data['direction_type'][$language_id]) && $form_data['direction_type'][$language_id] == '2') ? 'rtl' : 'ltr';

    $data['action'] = $this->url->link('extension/ocdevwizard/'.$this->_name.'/page_index','',$this->_ssl_code);

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->page_action()) {
      if (version_compare(VERSION,'2.0.0.0','<')) {
        $this->redirect($this->url->link('common/login','pattern='.$this->request->post['pattern'],$this->_ssl_code));
      } else {
        if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'],HTTP_SERVER) === 0 || strpos($this->request->post['redirect'],HTTPS_SERVER) === 0)) {
          $this->response->redirect($this->request->post['redirect']);
        } else {
          $this->response->redirect($this->url->link('common/login','pattern='.$this->request->post['pattern'],$this->_ssl_code));
        }
      }
    }

    $data['error_pattern_code']     = (isset($this->error['pattern_code']) && !isset($this->error['pattern_attempts'])) ? $this->error['pattern_code'] : '';
    $data['error_pattern_attempts'] = (isset($this->error['pattern_attempts'])) ? $this->error['pattern_attempts'] : '';
    $data['pattern']                = (isset($this->request->post['pattern'])) ? $this->request->post['pattern'] : '';

    if (isset($this->request->get['route'])) {
      $route = $this->request->get['route'];

      unset($this->request->get['route']);
      unset($this->request->get['token']);

      $url = '';

      if ($this->request->get) {
        $url .= http_build_query($this->request->get);
      }

      $data['redirect'] = $this->url->link($route,$url,$this->_ssl_code);
    } else {
      $data['redirect'] = '';
    }

    $data['ip']                = $ip = (!empty($this->request->server['REMOTE_ADDR'])) ? $this->request->server['REMOTE_ADDR'] : '';
    $data['panel_style_color'] = $this->hex2rgba($form_data['panel_style_color'],$form_data['background_opacity']);

    $record_info = $this->{'model_extension_ocdevwizard_'.$this->_name}->getRecordByIp($ip);

    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      if ($record_info) {
        $filter_data = [
          'ip'        => $ip,
          'type'      => 'guest',
          'form_data' => $form_data
        ];

        $this->{'model_extension_ocdevwizard_'.$this->_name}->updateRecord($filter_data);
      } else {
        if (version_compare(VERSION,'2.0.3.1','<=')) {
          $salt = substr(md5(uniqid(rand(),true)),0,9);
        } else {
          $salt = token(9);
        }

        $token = md5(sha1($salt.sha1($salt.sha1($ip))));

        if (version_compare(VERSION,'2.0.3.1','<=')) {
          $salt = substr(md5(uniqid(rand(),true)),0,9);
        } else {
          $salt = token(9);
        }

        $token .= md5(sha1($salt.sha1($salt.sha1($ip))));

        $filter_data = [
          'ip'        => $ip,
          'form_data' => $form_data,
          'token'     => $token
        ];

        $this->{'model_extension_ocdevwizard_'.$this->_name}->addRecord($filter_data);
      }
    }

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/page_index',$data));
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/page_index_for_oc1.tpl';

      $this->response->setOutput($this->render());
    } else {
      $this->response->setOutput($this->load->view('extension/ocdevwizard/'.$this->_name.'/page_index.tpl',$data));
    }
  }

  private function page_action() {
    $models = [
      'extension/ocdevwizard/'.$this->_name,
      'extension/ocdevwizard/helper'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $form_data = (array)$this->model_extension_ocdevwizard_helper->getSettingData($this->_name.'_form_data',(int)$this->config->get('config_store_id'));
    $text_data = (array)$this->model_extension_ocdevwizard_helper->getSettingData($this->_name.'_text_data',(int)$this->config->get('config_store_id'));

    $language_id = $this->{'model_extension_ocdevwizard_'.$this->_name}->getLanguageIdByCode($this->config->get('config_admin_language'));

    $record_info = $this->{'model_extension_ocdevwizard_'.$this->_name}->getRecordByIp($this->request->server['REMOTE_ADDR']);

    if (isset($this->request->post['pattern']) && ($this->request->post['pattern'] != $form_data['pattern_code'])) {
      if (isset($text_data[$language_id])) {
        $find = [
          '{count_attempt_left}'
        ];

        $replace = [
          ($form_data['access_attempts'] - (($record_info) ? $record_info['total'] : 0))
        ];

        $this->error['pattern_code'] = html_entity_decode(str_replace($find,$replace,$text_data[$language_id]['pattern']),ENT_QUOTES,'UTF-8');
      }
    }

    if ($record_info && ($record_info['total'] >= $form_data['access_attempts']) && strtotime('-1 hour') < strtotime($record_info['date_modified'])) {
      if (isset($text_data[$language_id])) {
        $this->error['pattern_attempts'] = html_entity_decode($text_data[$language_id]['pattern_attempt'],ENT_QUOTES,'UTF-8');
      }
    }

    return !$this->error;
  }

  private function hex2rgba($color,$opacity = 0) {
    if ($color[0] == '#') {
      $color = substr($color,1);
    }

    if (strlen($color) == 6) {
      $hex = [$color[0].$color[1],$color[2].$color[3],$color[4].$color[5]];
    } else if (strlen($color) == 3) {
      $hex = [$color[0].$color[0],$color[1].$color[1],$color[2].$color[2]];
    } else {
      return 'rgb(0,0,0)';
    }

    $rgb = array_map('hexdec',$hex);

    if ($opacity) {
      if (abs($opacity) > 1) {
        $opacity = 1.0;
      }

      $result = 'rgba('.implode(",",$rgb).','.$opacity.')';
    } else {
      $result = 'rgb('.implode(",",$rgb).')';
    }

    return $result;
  }

  public function record_banned_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $models = [
        'extension/ocdevwizard/'.$this->_name,
        'extension/ocdevwizard/helper'
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

      $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

      if (isset($this->request->get['record_id'])) {
        $record_info = $this->{'model_extension_ocdevwizard_'.$this->_name}->getRecord($this->request->get['record_id']);

        if ($record_info) {
          $post_data = [];

          $post_data['ip'] = $record_info['ip'];

          $banneds = $this->{'model_extension_ocdevwizard_'.$this->_name}->getBannedByIp($record_info['ip']);

          if ($banneds) {
            foreach ($banneds as $banned) {
              $post_data['banned_id'] = $banned['banned_id'];

              if (isset($this->request->get['type']) && $this->request->get['type'] == 'remove') {
                $post_data['status'] = 0;

                $this->send_curl($post_data,'banned',$license_key,0,'edit');

                $json['success'] = $this->language->get('text_success_banned_edit');
              }

              if (isset($this->request->get['type']) && $this->request->get['type'] == 'add') {
                $post_data['status'] = 1;

                $this->send_curl($post_data,'banned',$license_key,0,'edit');

                $json['success'] = $this->language->get('text_success_banned_edit');
              }
            }
          } else {
            $post_data['status'] = 1;

            $this->send_curl($post_data,'banned',$license_key,0,'add');

            $json['success'] = $this->language->get('text_success_banned_add');
          }
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function save_css_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      if (isset($this->request->post['code']) && !empty($this->request->post['code']) && isset($this->request->post['stylesheet']) && !empty($this->request->post['stylesheet'])) {
        if (is_file(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/'.$this->request->post['stylesheet'].'.css')) {
          file_put_contents(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/'.$this->request->post['stylesheet'].'.css',str_replace([
            "&amp;gt;",
            "&gt;",
            "&quot;"
          ],[">",">","\""],$this->request->post['code']));

          $json['success'] = $this->language->get('text_success_css_saved');
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function restore_css_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      if (is_file(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/'.$this->request->post['stylesheet'].'.css') && is_file(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/'.$this->request->post['stylesheet_default'].'.css')) {
        if (isset($this->request->post['stylesheet']) && !empty($this->request->post['stylesheet']) && isset($this->request->post['stylesheet_default']) && !empty($this->request->post['stylesheet_default'])) {
          $stylesheet_data = file_get_contents(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/'.$this->request->post['stylesheet_default'].'.css');
          file_put_contents(DIR_APPLICATION.'view/stylesheet/ocdevwizard/'.$this->_name.'/'.$this->request->post['stylesheet'].'.css',str_replace([
            "&amp;gt;",
            "&gt;",
            "&quot;"
          ],[">",">","\""],$stylesheet_data));

          $json['success'] = $this->language->get('text_success_css_restored');
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function delete_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $models = [
        'extension/ocdevwizard/'.$this->_name,
        'extension/ocdevwizard/helper'
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $type  = (isset($this->request->get['type']) && $this->request->get['type']) ? $this->request->get['type'] : '';
      $group = (isset($this->request->get['group']) && $this->request->get['group']) ? $this->request->get['group'] : '';

      if ($type && $group) {
        $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

        $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

        if ($group == 'all') {
          $result = $this->send_curl([],$type,$license_key,0,'delete_all');
        }

        if ($group == 'all_selected') {
          if (isset($this->request->request['selected'])) {
            foreach ($this->request->request['selected'] as $selected_id) {
              if (class_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst) && method_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst,'get'.$this->uc_first($type))) {
                $info = $this->{'model_extension_ocdevwizard_'.$this->_name}->{'get'.$this->uc_first($type)}((int)$selected_id);

                if ($info) {
                  $result = $this->send_curl([($type == 'email_template' ? 'template' : $type).'_id' => (int)$selected_id],$type,$license_key,0,'delete');
                }
              }
            }
          }
        }

        if ($group == 'selected') {
          if (class_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst) && method_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst,'get'.$this->uc_first($type))) {
            $info = $this->{'model_extension_ocdevwizard_'.$this->_name}->{'get'.$this->uc_first($type)}((int)$this->request->get['delete']);

            if ($info) {
              $result = $this->send_curl([($type == 'email_template' ? 'template' : $type).'_id' => (int)$this->request->get['delete']],$type,$license_key,0,'delete');
            }
          }
        }

        if (!isset($result) || !$result) {
          $json['error']['task'] = $this->language->get('error_task');
        } else {
          $json['success'] = $this->language->get('text_success_task');
        }
      } else {
        $json['error']['task'] = $this->language->get('error_task');
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function copy_action() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $models = [
        'extension/ocdevwizard/'.$this->_name,
        'extension/ocdevwizard/helper'
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $type  = (isset($this->request->get['type']) && $this->request->get['type']) ? $this->request->get['type'] : '';
      $group = (isset($this->request->get['group']) && $this->request->get['group']) ? $this->request->get['group'] : '';

      if ($type && $group) {
        $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

        $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

        if ($group == 'all') {
          $result = $this->send_curl([],$type,$license_key,0,'copy_all');
        }

        if ($group == 'all_selected') {
          if (isset($this->request->request['selected'])) {
            foreach ($this->request->request['selected'] as $selected_id) {
              if (class_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst) && method_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst,'get'.$this->uc_first($type))) {
                $info = $this->{'model_extension_ocdevwizard_'.$this->_name}->{'get'.$this->uc_first($type)}((int)$selected_id);

                if ($info) {
                  $result = $this->send_curl([($type == 'email_template' ? 'template' : $type).'_id' => (int)$selected_id],$type,$license_key,0,'copy');
                }
              }
            }
          }
        }

        if ($group == 'selected') {
          if (class_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst) && method_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst,'get'.$this->uc_first($type))) {
            $info = $this->{'model_extension_ocdevwizard_'.$this->_name}->{'get'.$this->uc_first($type)}((int)$this->request->get['copy']);

            if ($info) {
              $result = $this->send_curl([($type == 'email_template' ? 'template' : $type).'_id' => (int)$this->request->get['copy']],$type,$license_key,0,'copy');
            }
          }
        }

        if (!isset($result) || !$result) {
          $json['error']['task'] = $this->language->get('error_task');
        } else {
          $json['success'] = $this->language->get('text_success_task');
        }
      } else {
        $json['error']['task'] = $this->language->get('error_task');
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function get_backup_files($type) {
    $files = [];

    if ($type && is_dir(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/'.$type)) {
      $dir = opendir(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/'.$type);

      while (($file = readdir($dir)) !== false) {
        if (in_array(substr(strrchr($file,'.'),1),['json'])) {
          $files[] = (DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/'.$type.'/'.$file);
        }
      }

      closedir($dir);
    }

    return $files;
  }

  public function export_settings() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;
    $type     = (isset($this->request->get['type']) && $this->request->get['type']) ? $this->request->get['type'] : '';

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $json['redirect'] = 'index.php?route=extension/ocdevwizard/'.$this->_name.'/export_settings_result&'.$this->_session_token.'&store_id='.$store_id.'&type='.$type;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function export_settings_result() {
    if ($this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $this->language->load('extension/ocdevwizard/'.$this->_name);

      $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;
      $type     = (isset($this->request->get['type']) && $this->request->get['type']) ? $this->request->get['type'] : '';

      if ($type) {
        $models = [
          'extension/ocdevwizard/'.$this->_name,
          'extension/ocdevwizard/helper'
        ];

        foreach ($models as $model) {
          $this->load->model($model);
        }

        $settings = '';

        if ($type == 'config') {
          $settings = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,$store_id);
        } else {
          $function_name = '';

          $function_name_array = explode('_',$type);

          if ($function_name_array) {
            foreach ($function_name_array as $name) {
              $function_name .= $this->uc_first($name);
            }
          }

          if ($function_name) {
            if (class_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst) && method_exists('ModelExtensionOcdevwizard'.$this->_name_ucfirst,'getExport'.$function_name.'s')) {
              $settings = $this->{'model_extension_ocdevwizard_'.$this->_name}->{'getExport'.$function_name.'s'}();
            }
          }
        }

        $this->response->addHeader('Pragma: public');
        $this->response->addHeader('Expires: 0');
        $this->response->addHeader('Content-Description: File Transfer');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Content-Disposition: attachment; filename='.$this->_name.'_'.$type.'_'.date("Y-m-d H:i:s",time()).'_'.$store_id.'.json');
        $this->response->addHeader('Content-Transfer-Encoding: binary');

        if (!empty($settings)) {
          if (is_dir(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/'.$type)) {
            file_put_contents(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/'.$type.'/'.date("Y-m-d_H-i-s",time()).'_'.$store_id.'.json',json_encode($settings));
          }
        }

        $this->response->setOutput(json_encode($settings));
      }
    }
  }

  public function import_settings() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!$this->user->hasPermission('modify','extension/ocdevwizard/'.$this->_name)) {
      $json['error']['warning']['permission'] = $this->language->get('error_permission');
    }

    if (!isset($json['error'])) {
      $store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;
      $type     = (isset($this->request->get['type']) && $this->request->get['type']) ? $this->request->get['type'] : '';

      if ($type) {
        if (isset($this->request->post['file_name']) && !empty($this->request->post['file_name'])) {
          $models = ['extension/ocdevwizard/helper'];

          foreach ($models as $model) {
            $this->load->model($model);
          }

          if (is_file(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/'.$type.'/'.$this->request->post['file_name'])) {
            $content = file_get_contents(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/'.$type.'/'.$this->request->post['file_name']);

            $setting_contents = json_decode($content,true);

            if ($setting_contents) {
              $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,$store_id);

              $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

              if ($type == 'config') {
                $this->send_curl($setting_contents,'edit_setting',$license_key,$store_id,'import');
              } else {
                $this->send_curl([],$type,$license_key,$store_id,'prepare');

                foreach ($setting_contents as $setting_content) {
                  $this->send_curl($setting_content,$type,$license_key,$store_id,'import');
                }
              }

              $this->cache(true);

              $json['success'] = $this->language->get('text_success_'.$type.'_restored');
            } else {
              $json['error']['task'] = $this->language->get('error_task');
            }
          } else {
            $json['error']['task'] = $this->language->get('error_task');
          }
        } else {
          if (isset($this->request->get['source']) && $this->request->get['source'] == 'from_user') {
            $models = ['extension/ocdevwizard/helper'];

            foreach ($models as $model) {
              $this->load->model($model);
            }

            $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,$store_id);

            $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

            $content = '';

            if (isset($this->request->files[$type.'_import']) && is_uploaded_file($this->request->files[$type.'_import']['tmp_name'])) {
              $content = file_get_contents($this->request->files[$type.'_import']['tmp_name']);
            }

            $response_info = $this->send_curl($this->request->post,'base',$license_key);

            $this->cache(true);

            if ($content) {
              if ($response_info['status'] == 200 && !empty($response_info['response'])) {
                $setting_contents = json_decode($content,true);

                if ($type == 'config') {
                  if ($setting_contents) {
                    $this->send_curl($setting_contents,'edit_setting',$license_key,$store_id);
                  }

                  $json['success'] = $this->language->get('text_success_config_restored');
                } else {
                  if ($setting_contents) {
                    $this->send_curl([],$type,$license_key,$store_id,'prepare');

                    foreach ($setting_contents as $setting_content) {
                      $this->send_curl($setting_content,$type,$license_key,$store_id,'import');
                    }
                  }

                  $json['success'] = $this->language->get('text_success_'.$type.'_restored');
                }
              }
            }
          }
        }
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function autocomplete_email_template() {
    $json = [];

    if (isset($this->request->request['filter_name'])) {
      $models = ['extension/ocdevwizard/'.$this->_name];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $filter_data = [
        'filter_name' => $this->request->request['filter_name'],
        'start'       => 0,
        'limit'       => 10
      ];

      $results = $this->{'model_extension_ocdevwizard_'.$this->_name}->getEmailTemplates($filter_data);

      foreach ($results as $result) {
        $json[] = [
          'name'        => strip_tags(html_entity_decode($result['system_name'],ENT_QUOTES,'UTF-8')),
          'template_id' => $result['template_id']
        ];
      }

      $json = $this->mu_array($json,'name');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function autocomplete_record() {
    $json = [];

    if (isset($this->request->request['filter_ip'])) {
      $models = ['extension/ocdevwizard/'.$this->_name];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $filter_data = [
        'filter_ip' => $this->request->request['filter_ip'],
        'start'     => 0,
        'limit'     => 10
      ];

      $results = $this->{'model_extension_ocdevwizard_'.$this->_name}->getRecords($filter_data);

      foreach ($results as $result) {
        $json[] = [
          'ip'        => $result['ip'],
          'record_id' => $result['record_id']
        ];
      }

      $json = $this->mu_array($json,'ip');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function autocomplete_banned() {
    $json = [];

    if (isset($this->request->request['filter_ip'])) {
      $models = ['extension/ocdevwizard/'.$this->_name];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $filter_data = [
        'filter_ip' => $this->request->request['filter_ip'],
        'start'     => 0,
        'limit'     => 10
      ];

      $results = $this->{'model_extension_ocdevwizard_'.$this->_name}->getBanneds($filter_data);

      foreach ($results as $result) {
        $json[] = [
          'ip'        => $result['ip'],
          'banned_id' => $result['banned_id']
        ];
      }

      $json = $this->mu_array($json,'ip');
    }

    if (isset($this->request->request['filter_email'])) {
      $models = ['extension/ocdevwizard/'.$this->_name];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $filter_data = [
        'filter_email' => $this->request->request['filter_email'],
        'start'        => 0,
        'limit'        => 10
      ];

      $results = $this->{'model_extension_ocdevwizard_'.$this->_name}->getBanneds($filter_data);

      foreach ($results as $result) {
        $json[] = [
          'email'     => strip_tags(html_entity_decode($result['email'],ENT_QUOTES,'UTF-8')),
          'banned_id' => $result['banned_id']
        ];
      }

      $json = $this->mu_array($json,'email');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function generate_password() {
    $json = [];

    $this->language->load('extension/ocdevwizard/'.$this->_name);

    if (!isset($json['error'])) {
      $models = [
        'extension/ocdevwizard/'.$this->_name,
        'extension/ocdevwizard/helper'
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

      $config_store_url = $this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER;

      $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

      $response_info = $this->send_curl([],'generate_password',$license_key,0);

      $json['password'] = ($response_info['status'] == 200 && !empty($response_info['response'])) ? $response_info['response']['password'] : '';

      $secret_key = (isset($this->request->get['secret_key']) && $this->request->get['secret_key']) ? $this->request->get['secret_key'] : '';

      if ($response_info['status'] == 200 && !empty($response_info['response'])) {
        $json['technical_url_bacup'] = $config_store_url.'?'.$secret_key.'='.$response_info['response']['password'];
      } else {
        $json['technical_url_bacup'] = '';
      }

      $json['success'] = $this->language->get('text_success_generate_password');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function widget() {
    $data = [];

    $models = ['extension/ocdevwizard/'.$this->_name];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $data = array_merge($data,$this->language->load('extension/ocdevwizard/'.$this->_name));

    $data['results'] = [];

    $total_0 = $this->{'model_extension_ocdevwizard_'.$this->_name}->getTotalAttempts();

    $data['results'][] = [
      'name'    => $this->language->get('text_records'),
      'total_0' => (int)$total_0,
      'href'    => $this->url->link('extension/ocdevwizard/'.$this->_name,$this->_session_token,$this->_ssl_code)
    ];

    $total_1 = $this->{'model_extension_ocdevwizard_'.$this->_name}->getTotalBanneds(['filter_status' => 1]);

    $data['results'][] = [
      'name'    => $this->language->get('text_records_banned'),
      'total_0' => (int)$total_1,
      'href'    => $this->url->link('extension/ocdevwizard/'.$this->_name,$this->_session_token,$this->_ssl_code)
    ];

    $data['total_0'] = (int)$total_0;
    $data['total_1'] = (int)$total_1;

    $data['link'] = $this->url->link('extension/ocdevwizard/'.$this->_name,$this->_session_token,$this->_ssl_code);

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      return $this->load->view('extension/ocdevwizard/'.$this->_name.'/widget',$data);
    } else if (version_compare(VERSION,'2.0.0.0','<')) {
      $this->data = $data;

      $this->template = 'extension/ocdevwizard/'.$this->_name.'/widget_for_oc1.tpl';

      $this->render();
    } else {
      return $this->load->view('extension/ocdevwizard/'.$this->_name.'/widget.tpl',$data);
    }
  }

  private function curl_progress_callback($resource,$downloadSize,$downloaded,$uploadSize,$uploaded) {
    if ($downloaded > 1024 * 1024 * 5) {
      return -1;
    }
  }

  private function get_background() {
    $backgrounds = [];

    if (is_dir(DIR_IMAGE.'catalog/ocdevwizard/'.$this->_name.'/background')) {
      $dir = opendir(DIR_IMAGE.'catalog/ocdevwizard/'.$this->_name.'/background');

      while (($file = readdir($dir)) !== false) {
        if (in_array(substr(strrchr($file,'.'),1),['png','jpg'])) {
          $backgrounds[] = (HTTP_CATALOG.'image/catalog/ocdevwizard/'.$this->_name.'/background/'.$file);
        }
      }

      closedir($dir);
    }

    return $backgrounds;
  }

  private function mu_array($array,$key) {
    $models = ['extension/ocdevwizard/helper'];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

    $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

    $response_info = $this->send_curl([
      'array' => $array,
      'key'   => $key
    ],'mu_array',$license_key,0);

    return ($response_info['status'] == 200 && !empty($response_info['response'])) ? $response_info['response'] : [];
  }

  private function uc_first($string) {
    $models = ['extension/ocdevwizard/helper'];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $setting_info = $this->model_extension_ocdevwizard_helper->getSetting($this->_name,0);

    $license_key = (isset($setting_info[$this->_name.'_license'])) ? $setting_info[$this->_name.'_license'] : '';

    $response_info = $this->send_curl([
      'string' => $string
    ],'uc_first',$license_key,0);

    return ($response_info['status'] == 200 && !empty($response_info['response'])) ? $response_info['response']['string'] : '';
  }

  private function day_formatting($n,$form1,$form2) {
    $n  = abs($n) % 100;
    $n1 = $n % 10;

    if ($n > 10 && $n < 20) {
      return $form2;
    }

    if ($n1 == 1) {
      return $form1;
    }

    return $form2;
  }

  private function check_remote_file() {
    $file         = 'http://api.ocdevwizard.com/License/'.$this->_code.'/index.html';
    $file_headers = @get_headers($file);

    if (isset($file_headers[0]) && strpos($file_headers[0],'200 OK')) {
      return true;
    } else {
      return false;
    }
  }

  private function send_curl($data,$type,$license_key,$store_id = 0,$sub_type = '') {
    $data['referer']          = (string)($this->config->get('config_secure')) ? HTTPS_CATALOG : HTTP_CATALOG;
    $data['request_type']     = (string)$type;
    $data['sub_type']         = (string)$sub_type;
    $data['version']          = (string)$this->_version;
    $data['opencart_version'] = VERSION;
    $data['access_token']     = (string)$access_token = md5(time().rand());
    $data['store_id']         = (int)$store_id;
    $data['remote_addr']      = $this->request->server['REMOTE_ADDR'];

    if ($type != 'validate_access' && $type != 'validate_data' && $type != 'base' && $type != 'mu_array' && $type != 'uc_first' && $type != 'generate_password' && $type != 'pagination' && $type != 'form_data' && $type != 'text_data') {
      file_put_contents(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/access.ocdw',$access_token);
    }

    if ($type == 'default' || $type == 'restore') {
      $this->language->load('extension/ocdevwizard/'.$this->_name);

      $models = [
        'localisation/language'
      ];

      foreach ($models as $model) {
        $this->load->model($model);
      }

      $languages = $this->model_localisation_language->getLanguages();

      $data['default_language_data'] = [];

      foreach ($languages as $language) {
        $data['default_language_data'][$language['language_id']] = [
          'name'            => $this->language->get('default_name'),
          'meta_title'      => $this->language->get('default_meta_title'),
          'pattern'         => $this->language->get('default_pattern'),
          'pattern_attempt' => $this->language->get('default_pattern_attempt'),
          'description'     => $this->language->get('default_description')
        ];
      }

      foreach ($languages as $language) {
        $data['direction_type'][$language['language_id']] = 1;
      }

      $data['admin_email']   = (string)$this->config->get('config_email');
      $data['heading_title'] = (string)$this->language->get('heading_title');
    }

    $curl = curl_init();

    curl_setopt($curl,CURLOPT_URL,'http://api.ocdevwizard.com/License/'.$this->_code.'/?pk=1aJ87G2AtY&lc='.$license_key);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl,CURLOPT_HEADER,0);
    curl_setopt($curl,CURLOPT_POST,1);
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($data));

    $response_data = curl_exec($curl);
    $httpcode_data = curl_getinfo($curl,CURLINFO_HTTP_CODE);

    curl_close($curl);

    $results = [
      'status'   => (int)$httpcode_data,
      'response' => json_decode($response_data,true)
    ];

    return $results;
  }
}

?>