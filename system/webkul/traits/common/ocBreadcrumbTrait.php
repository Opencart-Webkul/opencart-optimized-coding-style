<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
trait ocBreadcrumbTrait {

    private function genrateCommonBreadcrumb($breadcrumbs = array()) {
        (isset($this->breadcrumbs) && is_array($this->breadcrumbs)) ? $this->setBradCrumbs($breadcrumbs) : '';
        $this->data['breadcrumbs'] = array();
         foreach ($this->breadcrumbs as $key => $value) {
           $this->data['breadcrumbs'][] = array(
             'text'          => $key,
             'href'          => $this->url->link($value, 'user_token=' . $this->session->data['user_token'], true),
           );
         }
    }

    private function setBradCrumbs($breadcrumbs) {
      array_push($this->breadcrumbs,$breadcrumbs);
    }

    private function initBreadCrumbs($path = 'admin'){
        $route = $path == 'admin' ? 'common/dashboard' : 'common/home';
        $this->breadcrumbs = array(
           $this->language->get('text_home') => $route,
           $this->language->get('heading_title') => $this->file_path,
        );
    }
    

    
    
     

}