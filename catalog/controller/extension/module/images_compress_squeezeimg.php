<?php


class ControllerExtensionModuleImagesCompressSqueezeimg extends Controller
{
    public function cron()
    {
        $status = $this->config->get('module_images_compress_squeezeimg_status');
        $quality = ($this->config->get('module_images_compress_squeezeimg_quality')) ? $this->config->get('module_images_compress_squeezeimg_quality') : 90;
        $limit = $this->config->get('module_images_compress_squeezeimg_count_send_cron') ? $this->config->get('module_images_compress_squeezeimg_count_send_cron') : 100;
        $replace = $this->config->get('module_images_compress_squeezeimg_origin_replace');
        $type = $this->config->get('module_images_compress_squeezeimg_cron_type');
        if ($type == 'all') {
            $types = ['jpg', 'webp', 'jp2','avif'];
        } else {
            $types = [$type];
        }

        $this->load->library('images_compress_squeezeimg/images');
        $this->images->cron($status, $quality, $limit, $replace, $types, $this->config->get('module_images_compress_squeezeimg_token'));
    }

    public function getImage()
    {
        if (!empty($this->request->get['url'])) {
            $this->load->library('images_compress_squeezeimg/images');
            $link = html_entity_decode($this->request->get['url']);
            $dir_root = dirname(DIR_APPLICATION);
            $file = $dir_root . "/" . $link;
            if (!file_exists($file)) {
                $link = str_replace(' ', '%20', $link);
                $name = basename($this->request->get['url']);
                $name = str_replace(' ', '*', $name);
                $files = $this->images->rglobBase($dir_root . "/*" . $name);
                if (!empty($files)) {
                    $link = str_replace($dir_root . "/", '', $files[0]);
                }
            }
            $image = $link;
            $extension = pathinfo($image, PATHINFO_EXTENSION);

            try {
                if ($this->config->get('module_images_compress_squeezeimg_status') && !is_null($this->config->get('module_images_compress_squeezeimg_token'))) {
                    if ($this->config->get('module_images_compress_squeezeimg_status')) {
                        $format = 'jpg';
                    }
                    if ($this->config->get('module_images_compress_squeezeimg_webp_status')) {
                        $format = 'webp';
                    }
                    if ($this->config->get('module_images_compress_squeezeimg_avif_status')) {
                        $format = 'avif';
                    }
                    $safari = false;
                    if (isset($_SERVER['HTTP_USER_AGENT'])) {
                        if ((strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false) &&
                            (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false)) {
                            $safari = true;
                        }
                    }
                    if ($safari) {
                        if ($this->config->get('module_images_compress_squeezeimg_jp2_status')) {
                            $format = 'jp2';
                        }
                    }
                    if (file_exists($image)) {
                        $pathinfo = pathinfo($image);
                        if (!empty($pathinfo)) {
                            $new_name = $image;
                            if ($format == 'jpg') {
                                if (strpos($image, "_compress.jpg") !== false) {
                                    header("Content-type: image/" . $extension);
                                    header("IMAGE-COMPRESS-SQUEEZEIMG: PINTA-ORIGIN-JPG");
                                    header("IMAGE-COMPRESS-SQUEEZEIMG-SIZE: " . $this->filesizeFormatted($image));
                                    header('Content-Length: ' . filesize($image));
                                    echo file_get_contents($image);
                                    exit;
                                } else {
                                    $new_name .= '_compress';
                                }
                            }
                            $new_file = $new_name . "." . $format;

                            if (file_exists($new_file)) {
                                header("Content-type: image/" . $format);
                                header("IMAGE-COMPRESS-SQUEEZEIMG: PINTA-WEBP");
                                header("IMAGE-COMPRESS-SQUEEZEIMG-SIZE: " . $this->filesizeFormatted($new_file));
                                header('Content-Length: ' . filesize($new_file));

                                echo file_get_contents($new_file);
                                exit;
                            } else {
                                $quality = ($this->config->get('module_images_compress_squeezeimg_quality')) ?
                                    $this->config->get('module_images_compress_squeezeimg_quality') : 90;
                                if ($this->config->get('module_images_compress_squeezeimg_status')) {
                                    $extension = pathinfo($image, PATHINFO_EXTENSION);
                                    $new_file = $image . '_compress' . "." . $extension;
                                    $data = $this->images->getApiSendSettings('jpg', $this->config->get('module_images_compress_squeezeimg_token'));
                                    $this->convertFile($image, $new_file, $quality, $data);
                                }
                                if ($this->config->get('module_images_compress_squeezeimg_webp_status')) {
                                    $new_file = $image . ".webp";
                                    $data = $this->images->getApiSendSettings('webp', $this->config->get('module_images_compress_squeezeimg_token'));

                                    $this->convertFile($image, $new_file, $quality, $data);
                                }
                                if ($this->config->get('module_images_compress_squeezeimg_avif_status')) {
                                    $new_file = $image . ".avif";
                                    $data = $this->images->getApiSendSettings('avif', $this->config->get('module_images_compress_squeezeimg_token'));

                                    $this->convertFile($image, $new_file, $quality, $data);
                                }
                                if ($this->config->get('module_images_compress_squeezeimg_jp2_status')) {
                                    $new_file = $image . ".jp2";
                                    $data = $this->images->getApiSendSettings('jp2', $this->config->get('module_images_compress_squeezeimg_token'));
                                    $this->convertFile($image, $new_file, $quality, $data);
                                }
                            }
                        }
                    } else {
                        header("LE-FIL23E: file not exists");
                    }
                }
            } catch (\Exception $e) {
                //  echo $e->getMessage();die;
            }
            header("Content-type: image/" . $extension);
            header("IMAGE-COMPRESS-SQUEEZEIMG: PINTA-ORIGIN");
            header("IMAGE-COMPRESS-SQUEEZEIMG-SIZE: " . $this->filesizeFormatted($image));
            header('Content-Length: ' . filesize($image));
            echo file_get_contents($image);
            exit;
        }
    }

