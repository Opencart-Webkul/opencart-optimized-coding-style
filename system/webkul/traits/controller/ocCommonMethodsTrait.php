<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

trait ocCommonMethodsTrait {

	private function validatePermissions($path = '') {
		if (!$this->user->hasPermission('modify', $path)){
      $this->error['warning'] = $this->language->get('error_permission');
    }
  }

	private function genrateCBreadcrumb($breadcrumbs = array()) {
		 $this->data['breadcrumbs'] = array();
		 
		 foreach ($breadcrumbs as $key => $value) {
       $this->data['breadcrumbs'][] = array(
         'text'          => $key,
         'href'          => $this->url->link($value, 'user_token=' . $this->session->data['user_token'], true),
       );
     }
  }

	private function loadCommonControllers(){
    $this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
  }

  private function initBreadCrumbs(){
      $this->breadcrumbs = array(
         $this->language->get('text_home') => 'common/home',
         $this->language->get('heading_title') => $this->file_path,
      );
  }

  private function initAddPageBreadCrumbs(){
      $this->breadcrumbs = array(
         $this->language->get('text_home') => 'common/home',
         $this->language->get('heading_title') => $this->file_path,
         $this->language->get('heading_title_add') => $this->file_path.'/add',
      );
  }
}
