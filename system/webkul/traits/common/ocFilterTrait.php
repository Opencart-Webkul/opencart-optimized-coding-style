<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
trait ocFilterTrait {
    
    public function getFilterUrl() {
        $_url = '';
     
        if(!isset($this->filter_keys)) {
            return false;
        }

        if(!is_array($this->filter_keys) || empty($this->filter_keys)) {
           return false;
        }

        foreach ($this->filter_keys as $type => $valString) {
           foreach ($valString as $key => $value) {
               if($type) {
                    switch ($type) {
                        case 'number':
                            if (isset($this->request->get[$value])) {
                                $_url .= '&' .$value. '=' .$this->request->get[$value];
                            }
                        break;
                        case 'string':
                            if (isset($this->request->get[$value])) {
                                $_url .= '&' .$value. '=' . urlencode(html_entity_decode($this->request->get[$value], ENT_QUOTES, 'UTF-8'));
                            }
                        break;
                    }
               }
           }
        }
        return  $_url;
    }

    public function getSOPFilterUrl($page = false) {
        $_url = '';
        if(isset($this->sop)) { 
            if(!is_array($this->sop) || empty($this->sop)) {
              return   $_url;
            }

            foreach ($this->sop as $key => $value) {
                if($value == 'page' && $page) 
                { 
                    if (isset($this->request->get[$value])) {
                        $_url .= '&' .$value. '=' .$this->request->get[$value];
                    } 
                }  
            }
           
        }
        return  $_url;
    }

    
    
     

}