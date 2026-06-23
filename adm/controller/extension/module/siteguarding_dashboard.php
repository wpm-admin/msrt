<?php

class ControllerExtensionModuleSiteguardingDashboard extends Controller {
    
    private $error = array();
    
    public function index()
    {
        
        $this->load->language('extension/module/siteguarding_dashboard');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $data['sg_tools'] = $this->check_sg_tools();
        $data['sg_conf'] = $this->check_sg_conf();
        $data['autologin_id'] = $this->get_autologin_id();
        
        $this->response->setOutput($this->load->view('extension/module/siteguarding_dashboard', $data));
        
    }
    
    public function check_sg_tools() {
            
        $tools_file = substr(__DIR__, 0, strrpos(str_replace( DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'extension' . DIRECTORY_SEPARATOR . 'module' , '' , __DIR__ ), DIRECTORY_SEPARATOR) ) . DIRECTORY_SEPARATOR . 'siteguarding_tools.php';
        
        if (is_file($tools_file)) {
            chmod($tools_file, 0644);
            return true;
        } else {
            return false;
        }
    }
    
    public function check_sg_conf(){
        
        $webanalyze = substr(__DIR__, 0, strrpos(str_replace( DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'extension' . DIRECTORY_SEPARATOR . 'module' , '' , __DIR__ ), DIRECTORY_SEPARATOR) ) . DIRECTORY_SEPARATOR . 'webanalyze';
        
        $conf_file = $webanalyze . DIRECTORY_SEPARATOR . 'website-security-conf.php';
        
        if (file_exists($conf_file)) {
            chmod($conf_file, 0644);
            return true;
        } else {
            if (!file_exists($webanalyze)) {
                mkdir($webanalyze);
            }
            return false;
        }
    }
    
    public function get_autologin_id(){
        
        $conf_file = substr(__DIR__, 0, strrpos(str_replace( DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'extension' . DIRECTORY_SEPARATOR . 'module' , '' , __DIR__ ), DIRECTORY_SEPARATOR) ) . DIRECTORY_SEPARATOR . 'webanalyze' . DIRECTORY_SEPARATOR . 'website-security-conf.php';
        
        if(file_exists($conf_file)){
            include_once($conf_file);
        }
    }
}