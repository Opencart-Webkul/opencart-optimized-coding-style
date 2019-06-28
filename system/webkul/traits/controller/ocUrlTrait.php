<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

trait OcUrlTrait {

  private function setUrl() {
    $this->_url .= $this->urlFilterSorting();
  }

  private function getUrl() {
    return $this->_url;
  }

  public function urlFilterSorting(){

    $url = '';

    foreach ($this->filter_key as $item) {
      list($key, $type) =  explode('|',$item);
      
      if ($type == 'string') {
        if (isset($this->request->get[$key])) {
          $url .= '&'.$key.'=' . urlencode(html_entity_decode($this->request->get[$key], ENT_QUOTES, 'UTF-8'));
        }
      } else {
        if (isset($this->request->get[$key])) {
          $url .= '&'.$key.'=' . $this->request->get[$key];
        }
      }
    }

    $url .= $this->quickSort();

    return $url;
  }

 private function quickSortOrder() {
   $url = '';

   if (isset($this->request->get['sort'])) {
     $url .= '&sort=' . $this->request->get['sort'];
   }

   if (isset($this->request->get['order'])) {
     $url .= '&order=' . $this->request->get['order'];
   }

   return $url;
 }

 private function quickSort() {

   $url = $this->quickSortOrder();

   if (isset($this->request->get['page'])) {
     $url .= '&page=' . $this->request->get['page'];
   }

   return $url;
 }

}
