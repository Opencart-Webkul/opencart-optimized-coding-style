<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
trait OcPaginationControllerTrait {

  public function renderPagination($helper_data) {
    // get total numbers of the item to be render
    $total = isset($helper_data['total']) ? $helper_data['total'] : 0;
    // get total page number to render on
    $page = (isset($helper_data['page']) && $helper_data['page'] > 0) ? (int)$helper_data['page'] : 1;
    // set max to render
    $limit = isset($helper_data['limit']) ? $helper_data['limit'] : 10;
    //set userl isString
    $url = (isset($helper_data['url'])) ? str_replace('%7Bpage%7D', '{page}', (string)$helper_data['url']) : '';
    // total 8 links allowed per pagination to show
    $num_links = 8;
    //calculate the number of pages based on the total item to render
    $num_pages = ceil($total / $limit);

    $data['page'] = $page;
    // check if not single page to be displayed
    if ($page > 1) {
      $data['first'] = str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $url);

      if ($page - 1 === 1) {
        $data['prev'] = str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $url);
      } else {
        $data['prev'] = str_replace('{page}', $page - 1, $url);
      }

    } else {
      $data['first'] = '';
      $data['prev']  = '';
    }

    $data['links'] = array();

    if ($num_pages > 1) {
      //manage start and end values
      if ($num_pages <= $num_links) {
        $start = 1;
        $end = $num_pages;
      } else {
        $start = $page - floor($num_links / 2);
        $end = $page + floor($num_links / 2);

        if ($start < 1) {
          $end += abs($start) + 1;
          $start = 1;
        }

        if ($end > $num_pages) {
          $start -= ($end - $num_pages);
          $end = $num_pages;
        }
      }
      // create links for the all the pages based on the coutner
      for ($counter = $start; $counter <= $end; $counter++) {
        $data['links'][] = array(
          'page' => $counter,
          'href' => str_replace('{page}', $counter, $url)
        );
      }
    }

    if ($num_pages > $page) {
      $data['next'] = str_replace('{page}', $page + 1, $url);
      $data['last'] = str_replace('{page}', $num_pages, $url);
    } else {
      $data['next'] = '';
      $data['last'] = '';
    }

    if ($num_pages > 1) {
			return $this->load->view('common/wk_pagination', $this->data);
		} else {
			return '';
		}
  }
}