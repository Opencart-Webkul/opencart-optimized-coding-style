<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

trait OcQuickAddTrait {
  public function setModelPath($route) {
    $this->model_path = $route;
  }

  public function getModelObject($route) {
    if(!$route)
      $route = $this->setModelPath($route);
    $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
    $this->model_object = 'model_' . str_replace('/', '_', $route);
  }

  private function ocMasterTrait($call_back = '', $model_function, $get_variable = 0) {
    $this->load->model($this->model_path);
    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if ($get_variable){
        $this->{$this->model_object}->{$model_function}($get_variable, $this->request->post);
      } else {
        $this->{$this->model_object}->{$model_function}($this->request->post);
      }

      $this->setWkSessionSuccess($this->data['text_success']);

      $url = $this->quickSort();
      $this->response->redirect($this->url->link($this->file_path, 'user_token=' . $this->session->data['user_token'] . $url, true));
    }
    $this->{$call_back}();
 }

 private function ocMasterDeleteTrait($model_obj = '', $model_function, $get_variable = 0) {
   if (isset($this->request->post['selected']) && $this->validateDelete()) {
     foreach ($this->request->post['selected'] as $delete_item_id) {
       $this->{$model_obj}->{$model_function}($delete_item_id);
     }

     $this->setWkSessionSuccess($this->data['text_success']);

     $url = $this->quickSort();

     $this->response->redirect($this->url->link($this->file_path, 'user_token=' . $this->session->data['user_token'] . $url, true));
   }
   $this->getList();
 }

 private function setWkSessionSuccess(){
    $this->session->data['success'] = $this->language->get('text_success');
 }
 

}
