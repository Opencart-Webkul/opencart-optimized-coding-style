<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
trait OcVisitorModelTrait {

  private  function updateExitTime() {
    $date = date("m-d-Y");
    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "crm_visitor_history WHERE customer_id = '" . (int)$this->customer->getId() . "' AND date = cast((now()) as date)  AND  session_id = '" . $this->db->escape($this->session->getId()) . "'");

    if (!$query->row['total']) {
      $this->db->query("INSERT " . DB_PREFIX . "crm_visitor_history SET date = NOW(),sign_out = NOW(),sign_in = NOW(), customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session->getId()) . "'");
    } else {
      $this->db->query("UPDATE " . DB_PREFIX . "crm_visitor_history SET sign_out = NOW(), customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND date = cast((now()) as date)");
    }
  }

  private  function updatePageInfo($post) {
    $date = date("m-d-Y");

    if($post['url']){

      $build_condition = "customer_id = '" . (int)$this->customer->getId() . "' AND date = cast((now()) as date)  AND  page_url = '" . $this->db->escape($post['url']) . "'";

      $query = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "crm_visitor_pages WHERE ";

      $query .= $build_condition;

      $query = $this->db->query($query);

      $this->db->query("INSERT " . DB_PREFIX . "crm_visitor_pages SET date = NOW(),in_time = NOW(),out_time = NOW(), customer_id = '" . (int)$this->customer->getId() . "',page_url = '" . $this->db->escape($post['url']) . "'");

      if (!$query->row['total']) {
        $this->db->query("INSERT " . DB_PREFIX . "crm_visitor_page_count SET date = NOW(), count = 1, customer_id = '" . (int)$this->customer->getId() . "', page_url = '" . $this->db->escape($post['url']) . "'");
      } else {
        $query = "SELECT count FROM " . DB_PREFIX . "crm_visitor_page_count WHERE ";

        $query .= $build_condition;

        $getCount = $this->db->query($query)->row;

        $count_pages = isset($getCount['count']) ? $getCount['count'] + 1 : 1;

        $query = "UPDATE " . DB_PREFIX . "crm_visitor_page_count set count = '".(int)$count_pages."' WHERE  ";

        $query .= $build_condition;

        $this->db->query($query);
      }
    }

  }

}
