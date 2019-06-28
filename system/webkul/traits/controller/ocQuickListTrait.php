<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

trait OcQuickListTrait {

  public function setWarningVariable() {
    $this->data['error_warning'] =  isset($this->error['warning']) ?  $this->error['warning'] : '';
  }

  public function setSuccessVariable() {
    $this->data['success'] =  isset($this->session->data['success']) ?  $this->session->data['success'] : '';
  }

  public function setSeletedVariable() {
    $this->data['selected'] =  isset($this->request->post['selected']) ?  (array)$this->request->post['selected'] : array();
  }

  public function setPageNumber($page) {
    $this->page = $page;
  }

  public function getPageNumber($page) {
    return $this->page;
  }

  private function webkulListingSetup() {
    /**
     * [foreach manage Filter]
    */
    foreach ($this->_filter as $type => $filter_key) {
      // second loop will be based on the type stored in the variable
      foreach ($filter_key as $key => $default) {
          $filter_{$key} = isset($this->request->get['filter_'.$key]) ? $this->request->get['filter_'.$key] : $default;
          // this will used to get results from the sql
          array_push($this->filter_data, array('filter_'.$key => $filter_{$key}));
          //store in the data variable
          $this->data['filter_'.$key] = $filter_{$key};
      }
    }
    /**
     * [foreach manage URL SOP sort order and page variables]
    */
    // merge both page and sort order variables
    $sop = array_merge($this->_sort_order,$this->_page);
    // loop to get all variable value
    foreach ($sop as $key => $value) {
      ${$key} = isset($this->request->get[$key]) ? $this->request->get[$key] : $value;
      if($key != 'page')
         array_push($this->filter_data, array( $key => ${$key}));
      //store in the data variable
      $this->data[$key] = ${$key};
    }

    $this->urlManipulation();

    // set filter array with limit values
    $sql_limit = array(
      'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
      'limit'           => $this->config->get('config_limit_admin')
    );

    $this->filter_data = array_merge($this->filter_data,$sql_limit);
  }




  public function urlManipulation() {
   // inilize url to blank and create with get varibaless
   $this->_url = '';

   foreach ($this->_filter as $dtype => $typeArray) {
      foreach ($typeArray as $key => $value) {
        if($dtype) {
          switch ($dtype) {
            case 'int':
                if (isset($this->request->get['filter_'.$key])) {
                  $this->_url .= '&filter_' .$key. '=' .$this->request->get['filter_'.$key];
                }
              break;
            case 'string':
                if (isset($this->request->get['filter_'.$key])) {
                  $this->_url .= '&filter_' .$key. '=' . urlencode(html_entity_decode($this->request->get['filter_'.$key], ENT_QUOTES, 'UTF-8'));
                }
              break;
          }
        }
      }
   }
 }

 public function webkulUrlMakeForNextPage() {
   $this->_url = '';

   $order = isset($this->request->get['order']) ? $this->request->get['order'] : '';

   $this->data['user_token'] = $this->session->data['user_token'];

   $this->urlManipulation();

   $this->_url .= ($order == 'ASC') ? '&order=DESC' : '&order=ASC';

   if (isset($this->request->get['page'])) {
     $this->_url.= '&page=' . $this->request->get['page'];
   }
 }
 public function webkulListingFooterPart() {

   $this->urlManipulation();

   $pagination = new Pagination();

   $pagination->total = $this->product_total;
   $pagination->page = $this->data['page'];
   $pagination->limit = $this->config->get('config_limit_admin');
   $pagination->url = $this->url->link($this->file_path, 'user_token=' . $this->session->data['user_token'] . $this->_url. '&page={page}', true);

   $this->data['pagination'] = $pagination->render();

   $this->data['results'] = sprintf($this->language->get('text_pagination'), ($this->product_total) ? (($this->data['page'] - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($this->data['page'] - 1) * $this->config->get('config_limit_admin')) > ($this->product_total - $this->config->get('config_limit_admin'))) ? $this->product_total : ((($this->data['page'] - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $this->product_total, ceil($this->product_total / $this->config->get('config_limit_admin')));

   $this->loadCommonControllers();
 }



 

}
