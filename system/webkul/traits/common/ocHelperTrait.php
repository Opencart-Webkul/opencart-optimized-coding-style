<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
trait ocHelperTrait {
    
    // public function __construct($registory) {
    //     parent::__construct($registory);

    //    $this->setFilePath();

    //    $this->data = $this->load->language($this->file_path);
       
    //    $this->load->model($this->file_path);

    //    $this->getModelObject($this->file_path);
    // }

    public function getFilePath() {
        $_class = $this->getClassName();

        $_routeArray = $this->createRouteByClassName($_class);
        
        function lowercase(&$value, $key) {
            $value = strtolower($value);
        }

        array_walk($_routeArray,'lowercase');

        $_route = implode('/',$_routeArray);
        
        return $_route;
    }

    public function setFilePath() {
        $this->file_path = $this->getFilePath();
    }

    public function setModelPath($route) {
        $this->model_path = $route;
    }
   
    public function getModelObject($route) {
        if(!$route)
          $route = $this->setModelPath($route);
        $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
        $this->model_object = 'model_' . str_replace('/', '_', $route);
    }
    
    public function createRouteByClassName($_className) {
        $path = preg_split('/(?=[A-Z])/',$_className);
        
        return array_splice($path,2,2);
    }

    public function getClassName() {
      return get_class($this);
    }

}