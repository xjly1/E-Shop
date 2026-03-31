<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Products extends REST_Controller
{
    private $allowed_img_types;
    function __construct()
    {
        parent::__construct();
        $this->methods['all_get']['limit'] = 500;
        $this->methods['one_get']['limit'] = 500;
        $this->methods['set_post']['limit'] = 100;
        $this->methods['productDel_delete']['limit'] = 50;
        $this->load->model(array('Api_model', 'admin/Products_model'));
        $this->allowed_img_types = $this->config->item('allowed_img_types');
    }

    public function all_get($lang)
    {
        $products = $this->Api_model->getProducts($lang);
        if ($products) {
            $this->response($products, REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'No products were found'
                    ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function one_get($lang, $id)
    {
        $product = $this->Api_model->getProduct($lang, $id);
        if ($product) {
            $this->response($product, REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'No product were found'
                    ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function set_post()
    {
        $errors = [];
        $_POST['image'] = $this->uploadImage();
        if (!isset($_POST['translations']) || empty($_POST['translations'])) {
            $errors[] = 'No translations array or empty';
        }
        if (!isset($_POST['title']) || empty($_POST['title'])) {
            $errors[] = 'No title array or empty';
        }
        if (!isset($_POST['basic_description']) || empty($_POST['basic_description'])) {
            $errors[] = 'No basic_description array or empty';
        }
        if (!isset($_POST['description']) || empty($_POST['description'])) {
            $errors[] = 'No description array or empty';
        }
        if (!isset($_POST['price']) || empty($_POST['price'])) {
            $errors[] = 'No price array or empty';
        }
        if (!isset($_POST['old_price']) || empty($_POST['old_price'])) {
            $errors[] = 'No old_price array or empty';
        }
        if (!isset($_POST['shop_categorie'])) {
            $errors[] = 'shop_categorie not found';
        }
        if (!isset($_POST['quantity'])) {
            $errors[] = 'quantity not found';
        }
        if (!isset($_POST['in_slider'])) {
            $errors[] = 'in_slider not found';
        }
        if (!isset($_POST['position'])) {
            $errors[] = 'position not found';
        }
        if (!empty($errors)) {
            $error = implode(", ", $errors);
            $message = [
                'message' => $error
            ];
        } else {
            $this->Api_model->setProduct($_POST);
            $message = [
                'message' => 'Added a resource'
            ];
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    private function uploadImage()
    {
        $config['upload_path'] = './attachments/shop_images/';
        $config['allowed_types'] = $this->allowed_img_types;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('userfile')) {
            log_message('error', 'Image Upload Error: ' . $this->upload->display_errors());
        }
        $img = $this->upload->data();
        return $img['file_name'];
    }

    public function productDel_delete($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }
        $this->Api_model->deleteProduct($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];
        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT);
    }
}