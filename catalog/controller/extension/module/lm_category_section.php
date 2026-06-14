<?php
class ControllerExtensionModuleLmCategorySection extends Controller {
    public function models() {
        $this->load->model('catalog/mark');
        $json = array();

        if (isset($this->request->get['mark_id'])) {
            $mark_id = (int)$this->request->get['mark_id'];
            $results = $this->model_catalog_mark->getMarks($mark_id);

            $this->load->model('tool/image');

            foreach ($results as $result) {
                $json[] = array(
                    'model_id' => $result['mark_id'],
                    'name'     => $result['name'],
                    'image'    => $result['image'] ? $this->model_tool_image->resize($result['image'], 50, 50) : '',
                    'href'     => $this->url->link('product/mark', 'mark_id=' . $result['mark_id'])
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function categories() {
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $json = array();

        if (isset($this->request->get['model_id'])) {
            $model_id = (int)$this->request->get['model_id'];
            $categories = $this->model_catalog_category->getCategories(0);

            foreach ($categories as $category) {
                $filter_data = array(
                    'filter_category_id'  => $category['category_id'],
                    'filter_sub_category' => true,
                    'filter_model'        => true,
                    'model_id'            => $model_id,
                    'start'               => 0,
                    'limit'               => 1
                );
                if ($this->model_catalog_product->getTotalProducts($filter_data) > 0) {
                    $lm_product_id = $this->_lm_findDiagramProduct($category['category_id'], $model_id);
                    $href = $lm_product_id
                        ? $this->url->link('product/product', 'product_id=' . $lm_product_id)
                        : $this->_lm_buildCategoryUrl($category['category_id'], $model_id);
                    $json[] = array(
                        'category_id' => $category['category_id'],
                        'name'        => $category['name'],
                        'href'        => $href
                    );
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function categoryBottom() {
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $json = array();

        if (isset($this->request->get['model_id'])) {
            $model_id = (int)$this->request->get['model_id'];
            $categories = $this->model_catalog_category->getCategories(0);
            $count = 0;

            foreach ($categories as $category) {
                if ($count >= 6) break;

                $lm_filter_data = array(
                    'filter_category_id'  => $category['category_id'],
                    'filter_sub_category' => true,
                    'filter_model'        => true,
                    'model_id'            => $model_id,
                    'start'               => 0,
                    'limit'               => 1
                );
                if ($this->model_catalog_product->getTotalProducts($lm_filter_data) > 0) {
                    $lm_product_id = $this->_lm_findDiagramProduct($category['category_id'], $model_id);
                    if ($lm_product_id) {
                        $lm_pinfo = $this->model_catalog_product->getProduct($lm_product_id);
                        $thumb = ($lm_pinfo && $lm_pinfo['image'])
                            ? $this->model_tool_image->resize($lm_pinfo['image'], 265, 184)
                            : ($category['image'] ? $this->model_tool_image->resize($category['image'], 265, 184) : '');
                        $href = $this->url->link('product/product', 'product_id=' . $lm_product_id);
                    } else {
                        $thumb = $category['image'] ? $this->model_tool_image->resize($category['image'], 265, 184) : '';
                        $href = $this->_lm_buildCategoryUrl($category['category_id'], $model_id);
                    }
                    $json[] = array(
                        'name'  => $category['name'],
                        'thumb' => $thumb,
                        'href'  => $href
                    );
                    $count++;
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function _lm_findDiagramProduct($category_id, $model_id) {
        $sql = "SELECT p.product_id
                FROM " . DB_PREFIX . "product p
                LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
                LEFT JOIN " . DB_PREFIX . "product_to_mark p2m ON (p.product_id = p2m.product_id)
                WHERE p.status = '1'
                  AND p.diagram = '1'
                  AND p2c.category_id IN (
                      SELECT category_id FROM " . DB_PREFIX . "category_path WHERE path_id = " . (int)$category_id . "
                  )
                  AND p2m.mark_id = " . (int)$model_id . "
                ORDER BY p.sort_order ASC, p.product_id
                LIMIT 1";
        $query = $this->db->query($sql);
        return $query->num_rows ? (int)$query->row['product_id'] : false;
    }

    private function _lm_buildCategoryUrl($category_id, $model_id) {
        $this->load->model('catalog/mark');
        $this->load->model('localisation/language');

        $language_id = (int)$this->config->get('config_language_id');
        $store_id = (int)$this->config->get('config_store_id');

        $mark_info = $this->model_catalog_mark->getMark($model_id);
        if (!$mark_info) {
            return '';
        }

        $brand_id = (int)$mark_info['parent_id'] > 0 ? (int)$mark_info['parent_id'] : $model_id;

        $query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query='mark_id=" . (int)$brand_id . "' AND store_id=" . $store_id . " AND language_id=" . $language_id);
        $brand_keyword = $query->num_rows ? $query->row['keyword'] : '';

        $query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query='mark_id=" . (int)$model_id . "' AND store_id=" . $store_id . " AND language_id=" . $language_id);
        $model_keyword = $query->num_rows ? $query->row['keyword'] : '';

        $query = $this->db->query("SELECT ua.keyword FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('category_id=', cp.path_id) WHERE cp.category_id=" . (int)$category_id . " AND ua.language_id=" . $language_id . " ORDER BY cp.level");
        $category_path = '';
        foreach ($query->rows as $row) {
            $category_path .= '/' . $row['keyword'];
        }

        $lang_info = $this->model_localisation_language->getLanguage($language_id);
        $lang_prefix = ($lang_info['url'] != '') ? '/' . $lang_info['url'] : '';

        return rtrim(HTTPS_SERVER, '/') . $lang_prefix . '/' . $brand_keyword . '/' . $model_keyword . $category_path;
    }
}