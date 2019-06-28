<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
trait OcCrmMasterTrait {
  public function __rebuildMaterPath(){
    // Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->file_path);
    $this->helper_modal = 'model_' . str_replace('/', '_', $route);
  }
}