    private function convertFile($image, $new_file, $quality, $data)
    {
        $result = $this->images->convertFile($image, $new_file, $quality, $data);
        if(isset($result['status']) && $result['status']){
            if(isset($data['method']) && $data['method'] == 'compress'){
                if($this->config->get('module_images_compress_squeezeimg_origin_replace')){
                    $this->images->update($image,'replace',1);
                } else {
                    $this->images->update($image,'jpg',1);
                }
            } else {
                $this->images->update($image,$data['to'],1);
            }
        }
    }

    private function filesizeFormatted($path)
    {
        $size = filesize($path);
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    public function sitemap()
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?>';
        $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $products = $this->model_catalog_product->getProducts();

        foreach ($products as $product) {
            if ($product['image']) {
                $output .= '<url>';
                $output .= '  <loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</loc>';
                $output .= '  <changefreq>weekly</changefreq>';
                $output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
                $output .= '  <priority>1.0</priority>';
                $output .= '  <image:image>';
                $output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
                $output .= '  <image:caption>' . $product['name'] . '</image:caption>';
                $output .= '  <image:title>' . $product['name'] . '</image:title>';
                $output .= '  </image:image>';
                $output .= '</url>';
            }
        }

        $this->load->model('catalog/category');

        $output .= $this->getCategories(0);

        $this->load->model('catalog/manufacturer');

        $manufacturers = $this->model_catalog_manufacturer->getManufacturers();

        foreach ($manufacturers as $manufacturer) {
            $output .= '<url>';
            $output .= '  <loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']) . '</loc>';
            $output .= '  <changefreq>weekly</changefreq>';
            $output .= '  <priority>0.7</priority>';
            $output .= '</url>';

            $products = $this->model_catalog_product->getProducts(array('filter_manufacturer_id' => $manufacturer['manufacturer_id']));

            foreach ($products as $product) {
                $output .= '<url>';
                $output .= '  <loc>' . $this->url->link('product/product', 'manufacturer_id=' . $manufacturer['manufacturer_id'] . '&product_id=' . $product['product_id']) . '</loc>';
                $output .= '  <changefreq>weekly</changefreq>';
                $output .= '  <priority>1.0</priority>';
                $output .= '</url>';
            }
        }

        $this->load->model('catalog/information');

        $informations = $this->model_catalog_information->getInformations();

        foreach ($informations as $information) {
            $output .= '<url>';
            $output .= '  <loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id']) . '</loc>';
            $output .= '  <changefreq>weekly</changefreq>';
            $output .= '  <priority>0.5</priority>';
            $output .= '</url>';
        }

        $output .= '</urlset>';

        $this->response->addHeader('Content-Type: application/xml');
        $this->response->setOutput($output);
    }

    protected function getCategories($parent_id, $current_path = '')
    {
        $output = '';

        $results = $this->model_catalog_category->getCategories($parent_id);

        foreach ($results as $result) {
            if (!$current_path) {
                $new_path = $result['category_id'];
            } else {
                $new_path = $current_path . '_' . $result['category_id'];
            }

            $output .= '<url>';
            $output .= '  <loc>' . $this->url->link('product/category', 'path=' . $new_path) . '</loc>';
            $output .= '  <changefreq>weekly</changefreq>';
            $output .= '  <priority>0.7</priority>';
            $output .= '</url>';

            $products = $this->model_catalog_product->getProducts(array('filter_category_id' => $result['category_id']));

            foreach ($products as $product) {
                $output .= '<url>';
                $output .= '  <loc>' . $this->url->link('product/product', 'path=' . $new_path . '&product_id=' . $product['product_id']) . '</loc>';
                $output .= '  <changefreq>weekly</changefreq>';
                $output .= '  <priority>1.0</priority>';
                $output .= '</url>';
            }

            $output .= $this->getCategories($result['category_id'], $new_path);
        }

        return $output;
    }
}


