<?php
class ControllerExtensionModuleImagesCompressSqueezeimg extends Controller
{
    private $error = array();
    const IMCP_DIR_STORAGE = DIR_STORAGE;
    public function index()
    {
        $this->load->language('extension/module/images_compress_squeezeimg');

        $this->document->setTitle(strip_tags($this->language->get('heading_title')));
        $this->load->library('images_compress_squeezeimg/images');
        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            if (!file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg")){
                if(!@mkdir(self::IMCP_DIR_STORAGE."images_compress_squeezeimg", 0777, true)) {
                    $this->error['warning'] = 'Can\'t create folder '.self::IMCP_DIR_STORAGE."images_compress_squeezeimg";
                }
            }
            if (isset($this->request->post["module_images_compress_squeezeimg_qzip"]) && ($this->request->post["module_images_compress_squeezeimg_qzip"])) {
                $this->switchGzipSetting(true);
            } else {
                $this->switchGzipSetting(false);
            }
            if (isset($this->request->post["module_images_compress_squeezeimg_status"]) && ($this->request->post["module_images_compress_squeezeimg_status"])) {
                $this->switchEnabledHtaccessRules(true);
            } else {
                $this->switchEnabledHtaccessRules(false);
            }
            if (isset($this->request->post["module_images_compress_squeezeimg_webp_status"]) && ($this->request->post["module_images_compress_squeezeimg_webp_status"])) {
                $this->enbleFormat(true, 'webp');
            } else {
                $this->enbleFormat(false, 'webp');
            }
            if (isset($this->request->post["module_images_compress_squeezeimg_avif_status"]) && ($this->request->post["module_images_compress_squeezeimg_avif_status"])) {
                $this->enbleFormat(true, 'avif');
            } else {
                $this->enbleFormat(false, 'avif');
            }
            if (isset($this->request->post["module_images_compress_squeezeimg_jp2_status"]) && ($this->request->post["module_images_compress_squeezeimg_jp2_status"])) {
                $this->enbleFormat(true, 'jp2');
            } else {
                $this->enbleFormat(false, 'jp2');
            }
            if (isset($this->request->post["module_images_compress_squeezeimg_loader"])) {
                file_put_contents(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/lazy_load.image",$this->request->post["module_images_compress_squeezeimg_loader"]);
            }
            if (isset($this->request->post["module_images_compress_squeezeimg_lazy_load"]) && ($this->request->post["module_images_compress_squeezeimg_lazy_load"])) {
                if (!file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/lazy_load.enabled")) {
                    file_put_contents(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/lazy_load.enabled",1);
                }
            } else {
                if (file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/lazy_load.enabled")) {
                    unlink(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/lazy_load.enabled");
                }
            }
            $this->model_setting_setting->editSetting('module_images_compress_squeezeimg', $this->request->post);

            $data['success'] = $this->language->get('text_success');

        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        $data['block_content'] = false;
        if($this->images->hasBlock()){
            $data['error_warning'] = $this->language->get('error_limit');
            $data['block_content'] = true;

        }
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/images_compress_squeezeimg', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/images_compress_squeezeimg', 'user_token=' . $this->session->data['user_token'], true);
        $data['pageReadme'] = $this->url->link('extension/module/images_compress_squeezeimg', 'user_token=' . $this->session->data['user_token'].'&page=readme', true);
        $data['url_sitemap'] = HTTPS_CATALOG . "index.php?route=extension/module/images_compress_squeezeimg/sitemap";
        $data['url_cron'] = HTTPS_CATALOG . "index.php?route=extension/module/images_compress_squeezeimg/cron";
        $data['url_compress'] = $this->url->link('extension/module/images_compress_squeezeimg/compress', "", true);
        $data['url_delete'] = $this->url->link('extension/module/images_compress_squeezeimg/deleteImages', "", true);
        $data['user_token'] = '&user_token=' . $this->session->data['user_token'];
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_images_compress_squeezeimg_status'])) {
            $data['module_images_compress_squeezeimg_status'] = $this->request->post['module_images_compress_squeezeimg_status'];
        } else {
            $data['module_images_compress_squeezeimg_status'] = $this->config->get('module_images_compress_squeezeimg_status');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_webp_status'])) {
            $data['module_images_compress_squeezeimg_webp_status'] = $this->request->post['module_images_compress_squeezeimg_webp_status'];
        } else {
            $data['module_images_compress_squeezeimg_webp_status'] = $this->config->get('module_images_compress_squeezeimg_webp_status');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_avif_status'])) {
            $data['module_images_compress_squeezeimg_avif_status'] = $this->request->post['module_images_compress_squeezeimg_avif_status'];
        } else {
            $data['module_images_compress_squeezeimg_avif_status'] = $this->config->get('module_images_compress_squeezeimg_avif_status');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_jp2_status'])) {
            $data['module_images_compress_squeezeimg_jp2_status'] = $this->request->post['module_images_compress_squeezeimg_jp2_status'];
        } else {
            $data['module_images_compress_squeezeimg_jp2_status'] = $this->config->get('module_images_compress_squeezeimg_jp2_status');
        }

        if (isset($this->request->post['module_images_compress_squeezeimg_quality'])) {
            $data['module_images_compress_squeezeimg_quality'] = $this->request->post['module_images_compress_squeezeimg_quality'];
        } else {
            $data['module_images_compress_squeezeimg_quality'] = $this->config->get('module_images_compress_squeezeimg_quality');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_qzip'])) {
            $data['module_images_compress_squeezeimg_qzip'] = $this->request->post['module_images_compress_squeezeimg_qzip'];
        } else {
            $data['module_images_compress_squeezeimg_qzip'] = $this->config->get('module_images_compress_squeezeimg_qzip');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_count_send'])) {
            $data['module_images_compress_squeezeimg_count_send'] = $this->request->post['module_images_compress_squeezeimg_count_send'];
        } else {
            $data['module_images_compress_squeezeimg_count_send'] = $this->config->get('module_images_compress_squeezeimg_count_send');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_count_send_cron'])) {
            $data['module_images_compress_squeezeimg_count_send_cron'] = $this->request->post['module_images_compress_squeezeimg_count_send_cron'];
        } else {
            $data['module_images_compress_squeezeimg_count_send_cron'] = $this->config->get('module_images_compress_squeezeimg_count_send_cron');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_lazy_load'])) {
            $data['module_images_compress_squeezeimg_lazy_load'] = $this->request->post['module_images_compress_squeezeimg_lazy_load'];
        } else {
            $data['module_images_compress_squeezeimg_lazy_load'] = $this->config->get('module_images_compress_squeezeimg_lazy_load');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_origin_replace'])) {
            $data['module_images_compress_squeezeimg_origin_replace'] = $this->request->post['module_images_compress_squeezeimg_origin_replace'];
        } else {
            $data['module_images_compress_squeezeimg_origin_replace'] = $this->config->get('module_images_compress_squeezeimg_origin_replace');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_loader'])) {
            $data['module_images_compress_squeezeimg_loader'] = $this->request->post['module_images_compress_squeezeimg_loader'];
        } else {
            $data['module_images_compress_squeezeimg_loader'] = $this->config->get('module_images_compress_squeezeimg_loader');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_server_type'])) {
            $data['module_images_compress_squeezeimg_server_type'] = $this->request->post['module_images_compress_squeezeimg_server_type'];
        } else {
            $data['module_images_compress_squeezeimg_server_type'] = $this->config->get('module_images_compress_squeezeimg_server_type');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_cron_type'])) {
            $data['module_images_compress_squeezeimg_cron_type'] = $this->request->post['module_images_compress_squeezeimg_cron_type'];
        } else {
            $data['module_images_compress_squeezeimg_cron_type'] = $this->config->get('module_images_compress_squeezeimg_cron_type');
        }
        if (isset($this->request->post['module_images_compress_squeezeimg_token'])) {
            $data['module_images_compress_squeezeimg_token'] = $this->request->post['module_images_compress_squeezeimg_token'];
        } else {
            $data['module_images_compress_squeezeimg_token'] = $this->config->get('module_images_compress_squeezeimg_token');
        }
        if(empty($data['module_images_compress_squeezeimg_server_type'])){
            $data['module_images_compress_squeezeimg_server_type'] = $this->checkServer();
        }
        if ($data['module_images_compress_squeezeimg_server_type'] == 'nginx'){
            $data['error_apache'] =  sprintf($this->language->get('text_nginx'),$data['pageReadme']);
        } else {
            $data['error_apache'] =  '';
        }
        $data['getFolderTree'] = $this->url->link('extension/module/images_compress_squeezeimg/getFolderTree', '', true);
        $data['getCompressImg'] = $this->url->link('extension/module/images_compress_squeezeimg/getCompressImg', '', true);
        $data['compressOneImg'] = $this->url->link('extension/module/images_compress_squeezeimg/compressOneImg', '', true);
        $data['tryCompress'] = $this->url->link('extension/module/images_compress_squeezeimg/tryCompress', '', true);
        $data['cronTask'] = HTTPS_CATALOG."index.php?route=extension/module/images_compress_squeezeimg/cron";
        $data['treeFolder'] = $this->images->getTreeFolder();
        $data['loaders'] = $this->getImagesLoader();
        $data['countAllImage'] = $this->images->getAllImageOrigin(true);
        $data['countAllImageConvert']['jpg'] = $this->images->getAllImageConvert('jpg', true,$this->config->get('module_images_compress_squeezeimg_origin_replace'));
        $data['countAllImageConvert']['webp'] = $this->images->getAllImageConvert('webp', true,$this->config->get('module_images_compress_squeezeimg_origin_replace'));
        $data['countAllImageConvert']['avif'] = $this->images->getAllImageConvert('avif', true,$this->config->get('module_images_compress_squeezeimg_origin_replace'));
        $data['countAllImageConvert']['jp2'] = $this->images->getAllImageConvert('jp2', true,$this->config->get('module_images_compress_squeezeimg_origin_replace'));
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        if(isset($this->request->get['page']) && $this->request->get['page'] == 'readme'){
            if(file_exists(DIR_SYSTEM."library/images_compress_squeezeimg/README.txt")){
                $data['content_readme'] = @file_get_contents(DIR_SYSTEM."library/images_compress_squeezeimg/README.txt");
                if(!empty($data['content_readme'])){
                    $data['content_readme'] = str_replace('{{DIR_STORAGE}}',DIR_STORAGE,$data['content_readme']);
                }
            }
            $data['cancel'] = $this->url->link('extension/module/images_compress_squeezeimg', 'user_token=' . $this->session->data['user_token'], true);

            $this->response->setOutput($this->load->view('extension/module/images_compress_squeezeimg_readme', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/module/images_compress_squeezeimg', $data));
        }
    }

    public function tryCompress()
    {
        $response = [];
        $type = $this->request->post['type'];
        if( !empty($type)){
            $this->load->library('images_compress_squeezeimg/images');
            $response = $this->images->tryCompress(
                $type,
                '',
                70,
                $this->config->get('module_images_compress_squeezeimg_origin_replace'),
                $this->config->get('module_images_compress_squeezeimg_token')
            );
        } else {
            $response = [
                'error' => 'Missing params'
            ];
        }
        $this->jsonResponse($response);
    }
    private function checkServer()
    {
        $this->load->library('images_compress_squeezeimg/images');
        return   $this->images->checkServer();

    }
    public function getCompressImg()
    {
        $response = [];
        $folder = $this->request->post['folder'];
        $type = $this->request->post['type'];
        $quality = $this->request->post['quality'];
        $page = isset($this->request->post['page'])? $this->request->post['page'] : 1;
        if(!empty($folder) && !empty($type)  && !empty($type)){
                    $this->load->library('images_compress_squeezeimg/images');
            $response = $this->images->getCompressLog(
                $folder,
                $type,
                $quality,
                $page,
                100,
                $this->config->get('module_images_compress_squeezeimg_origin_replace'));
        } else {
            $response = [
                'error' => 'Missing params'
            ];
        }
        $this->jsonResponse($response);
    }
    public function compressOneImg()
    {
        $response = [];
        $filename = $this->request->post['filename'];
        $type = $this->request->post['type'];
        $quality = $this->request->post['quality'];
        if(!empty($filename) && !empty($type) && !empty($quality)){
                    $this->load->library('images_compress_squeezeimg/images');
            if(!is_null($this->config->get('module_images_compress_squeezeimg_token'))){
                $data = $this->images->getApiSendSettings($type,$this->config->get('module_images_compress_squeezeimg_token'));

                $response = $this->images->compressOneimg($filename,$type,$quality,$this->config->get('module_images_compress_squeezeimg_origin_replace'),$data);
            } else {
                $response = [
                    'error' => 'Token not found'
                ];
            }
        } else {
            $response = [
                'error' => 'Missing params'
            ];
        }
        $this->jsonResponse($response);
    }
    public function getFolderTree()
    {
        $response = [];
        $path = $this->request->post['path'];
        if(!empty($path)){
                    $this->load->library('images_compress_squeezeimg/images');
            $response = $this->images->getTreeFolder($path);
        }
        $this->jsonResponse($response);

    }
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/images_compress_squeezeimg')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if(isset($this->request->post['module_images_compress_squeezeimg_token']) ?? !empty($this->request->post['module_images_compress_squeezeimg_token'])){
            $this->load->library('images_compress_squeezeimg/images');
            $result = $this->images->getToken($this->request->post['module_images_compress_squeezeimg_token']);
            if(isset($result['status']) && $result['status']){
                $this->token = $result;
                if(isset($result['limit']) && isset($result['used'])){
                    if($result['limit'] > $result['used'] ){
                        $this->images->removeBlock();
                    }
                }
            } else {
                $this->error['warning'] = $this->language->get('error_token_not_corect');
            }

        } else {
            $this->error['warning'] = $this->language->get('error_token');
        }

        return !$this->error;
    }

    public function compress()
    {
        $request = $this->request->request;
        if(!isset($request['name']) || !isset($request['page'])){
            $response = ['error' => "Missing params"];
        } else {
            $this->load->library('images_compress_squeezeimg/images');
            if(!is_null($this->config->get('module_images_compress_squeezeimg_token'))){
                $data = $this->images->getApiSendSettings($request['name'],$this->config->get('module_images_compress_squeezeimg_token'));
                $response = $this->images->convert(
                    $request['name'],
                    $this->config->get('module_images_compress_squeezeimg_count_send'),
                    $this->config->get('module_images_compress_squeezeimg_quality'),
                    $request['page'],
                    $this->config->get('module_images_compress_squeezeimg_origin_replace'),
                    $data
                );
            } else {
                $response = [
                    'error' => 'Token not found'
                ];
            }

        }

        $this->jsonResponse($response);
    }

    public function deleteImages()
    {
        $request = $this->request->request;
        if(!isset($request['name'])){
            $response = ['error' => "Missing params"];
        } else {
            $this->load->library('images_compress_squeezeimg/images');
            $response = $this->images->deleteImages($request['name'],$this->config->get('module_images_compress_squeezeimg_origin_replace'));
        }
        $this->jsonResponse($response);
    }

    public function jsonResponse($data)
    {
        if(!$this->config->get('module_images_compress_squeezeimg_status')){
            $data = ['error' => "Plugin disable"];
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        die;
    }

    public function switchEnabledHtaccessRules($on = false)
    {
        $htaccessPath = str_replace('system/','',DIR_SYSTEM).".htaccess";

        if ($on) {
            if(file_exists($htaccessPath)) {
                $toggleText = "###IMAGE_CONVERTER_SQUEZZEIMG_HTACCESS_START###
    <IfModule mod_rewrite.c>
 <IfModule mod_env.c>
 SetEnv HTTP_MOD_REWRITE On
 </IfModule>

 RewriteEngine on
 <IfModule mod_headers.c>
 	<filesMatch \"\.(webp)$\">
 			Header set Content-Type \"image/webp\"
 	</filesMatch>
 	<filesMatch \"\.(avif)$\">
 			Header set Content-Type \"image/avif\"
 	</filesMatch>
 	<filesMatch \"\.(jp2)$\">
     			Header set Content-Type \"image/jp2\"
     </filesMatch>
     <filesMatch \"(_compress\.jpg)$\">
           Header set image-compress-squeezeimg  \"compress\"
     </filesMatch>
 </IfModule>
 RewriteCond " . self::IMCP_DIR_STORAGE . "images_compress_squeezeimg/jpg.enabled -f
 RewriteRule . - [E=FORMAT_IMG:_compress.jpg]
 RewriteCond  %{HTTP_ACCEPT}  image/webp
 RewriteCond " . self::IMCP_DIR_STORAGE . "images_compress_squeezeimg/webp.enabled -f
 RewriteRule . - [E=FORMAT_IMG:.webp]
 RewriteCond  %{HTTP_ACCEPT}  image/avif
 RewriteCond " . self::IMCP_DIR_STORAGE . "images_compress_squeezeimg/avif.enabled -f
 RewriteRule . - [E=FORMAT_IMG:.avif]
 RewriteCond %{HTTP_ACCEPT}  !image/webp
 RewriteCond " . self::IMCP_DIR_STORAGE . "images_compress_squeezeimg/jp2.enabled -f
 RewriteRule . - [E=FORMAT_IMG:.jp2]

 RewriteCond " . self::IMCP_DIR_STORAGE . "images_compress_squeezeimg/getimage.php -f
 RewriteCond %{REQUEST_FILENAME}%{ENV:FORMAT_IMG} -f
 RewriteRule (.*\.(jpg|jpeg|png|gif|svg|tiff|bmp)$) %{REQUEST_URI}%{ENV:FORMAT_IMG} [NC,L]
 
 RewriteCond " . self::IMCP_DIR_STORAGE . "images_compress_squeezeimg/getimage.php -f
 RewriteCond  %{REQUEST_URI} !images_compress_squeezeimg
 RewriteCond  %{REQUEST_URI} !_compress
 RewriteCond  %{QUERY_STRING} !origin=
 RewriteCond %{REQUEST_URI} .*\.(jpg|jpeg|png|gif|svg|tiff|bmp)$ [NC]
 RewriteRule (.+)\.(jpg|jpeg|png|gif|svg|tiff|bmp)$ /index.php?route=extension/module/images_compress_squeezeimg/getImage&url=$1.$2 [NC,L]
 

 </IfModule>
            ###IMAGE_CONVERTER_SQUEZZEIMG_HTACCESS_END###
 ";
                $copyHtaccess = file_get_contents($htaccessPath);
                $checkHtaccess = preg_match('/###IMAGE_CONVERTER_SQUEZZEIMG_HTACCESS_START###.*?IMAGE_CONVERTER_SQUEZZEIMG_HTACCESS_END###/ms', $copyHtaccess);
                if (empty($checkHtaccess)) {
                    $toggleText .= $copyHtaccess;
                    file_put_contents($htaccessPath, $toggleText);
                }
            }
            if (!file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . "jpg.enabled")) {
                file_put_contents(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . "jpg.enabled", 1);
            }
            if (!file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . "getimage.php")) {
                file_put_contents(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . "getimage.php", 1);
            }
        } else {
            if(file_exists($htaccessPath)) {
                $copyHtaccess = file_get_contents($htaccessPath);
                $copyHtaccess = preg_replace('/###IMAGE_CONVERTER_SQUEZZEIMG_HTACCESS_START###.*?IMAGE_CONVERTER_SQUEZZEIMG_HTACCESS_END###/ms', '', $copyHtaccess);
                file_put_contents($htaccessPath, $copyHtaccess);
            }
            if (file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . "jpg.enabled")) {
                unlink(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . "jpg.enabled");
            }
            if (file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . "getimage.php")) {
                unlink(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . "getimage.php");
            }
        }

    }

    public function enbleFormat($status,$type)
    {
            if ($status) {
                if (!file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . $type . ".enabled")) {
                    file_put_contents(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . $type . ".enabled", 1);
                }
            } else {
                if (file_exists(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . $type . ".enabled")) {
                    unlink(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/" . $type . ".enabled");
                }
            }
    }

    public function switchGzipSetting($on = false)
    {
        $htaccessPath = str_replace('system/','',DIR_SYSTEM).".htaccess";
        if(!file_exists($htaccessPath)){
            return false;
        }
        if ($on) {
            $toggleText = "
          ###IMAGE_CONVERTER_SQUEZZEIMG_START###
          <ifModule mod_gzip.c>
              mod_gzip_on Yes
              mod_gzip_dechunk Yes
              mod_gzip_item_include file \.(html?|txt|css|js|php|pl)$
              mod_gzip_item_include mime ^application/x-javascript.*
              mod_gzip_item_include mime ^text/.*
              mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
              mod_gzip_item_exclude mime ^image/.* 
              mod_gzip_item_include handler ^cgi-script$
            </ifModule>
            <IfModule mod_deflate.c>
              AddOutputFilterByType DEFLATE text/html
              AddOutputFilterByType DEFLATE text/css
              AddOutputFilterByType DEFLATE text/javascript
              AddOutputFilterByType DEFLATE text/xml
              AddOutputFilterByType DEFLATE text/plain
              AddOutputFilterByType DEFLATE image/x-icon
              AddOutputFilterByType DEFLATE image/svg+xml
              AddOutputFilterByType DEFLATE application/rss+xml
              AddOutputFilterByType DEFLATE application/javascript
              AddOutputFilterByType DEFLATE application/x-javascript
              AddOutputFilterByType DEFLATE application/xml
              AddOutputFilterByType DEFLATE application/xhtml+xml 
              AddOutputFilterByType DEFLATE application/x-font  
              AddOutputFilterByType DEFLATE application/x-font-truetype  
              AddOutputFilterByType DEFLATE application/x-font-ttf  
              AddOutputFilterByType DEFLATE application/x-font-otf 
              AddOutputFilterByType DEFLATE application/x-font-opentype 
              AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
              AddOutputFilterByType DEFLATE font/ttf 
              AddOutputFilterByType DEFLATE font/otf 
              AddOutputFilterByType DEFLATE font/opentype
            # For Older Browsers Which Can't Handle Compression
              BrowserMatch ^Mozilla/4 gzip-only-text/html 
              BrowserMatch ^Mozilla/4\.0[678] no-gzip
              BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
            </IfModule>
            <IfModule mod_mime.c>
            AddType text/css .css
            AddType text/x-component .htc
            AddType application/x-javascript .js
            AddType application/javascript .js2
            AddType text/javascript .js3
            AddType text/x-js .js4
            AddType video/asf .asf .asx .wax .wmv .wmx
            AddType video/avi .avi
            AddType image/bmp .bmp
            AddType application/java .class
            AddType video/divx .divx
            AddType application/msword .doc .docx
            AddType application/vnd.ms-fontobject .eot
            AddType application/x-msdownload .exe
            AddType image/gif .gif
            AddType application/x-gzip .gz .gzip
            AddType image/x-icon .ico
            AddType image/jpeg .jpg .jpeg .jpe
            AddType image/webp .webp
            AddType application/json .json
            AddType application/vnd.ms-access .mdb
            AddType audio/midi .mid .midi
            AddType video/quicktime .mov .qt
            AddType audio/mpeg .mp3 .m4a
            AddType video/mp4 .mp4 .m4v
            AddType video/mpeg .mpeg .mpg .mpe
            AddType video/webm .webm
            AddType application/vnd.ms-project .mpp
            AddType application/x-font-otf .otf
            AddType application/vnd.ms-opentype ._otf
            AddType application/vnd.oasis.opendocument.database .odb
            AddType application/vnd.oasis.opendocument.chart .odc
            AddType application/vnd.oasis.opendocument.formula .odf
            AddType application/vnd.oasis.opendocument.graphics .odg
            AddType application/vnd.oasis.opendocument.presentation .odp
            AddType application/vnd.oasis.opendocument.spreadsheet .ods
            AddType application/vnd.oasis.opendocument.text .odt
            AddType audio/ogg .ogg
            AddType application/pdf .pdf
            AddType image/png .png
            AddType application/vnd.ms-powerpoint .pot .pps .ppt .pptx
            AddType audio/x-realaudio .ra .ram
            AddType image/svg+xml .svg .svgz
            AddType application/x-shockwave-flash .swf
            AddType application/x-tar .tar
            AddType image/tiff .tif .tiff
            AddType application/x-font-ttf .ttf .ttc
            AddType application/vnd.ms-opentype ._ttf
            AddType audio/wav .wav
            AddType audio/wma .wma
            AddType application/vnd.ms-write .wri
            AddType application/font-woff .woff
            AddType application/font-woff2 .woff2
            AddType application/vnd.ms-excel .xla .xls .xlsx .xlt .xlw
            AddType application/zip .zip
            </IfModule>
            <IfModule mod_expires.c>
            ExpiresActive On
            ExpiresByType text/css A31536000
            ExpiresByType text/x-component A31536000
            ExpiresByType application/x-javascript A31536000
            ExpiresByType application/javascript A31536000
            ExpiresByType text/javascript A31536000
            ExpiresByType text/x-js A31536000
            ExpiresByType video/asf A31536000
            ExpiresByType video/avi A31536000
            ExpiresByType image/bmp A31536000
            ExpiresByType application/java A31536000
            ExpiresByType video/divx A31536000
            ExpiresByType application/msword A31536000
            ExpiresByType application/vnd.ms-fontobject A31536000
            ExpiresByType application/x-msdownload A31536000
            ExpiresByType image/gif A31536000
            ExpiresByType application/x-gzip A31536000
            ExpiresByType image/x-icon A31536000
            ExpiresByType image/jpeg A31536000
            ExpiresByType image/webp A31536000
            ExpiresByType application/json A31536000
            ExpiresByType application/vnd.ms-access A31536000
            ExpiresByType audio/midi A31536000
            ExpiresByType video/quicktime A31536000
            ExpiresByType audio/mpeg A31536000
            ExpiresByType video/mp4 A31536000
            ExpiresByType video/mpeg A31536000
            ExpiresByType video/webm A31536000
            ExpiresByType application/vnd.ms-project A31536000
            ExpiresByType application/x-font-otf A31536000
            ExpiresByType application/vnd.ms-opentype A31536000
            ExpiresByType application/vnd.oasis.opendocument.database A31536000
            ExpiresByType application/vnd.oasis.opendocument.chart A31536000
            ExpiresByType application/vnd.oasis.opendocument.formula A31536000
            ExpiresByType application/vnd.oasis.opendocument.graphics A31536000
            ExpiresByType application/vnd.oasis.opendocument.presentation A31536000
            ExpiresByType application/vnd.oasis.opendocument.spreadsheet A31536000
            ExpiresByType application/vnd.oasis.opendocument.text A31536000
            ExpiresByType audio/ogg A31536000
            ExpiresByType application/pdf A31536000
            ExpiresByType image/png A31536000
            ExpiresByType application/vnd.ms-powerpoint A31536000
            ExpiresByType audio/x-realaudio A31536000
            ExpiresByType image/svg+xml A31536000
            ExpiresByType application/x-shockwave-flash A31536000
            ExpiresByType application/x-tar A31536000
            ExpiresByType image/tiff A31536000
            ExpiresByType application/x-font-ttf A31536000
            ExpiresByType application/vnd.ms-opentype A31536000
            ExpiresByType audio/wav A31536000
            ExpiresByType audio/wma A31536000
            ExpiresByType application/vnd.ms-write A31536000
            ExpiresByType application/font-woff A31536000
            ExpiresByType application/font-woff2 A31536000
            ExpiresByType application/vnd.ms-excel A31536000
            ExpiresByType application/zip A31536000
            </IfModule>
            ###IMAGE_CONVERTER_SQUEZZEIMG_END###
            ";
            $copyHtaccess = file_get_contents($htaccessPath);
            $checkHtaccess = preg_match('/###IMAGE_CONVERTER_SQUEZZEIMG_START###.*?IMAGE_CONVERTER_SQUEZZEIMG_END###/ms', $copyHtaccess);
            if (empty($checkHtaccess)) {
                $copyHtaccess .= $toggleText;
                file_put_contents($htaccessPath, $copyHtaccess);
            }

        } else {
            $copyHtaccess = file_get_contents($htaccessPath);
            $copyHtaccess = preg_replace('/###IMAGE_CONVERTER_SQUEZZEIMG_START###.*?IMAGE_CONVERTER_SQUEZZEIMG_END###/ms', '', $copyHtaccess);
            file_put_contents($htaccessPath, $copyHtaccess);
        }

    }
    public function uninstall()
    {
        $files = glob(self::IMCP_DIR_STORAGE."images_compress_squeezeimg/*");
        foreach ($files as $file) {
            unlink($file);
        }
    }
    public function getImagesLoader()
    {
        $result = array();
        $dir_root = dirname(DIR_APPLICATION)."/";
        $dir_images = $dir_root."catalog/view/javascript/images_compress_squeezeimg/images";
        $files = glob($dir_images."/*");
            foreach ($files as $file){
                $result[] = str_replace($dir_root, HTTPS_CATALOG,$file);
            }
        return [
            'folder' => '/catalog/view/javascript/images_compress_squeezeimg/images',
            'files' => $result
        ];
    }
}
