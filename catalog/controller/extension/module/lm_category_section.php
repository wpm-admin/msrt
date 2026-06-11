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
                    'filter_category_id' => $category['category_id'],
                    'filter_sub_category' => true,
                    'filter_mark_id'     => $model_id,
                    'start'              => 0,
                    'limit'              => 1
                );

                if ($this->model_catalog_product->getTotalProducts($filter_data) > 0) {
                    $json[] = array(
                        'category_id' => $category['category_id'],
                        'name'        => $category['name'],
                        'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
                    );
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
