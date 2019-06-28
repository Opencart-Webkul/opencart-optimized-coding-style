<?php
/**
 * Webkul Software.
 * @category  Webkul
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
trait OcCrmModelTrait {

  private $edit_check = false;
  private $cur_date = '';
  private $time_zone = '';
  private $expiry_date = '0000-00-00';
  private $defaultCategory = '';
  /**
   * [$total_count keeps the total row count]
   * @var integer
   */
  private $total_count = 0;
  /**
   * [$count_counter keeps the record of number of time function called]
   * @var integer
   */
  private $count_counter = 0;

  public function update_customer_quantity($customerid = 0, $groupid = 0, $paid_status = 'unpaid', $mail = false) {

		$product = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group WHERE groupid = '" . $groupid."'")->row;
		if (!$product) {
			return false;
		}

		$product_description = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_name WHERE id = '" . $groupid."'")->rows;

		if (isset($product['expiry']) && $product['expiry'] && $this->config->get('wk_seller_group_time_limit')) {
      $this->time_zone = $this->getClientTimezone();
      $current_DateTime 		= new DateTime(null, new DateTimeZone($this->time_zone));
      $this->cur_date = $current_DateTime->format('Y-m-d');
		  $this->expiry_date = date('Y-m-d',strtotime( $this->cur_date."+" . $product['expiry']." days") );
		} else {
       date_default_timezone_set($this->config->get('wk_seller_group_time_zone'));
       $this->cur_date = date('Y-m-d', time());
       $this->time_zone = '';
			 $this->expiry_date = '0000-00-00';
		}

    /**
     * delete previous membership data if it is enabled by admin.
     * @var [type]
     */
    if($this->config->get('wk_seller_group_clear_prev_membership'))
      $this->db->query("DELETE FROM `" . DB_PREFIX . "seller_group_customer` WHERE `customer_id` = '" . $customerid."'");

		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_customer WHERE `customer_id` = '" . $customerid."'")->row;

    if ($result) {

        if ($result['gcquantity'] == -1 || $product['defaultCategoryQuantity'] == -1 || $product['defaultCategoryQuantity'] == 0) {
  				$quantity = (int)$product['defaultCategoryQuantity'];
  			}else{
          $quantity = (int)$result['gcquantity'] + (int)$product['defaultCategoryQuantity'];
  			}

  			if ($result['amount'] == -1 || $product['defaultCategoryPrice'] == -1 || $product['defaultCategoryPrice'] == 0) {
  				$amount = (float)$product['defaultCategoryPrice'];
  			}else{
          $amount = (float)$result['amount'] + (float)$product['defaultCategoryPrice'];
  			}

  			if ($result['customerDefaultCategoryProduct'] == -1 || $product['defaultCategoryProduct'] == -1 || $product['defaultCategoryProduct'] == 0) {
  				$customerDefaultCategoryProduct = (int)$product['defaultCategoryProduct'];
  			}else{
          $customerDefaultCategoryProduct = (int)$result['customerDefaultCategoryProduct'] + (int)$product['defaultCategoryProduct'];
  			}

  			if ($result['customerDefaultNoOfListing'] == -1 || $product['defaultNoOfListing'] == -1 || $product['defaultNoOfListing'] == 0) {
  				$customerDefaultNoOfListing = (int)$product['defaultNoOfListing'];
  			}else{
          $customerDefaultNoOfListing = (int)$result['customerDefaultNoOfListing'] + (int)$product['defaultNoOfListing'];
  			}

  			$customerDefaultListingDuration = $product['defaultListingDuration'];
  			$customerDefaultGroupCommissionPercentage = $product['defaultGroupCommissionPercentage'];
  			$customerDefaultGroupCommissionFixed = $product['defaultGroupCommissionFixed'];
  			$customerDefaultListingFee = $product['defaultListingFee'];

        $this->db->query("UPDATE ".DB_PREFIX."seller_group_customer SET gid='".$product['groupid']."', gcquantity = '".$quantity."', group_gpquantity = '".(int)$product['defaultCategoryQuantity']."', amount = '".$amount."', gprice = '".(float)$product['gprice']."', membership_date = '".$this->cur_date."', membership_expiry = '".$this->expiry_date."', customerDefaultCategoryProduct = '".$customerDefaultCategoryProduct."',price_paid = '".(float)$product['gprice']."', recurring_id = '".(int)$product['recurring_id']."', trial_status = '".$product['trial_status']."', customerDefaultNoOfListing = '".$customerDefaultNoOfListing."', customerDefaultListingDuration = '".$customerDefaultListingDuration."', customerDefaultGroupCommissionPercentage = '".$customerDefaultGroupCommissionPercentage."', customerDefaultGroupCommissionFixed = '".$customerDefaultGroupCommissionFixed."', customerDefaultListingFee = '".$customerDefaultListingFee."', suspended = '0' WHERE customer_id = '".$customerid."'");

        /**
         * to delete all the other membership data as there is no expiry time
         * @var [type]
         */
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seller_group_customer` WHERE groupcid <> '".$result['groupcid']."' AND customer_id = '".$customerid."'");

    } else {

      $this->db->query("INSERT INTO `".DB_PREFIX."seller_group_customer` SET `gid`='" . (int)$product['groupid'] . "', `customer_id` = '" . $customerid."', `gcquantity` = '" . (int)$product['defaultCategoryQuantity'] . "', `group_gpquantity` = '" . (int)$product['defaultCategoryQuantity'] . "', `price_paid` = '" . (float)$product['gprice'] . "', `gprice` = '" . (float)$product['gprice'] . "', `amount` = '" . (float)$product['defaultCategoryPrice'] . "', `membership_date` = '" . $this->cur_date."', `membership_expiry` = '" . $this->expiry_date."', `membership_type` = '" . $product['membership_type'] . "', `recurring_id` = '" . $product['recurring_id'] . "', `trial_status` = '" . $product['trial_status'] . "',`customerDefaultCategoryProduct` = '" . (int)$product['defaultCategoryProduct'] . "', `customerDefaultNoOfListing` = '" . (int)$product['defaultNoOfListing'] . "', `customerDefaultListingDuration` = '" . (int)$product['defaultListingDuration'] . "', `customerDefaultGroupCommissionPercentage` = '" . (float)$product['defaultGroupCommissionPercentage'] . "', `customerDefaultGroupCommissionFixed` = '" . (float)$product['defaultGroupCommissionFixed'] . "', `customerDefaultListingFee` = '" . (float)$product['defaultListingFee'] . "', `paid_status` = '" . $paid_status . "',`suspended` = '0', `expiry_timezone` = '" . $this->db->escape($this->time_zone) . "', membershipType = '" . $this->config->get('wk_seller_group_membership_type') . "'");

    }

		$this->db->query("DELETE FROM ".DB_PREFIX."seller_group_customer_seller_group WHERE seller_id = '" . (int)$customerid."'");
    /**
     * latest membership record
     * @var [type]
     */
		foreach ($product_description as $key => $value) {
			$this->db->query("INSERT INTO ".DB_PREFIX."seller_group_customer_seller_group SET seller_id = '" . (int)$customerid."', seller_group_name = '" . $value['name'] . "', language_id = '" .(int)$value['language_id']. "'");
		}


		$optionData = array (
			'productQuantity',
			'productListing',
			'productListingDuration',
			'productgroupCommission',
			'productListingFee',
		);

		$groupDetails = $this->db->query("SELECT * FROM `".DB_PREFIX."seller_group_setting` WHERE group_id = '" . $product['groupid'] . "' ")->rows;

		foreach ($groupDetails as $key => $detail) {
			foreach ($optionData as $key => $option) {
				if (in_array($option, $detail)) {
					$groupSetting[$option] = $detail['value'];
				}
			}
		}

		$seller_group_option_data = array(
			'account',
			'producttab',
			'profileoption',
			'publicsellerprofile',
	    );

		$seller_group_setting_for = array();
		foreach ($groupDetails as $key => $detail) {
			foreach ($seller_group_option_data as $key => $option) {
				if (in_array($option, $detail)) {
					$seller_group_setting_for[$option] = $detail['value'];
				}
			}
		}

		$this->db->query("DELETE FROM ".DB_PREFIX."seller_group_product_setting WHERE seller_id = '" . (int)$customerid."' ");

		foreach ($seller_group_setting_for as $key => $value) {
			$this->db->query("INSERT INTO ".DB_PREFIX."seller_group_product_setting VALUES ('', '" . $product['groupid'] . "', '" . $key."', '" . $value."','" . (int)$customerid."' ) ");
		}

    if ($this->config->get('wk_seller_group_clear_prev_membership'))
      $this->db->query("DELETE FROM `" . DB_PREFIX . "seller_group_product_quantity` WHERE  `seller_id` = '" . (int)$customerid."'");

		if (isset($groupSetting['productQuantity']) && $groupSetting['productQuantity']) {
      $entry = unserialize($groupSetting['productQuantity']);

      $compare = $this->db->query("SELECT category_id FROM `" . DB_PREFIX . "seller_group_product_quantity` WHERE  `seller_id` = '" . (int)$customerid."'")->rows;

      foreach ($compare as $old) {
        $flag = 0;
        if ($entry) {
          foreach ($entry as $new) {
            if ($old['category_id'] == $new['category_id']) {
              $flag++;
            }
          }
        }
        if (!$flag) {
          $this->db->query("DELETE FROM `".DB_PREFIX."seller_group_product_quantity` WHERE `seller_id` = '" . (int)$customerid."' AND `category_id` = '" . (int)$value['category_id'] . "'");
        }
      }

      if($entry) {

  			foreach ($entry as $key => $value) {
          $check_product_quantity = $this->db->query("SELECT * FROM `".DB_PREFIX."seller_group_product_quantity` WHERE `seller_id` = '" . (int)$customerid."' AND `category_id` = '" . (int)$value['category_id'] . "'")->row;
          if ($check_product_quantity) {

              if ($check_product_quantity['quantity'] == -1 || $value['quantity'] == -1 || $value['quantity'] == 0) {
								$quantity = $value['quantity'];
							}else{
				        $quantity = $check_product_quantity['quantity'] + $value['quantity'];
							}

							if ($check_product_quantity['price'] == -1 || $value['price'] == -1 || $value['price'] == 0) {
								$price = $value['price'];
							}else{
				        $price = $check_product_quantity['price'] + $value['price'];
							}

							if ($check_product_quantity['product'] == -1 || $value['no_of_products'] == -1 || $value['no_of_products'] == 0) {
								$value['no_of_products'] = $value['no_of_products'];
							}else{
				        $value['no_of_products'] = $check_product_quantity['product'] + $value['no_of_products'];
							}

	            $this->db->query("UPDATE ".DB_PREFIX."seller_group_product_quantity SET group_id = '".$product['groupid']."',quantity ='".(int)$quantity."',product = '".(int)$value['no_of_products']."',price ='".(float)$price."' WHERE seller_id ='".(int)$customerid."' AND category_id = '".(int)$value['category_id']."' AND id = '" . $check_product_quantity['id'] . "'");

              $this->db->query("DELETE FROM `" . DB_PREFIX . "seller_group_product_quantity` WHERE id <> '" . $check_product_quantity['id'] . "' AND seller_id ='".(int)$customerid."' AND category_id = '".(int)$value['category_id']."'");

          } else {

            $this->db->query("INSERT INTO `".DB_PREFIX."seller_group_product_quantity` SET `group_id` = '" . $product['groupid'] . "',`quantity` ='" . (int)$value['quantity']."',`product` = '" . (int)$value['no_of_products'] . "',`price` ='" . (float)$value['price']."',`seller_id` ='" . (int)$customerid."', `category_id` = '" . (int)$value['category_id'] . "', `membership_expiry` = '" . $this->expiry_date."', `expiry_timezone` = '" . $this->db->escape($this->time_zone) . "', membershipType = '" . $this->config->get('wk_seller_group_membership_type') . "'");

          }
			  }
      }
		}


    if ($this->config->get('wk_seller_group_clear_prev_membership'))
      $this->db->query("DELETE FROM `" . DB_PREFIX . "seller_group_product_listing` WHERE  seller_id = '" . (int)$customerid."'");


		if (isset($groupSetting['productListing']) && $groupSetting['productListing']) {
			$entry = unserialize($groupSetting['productListing']);

      $compare = $this->db->query("SELECT category_id FROM `" . DB_PREFIX . "seller_group_product_listing` WHERE  `seller_id` = '" . (int)$customerid."'")->rows;

      foreach ($compare as $old) {
        $flag = 0;
        if ($entry) {
          foreach ($entry as $new) {
            if ($old['category_id'] == $new['category_id']) {
              $flag++;
            }
          }
        }
        if (!$flag) {
          $this->db->query("DELETE FROM `".DB_PREFIX."seller_group_product_listing` WHERE `seller_id` = '" . (int)$customerid."' AND `category_id` = '" . (int)$value['category_id'] . "'");
        }
      }

			if ($entry) {
				foreach ($entry as $key => $value) {
					$check_product_listing = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_product_listing WHERE seller_id = '" . (int)$customerid."' AND category_id = '" . (int)$value['category_id'] . "'")->row;
          if ($check_product_listing) {

              if ($check_product_listing['quantity'] == -1 || $value['quantity'] == -1 || $value['quantity'] == 0) {
                $quantity = $value['quantity'];
              }else{
                $quantity = $check_product_listing['quantity'] + $value['quantity'];
              }

              $this->db->query("UPDATE ".DB_PREFIX."seller_group_product_listing SET group_id = '".$product['groupid']."',quantity ='".(int)$quantity."' WHERE seller_id ='".(int)$customerid."' AND category_id = '".(int)$value['category_id']."'");

              $this->db->query("DELETE FROM `" . DB_PREFIX . "seller_group_product_listing` WHERE id <> '"  . $check_product_listing['id'] . "' AND seller_id ='".(int)$customerid."' AND category_id = '".(int)$value['category_id']."'");

          } else {
            $this->db->query("INSERT INTO ".DB_PREFIX."seller_group_product_listing SET group_id = '" . $product['groupid'] . "',quantity ='" . (int)$value['quantity']."', `membership_expiry` = '" . $this->expiry_date."', `expiry_timezone` = '" . $this->db->escape($this->time_zone) . "', seller_id ='" . (int)$customerid."', category_id = '" . (int)$value['category_id'] . "', membershipType = '" . $this->config->get('wk_seller_group_membership_type') . "'");
          }
				}
			}
		}

    if ($this->config->get('wk_seller_group_clear_prev_membership'))
		  $this->db->query("DELETE FROM ".DB_PREFIX."seller_group_product_listing_duration WHERE seller_id = '" . (int)$customerid."' ");

		if (isset($groupSetting['productListingDuration'])) {
			$entry = unserialize($groupSetting['productListingDuration']);

      $compare = $this->db->query("SELECT category_id FROM `" . DB_PREFIX . "seller_group_product_listing_duration` WHERE  `seller_id` = '" . (int)$customerid."'")->rows;

      foreach ($compare as $old) {
        $flag = 0;
        if ($entry) {
          foreach ($entry as $new) {
            if ($old['category_id'] == $new['category_id']) {
              $flag++;
            }
          }
        }
        if (!$flag) {
          $this->db->query("DELETE FROM `".DB_PREFIX."seller_group_product_listing_duration` WHERE `seller_id` = '" . (int)$customerid."' AND `category_id` = '" . (int)$value['category_id'] . "'");
        }
      }

      if ($entry) {
        foreach ($entry as $key => $value) {

          $this->db->query("DELETE FROM ".DB_PREFIX."seller_group_product_listing_duration WHERE seller_id = '" . (int)$customerid."' AND category_id = '" . (int)$value['category_id'] . "'");

          $this->db->query("INSERT INTO ".DB_PREFIX."seller_group_product_listing_duration SET group_id = '" . $product['groupid'] . "',days ='" . (int)$value['days']."', `membership_expiry` = '" . $this->expiry_date."', `expiry_timezone` = '" . $this->db->escape($this->time_zone) . "', seller_id ='" . (int)$customerid."', category_id = '" . (int)$value['category_id'] . "'");

        }
      }
		}

		$this->db->query("DELETE FROM ".DB_PREFIX."seller_group_commission_categorywise WHERE `seller_id` = '" . (int)$customerid."' ");

		if (isset($groupSetting['productgroupCommission']) && $groupSetting['productgroupCommission']) {
			$entry = unserialize($groupSetting['productgroupCommission']);

      $compare = $this->db->query("SELECT category_id FROM `" . DB_PREFIX . "seller_group_commission_categorywise` WHERE  `seller_id` = '" . (int)$customerid."'")->rows;

      foreach ($compare as $old) {
        $flag = 0;
        if ($entry) {
          foreach ($entry as $new) {
            if ($old['category_id'] == $new['category_id']) {
              $flag++;
            }
          }
        }
        if (!$flag) {
          $this->db->query("DELETE FROM `".DB_PREFIX."seller_group_commission_categorywise` WHERE `seller_id` = '" . (int)$customerid."' AND `category_id` = '" . (int)$value['category_id'] . "'");
        }
      }

			if ($entry) {
				foreach ($entry as $key => $value) {

          $this->db->query("INSERT INTO `".DB_PREFIX."seller_group_commission_categorywise` SET `group_id` = '" . $product['groupid'] . "', `percentage` = '" . (float)$value['percentage'] . "', `fixed` = '" . (float)$value['fixed'] . "', `category_id` = '" . (int)$value['category_id'] . "', `seller_id` = '" . (int)$customerid."'");

				}
			}
		}

		$this->db->query("DELETE FROM ".DB_PREFIX."seller_group_product_listing_fee WHERE `seller_id` = '" . (int)$customerid."' ");

		if (isset($groupSetting['productListingFee']) && $groupSetting['productListingFee']) {
			$entry = unserialize($groupSetting['productListingFee']);

      $compare = $this->db->query("SELECT category_id FROM `" . DB_PREFIX . "seller_group_product_listing_fee` WHERE  `seller_id` = '" . (int)$customerid."'")->rows;

      foreach ($compare as $old) {
        $flag = 0;
        if ($entry) {
          foreach ($entry as $new) {
            if ($old['category_id'] == $new['category_id']) {
              $flag++;
            }
          }
        }
        if (!$flag) {
          $this->db->query("DELETE FROM `".DB_PREFIX."seller_group_product_listing_fee` WHERE `seller_id` = '" . (int)$customerid."' AND `category_id` = '" . (int)$value['category_id'] . "'");
        }
      }

			if ($entry) {
        foreach ($entry as $key => $value) {
          $this->db->query("INSERT INTO ".DB_PREFIX."seller_group_product_listing_fee SET `group_id` = '" . $product['groupid'] . "', `fee` = '" . (float)$value['fee'] . "', `category_id` = '" . (int)$value['category_id'] . "', `seller_id` = '" . (int)$customerid."'");
				}
			}
		}

    /**
     * membership payment entry if seller paid status is paid
     * insert_payment function code
     * @var [type]
     */
    if ($paid_status == 'paid') {
      $this->db->query("INSERT INTO ".DB_PREFIX."seller_group_payment VALUES ('','" . (int)$customerid . "', '" . (int)$product['defaultCategoryQuantity'] . "', '" . (float)$product['gprice'] . "', NOW() ) ");
    }

    if ($this->config->get('wk_seller_group_mail_on_membership') && $mail) {
			$data = array(
				 'seller_id'   => $customerid,
	       'customer_id' => $customerid,
	       'mail_id'     => $this->config->get('wk_seller_group_mail_on_membership'),
	       'mail_from'   => $this->config->get('marketplace_adminmail') ? $this->config->get('marketplace_adminmail') : $this->config->get('config_email'),
	       'mail_to'     => $this->db->query("SELECT email FROM `" . DB_PREFIX . "customer` WHERE customer_id='" . (int)$customerid . "'")->row['email'],
	    );
	    $this->mail($data);
	  }

	}

  public function getClientTimezone() {
    $customer_Time_Zone = $this->config->get('wk_seller_group_time_zone');
    return $customer_Time_Zone;
  }

  public function getTime($timeZone = 'UTC') {
    $current_DateTime 		= new DateTime(null, new DateTimeZone($timeZone));
    return $current_DateTime->format('Y-m-d');
  }

  /**
	 * [checkAvailabilityProductToAdd function used when membership type is set to 'product' and here it checks the remaining product to add new product of a seller by category wise]
	 * @param  [type] $category_id [category Id ]
	 * @param  string $post        [post data to check whether product is edited or relisted so that remaing products will be previous products ]
	 * @return [type]              [array with category Id and remaining products of that category Id and category Id is Zero for default category]
	 */
	public function checkAvailabilityProductToAdd($category_id = 0,$post=array(), $customer_id = 0){

		$rest_products = '';

    /**
     * if product is being edited or relisted then category which were previously added previously will remain
     * unlimited.
     */
    if ((isset($post['edit']) && $post['edit'] && isset($this->request->get['product_id'])|| isset($post['relist']) && $post['relist']) && isset($this->request->get['product_id'])) {
      if ($this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_category` WHERE product_id = " . (int)$this->request->get['product_id'] . " AND category_id = '" . $category_id . "'")) {
        return 'unlimited';
      }
    }

    if (!isset($this->session->data['membership_array'][$category_id])) {
      $this->session->data['membership_original'][$category_id] = $this->productCategoryRemaining($category_id,$customer_id);
    }

		if (is_numeric($this->session->data['membership_array'][$category_id])) {
      return $this->checkAvailabilityProductToAddReturn($this->session->data['membership_array'][$category_id],$category_id,$category_id);
		} else if ($this->session->data['membership_array'][$category_id] == 'empty') {
      if (isset($this->session->data['membership_array'][$category_id])) {
        unset($this->session->data['membership_array'][$category_id]);
      }
      if(isset($this->session->data['membership_original'][$category_id])) {
        unset($this->session->data['membership_original'][$category_id]);
      }
      if (!isset($this->session->data['membership_array'][0])) {
        $this->session->data['membership_original'][0] = $this->defaultCategoryRemaining($customer_id);
      }
      if (is_numeric($this->session->data['membership_array'][0])) {
        return $this->checkAvailabilityProductToAddReturn($this->session->data['membership_array'][0],$category_id,0);
      }
		}
    return false;
	}

  public function checkAvailabilityProductToAddReturn($quantity = -1,$post_category_id = 0,$category_id = 0) {

    if ($quantity == 0) {
      return 'unlimited';
    } else if ($quantity < 0){
      $this->unsetMembershipValidation();
      return false;
    } else if ($quantity > 0) {
      /**
       * in case of general category different category sum should come;
       * @var [type]
       */
      $this->session->data['membership_array'][$category_id] = $rest_products = $quantity - 1;
    }

    if ($rest_products == 0 && $this->session->data['membership_original'][$category_id]) {
      $this->session->data['membership_array'][$category_id] = $rest_products = -1;
    }

    return array(
      'post_category_id'  => $post_category_id,
      'category_id'       => $category_id,
      'rest_products'     => $rest_products,
    );

  }

  protected function unsetMembershipValidation() {
    if (isset($this->session->data['membership_array'])) {
      unset($this->session->data['membership_array']);
    }
    if(isset($this->session->data['membership_original'])) {
      unset($this->session->data['membership_original']);
    }
  }

  /**
	 * [checkAvailabilityToAdd checking availability that as per details product is eligible to be added to store according to seller's membreship]
	 * @param  [integer] $quantity    [quantity of product]
	 * @param  [integer] $seller_id   [customer id of seller]
	 * @param  [float] $price       [price of product]
	 * @param  [integer] $category_id [id of category]
	 * @return [array|boolean]              [remaining details as per membership and product details|false]
	 */
	public function checkAvailabilityToAdd($quantity, $seller_id, $price, $category_id,$post = array()) {

    /**
     * if product is being edited or relisted then category which were previously added previously will remain
     * unlimited.
     */
     if ((isset($post['edit']) && $post['edit'] && isset($this->request->get['product_id'])|| isset($post['relist']) && $post['relist']) && isset($this->request->get['product_id'])) {
       if ($this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_category` WHERE product_id = " . (int)$this->request->get['product_id'] . " AND category_id = '" . $category_id . "'")->num_rows) {
         return 'unlimited';
       }
     }

    if (!isset($this->session->data['membership_array'][$category_id])) {
		    $this->session->data['membership_original'][$category_id] = $this->getQuantityPriceCategoryWise($category_id, $seller_id);
    }

    if (is_array($this->session->data['membership_array'][$category_id])) {
      if (!isset($this->session->data['membership_array'][$category_id])) {
        $this->session->data['membership_original'][$category_id] = $this->defaultCategoryRemaining($seller_id);
      }
		} else if ($this->session->data['membership_array'][$category_id] == 'empty') {

      if (isset($this->session->data['membership_array'][$category_id])) {
        unset($this->session->data['membership_array'][$category_id]);
      }
      if(isset($this->session->data['membership_original'][$category_id])) {
        unset($this->session->data['membership_original'][$category_id]);
      }

      $category_id = 0;

      if (!isset($this->session->data['membership_array'][0])) {
        $this->session->data['membership_original'][0] = $this->getQuantityPriceDefault($seller_id);
      }
		} else {
      $this->unsetMembershipValidation();
      return false;
    }

    if (!is_array($this->session->data['membership_array'][$category_id]) || $this->session->data['membership_array'][$category_id]['quantity'] < 0 || $this->session->data['membership_array'][$category_id]['price'] < 0) {
      $this->unsetMembershipValidation();
      return false;
    }

    $rest_quantity = $this->session->data['membership_array'][$category_id]['quantity'];
    $rest_price = $this->session->data['membership_array'][$category_id]['price'];

    if ($rest_quantity == 0 && $rest_price == 0) {
      return 'unlimited';
    } else if ($rest_quantity == 0 && $rest_price > 0) {
      $rest_price = $rest_price - $price;
      $rest_quantity = 0;
    } else if ($rest_quantity > 0 && $rest_price == 0) {
      $rest_price = 0;
      $rest_quantity = $rest_quantity - $quantity;
    } else if ($rest_quantity > 0 && $rest_price > 0) {
      $rest_quantity = $rest_quantity - $quantity;
      $rest_price = $rest_price - $price;
    }

    if ($rest_quantity < 0 || $rest_price < 0) {
      $this->unsetMembershipValidation();
      return false;
    }

    if ($rest_price == 0 && $this->session->data['membership_original'][$category_id]['price'] != 0) {
      $this->session->data['membership_array'][$category_id]['price'] = -1;
    } else {
      $this->session->data['membership_array'][$category_id]['price'] = $rest_price;
    }
    if ($rest_quantity == 0 && $this->session->data['membership_original'][$category_id]['quantity'] != 0) {
      $this->session->data['membership_array'][$category_id]['quantity'] = -1;
    } else {
      $this->session->data['membership_array'][$category_id]['quantity'] = $rest_quantity;
    }

    if ($category_id) {
      return array(
        'category_id' => $category_id,
        'quantity' => $rest_quantity,
        'price' => $rest_price
      );
    }
    return array(
      'quantity' => $rest_quantity,
      'price' => $rest_price
    );
	}

	/**
	 * [insertInPay to check and update seller's membership details by category wise]
	 * @param  [integer] $category_id [id of category]
	 * @param  [array]  $updation    [details about deduction and all from seller's membership]
	 * @return [boolean]              [true]
	 */
	public function insertInPay($updation = array(), $customer_id = 0) {

    if (!isset($this->session->data['membership_original']) || !isset($this->session->data['membership_array'])) {
      return false;
    }

    $time = $this->getTime($this->config->get('wk_seller_group_time_zone'));

		if ($this->customer->getId()) {
			$customer_id = $this->customer->getId();
		}

    if (isset($this->session->data['membership_original'])) {
      foreach ($this->session->data['membership_original'] as $category_id => $value) {
        if ($this->config->get('wk_seller_group_membership_type') == 'quantity') {
          if (!is_array($value)) {
            die('something went wrong');
          }
          if ($value['price'] == 0 && $value['quantity'] == 0) {
            continue;
          }

          $difference_price = $value['price'] - $this->session->data['membership_array'][$category_id]['price'];
          $difference_quantity = $value['quantity'] - $this->session->data['membership_array'][$category_id]['quantity'];

          $from = $this->config->get('config_currency');
          $to = isset($this->session->data['currency']) ? $this->session->data['currency'] : $this->currency->getCode();

          $difference_price = $this->currency->convert($difference_price,$from,$to);

          if ($category_id) {

            $result = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_product_quantity WHERE seller_id = '" . (int)$customer_id."' AND category_id = '" . (int)$category_id."' AND membership_expiry >= " . $time . "")->rows;

            foreach ($result as $row) {

              if ($row['price'] < $difference_price && $row['price'] != 0) {
                if($row['price'] >= 0)
                  $difference_price = $difference_price - $row['price'];
                $new_price = -1;
              } else if ($row['price'] != 0){
                $new_price = $row['price'] - $difference_price;
                if ($new_price == 0) {
                  $new_price = -1;
                }
                $difference_price = 0;
              } else {
                $difference_price = 0;
                $new_price = 0;
              }

              if ($row['quantity'] < $difference_quantity && $row['quantity'] != 0) {
                $new_quantity = -1;
                if($row['quantity'] >= 0)
                  $difference_quantity = $difference_quantity - $row['quantity'];
              } else if ($row['quantity'] != 0){
                $new_quantity = $row['quantity'] - $difference_quantity;
                if ($new_quantity == 0) {
                  $new_quantity = -1;
                }
                $difference_quantity = 0;
              } else {
                $difference_quantity = 0;
                $new_quantity = 0;
              }
              $this->db->query("UPDATE `".DB_PREFIX."seller_group_product_quantity` SET quantity = '" . (int)$new_quantity . "', price = '" . (float)$new_price . "' WHERE id = " . $row['id'] . "");

              if($difference_quantity <= 0 && $difference_price <= 0)
                break;
            }
          } else {
            $result = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_customer WHERE customer_id = '" . (int)$customer_id."' AND membership_expiry >= " . $time . "")->rows;
            foreach ($result as $row) {
              if ($row['amount'] < $difference_price && $row['amount'] != 0) {
                if($row['amount'] >= 0)
                  $difference_price = $difference_price - $row['amount'];
                $new_price = -1;
              } else if ($row['amount'] != 0){
                $new_price = $row['amount'] - $difference_price;
                if ($new_price == 0) {
                  $new_price = -1;
                }
                $difference_price = 0;
              } else {
                $difference_price = 0;
                $new_price = 0;
              }
              if ($row['gcquantity'] < $difference_quantity && $row['gcquantity'] != 0) {
                $new_quantity = -1;
                if($row['gcquantity'] >= 0)
                  $difference_quantity = $difference_quantity - $row['gcquantity'];
              } else if ($row['gcquantity'] != 0){
                $new_quantity = $row['gcquantity'] - $difference_quantity;
                if ($new_quantity == 0) {
                  $new_quantity = -1;
                }
                $difference_quantity = 0;
              } else {
                $difference_quantity = 0;
                $new_quantity = 0;
              }
              $this->db->query("UPDATE `".DB_PREFIX."seller_group_customer` SET gcquantity = '" . (int)$new_quantity . "', amount = '" . (float)$new_price . "' WHERE groupcid = " . $row['groupcid'] . "");
              if($difference_quantity <= 0 && $difference_price <= 0)
                break;
            }
          }
        } else if ($this->config->get('wk_seller_group_membership_type') == 'product') {
          if (!is_numeric($value)) {
            die('something went wrong');
          }
          if ($value == 0) {
            continue;
          }
          $difference_product = $value - $this->session->data['membership_array'][$category_id];
          if ($category_id) {

            $result = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_product_quantity WHERE seller_id = '" . (int)$customer_id."' AND category_id = '" . (int)$category_id."' AND membership_expiry >= " . $time . "")->rows;

            foreach ($result as $row) {

              if ($row['product'] < $difference_product && $row['product'] != 0) {
                $new_product = -1;
                if($row['product'] >= 0)
                  $difference_product = $difference_product - $row['product'];
              } else if($row['product'] != 0) {
                $new_product = $row['product'] - $difference_product;
                if ($new_product == 0) {
                  $new_product = -1;
                }
                $difference_product = 0;
              } else {
                $new_product = 0;
                $difference_product = 0;
              }

              $this->db->query("UPDATE `".DB_PREFIX."seller_group_product_quantity` SET product = '" . (int)$new_product . "' WHERE id = " . $row['id'] . "");
              if ($difference_product <= 0) {
                break;
              }
            }
          } else {
            $result = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_customer WHERE customer_id = '" . (int)$customer_id."' AND membership_expiry >= " . $time . "")->rows;

            foreach ($result as $row) {

              if ($row['customerDefaultCategoryProduct'] < $difference_product && $row['customerDefaultCategoryProduct'] != 0) {
                $new_product = -1;
                if($row['customerDefaultCategoryProduct'] >= 0)
                  $difference_product = $difference_product - $row['customerDefaultCategoryProduct'];
              } else if($row['customerDefaultCategoryProduct'] != 0) {
                $new_product = $row['customerDefaultCategoryProduct'] - $difference_product;
                if ($new_product == 0) {
                  $new_product = -1;
                }
                $difference_product = 0;
              } else {
                $new_product = 0;
                $difference_product = 0;
              }

              $this->db->query("UPDATE `".DB_PREFIX."seller_group_customer` SET customerDefaultCategoryProduct = '" . (int)$new_product . "' WHERE groupcid = " . $row['groupcid'] . "");
              if ($difference_product <= 0) {
                break;
              }
            }
          }
        }
      }
    }
		$this->unsetMembershipValidation();
		return true;
	}

  public function getQuantityPriceCategoryWise($category_id = 0, $customer_id = 0) {

    $this->session->data['membership_array'][$category_id]['price'] = -1;
    $this->session->data['membership_array'][$category_id]['quantity'] = -1;
    $time = $this->getTime($this->config->get('wk_seller_group_time_zone'));

    $value = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_product_quantity WHERE seller_id = '" . (int)$customer_id."' AND category_id = '" . (int)$category_id."' AND membership_expiry >= " . $time . "")->row;

    if (!$value) {
      $this->session->data['membership_array'][$category_id] = 'empty';
      return 'empty';
    }

    if ($value['quantity'] >= 0) {
      if ($this->session->data['membership_array'][$category_id]['quantity'] == -1) {
        $this->session->data['membership_array'][$category_id]['quantity'] = 0;
      }
      $this->session->data['membership_array'][$category_id]['quantity'] += $value['quantity'];
    }
    if ($value['price'] >= 0) {
      if ($this->session->data['membership_array'][$category_id]['price'] == -1) {
        $this->session->data['membership_array'][$category_id]['price'] = 0;
      }
      $from = $this->config->get('config_currency');
      $to = isset($this->session->data['currency']) ? $this->session->data['currency'] : $this->currency->getCode();
      $this->session->data['membership_array'][$category_id]['price'] += $this->currency->convert($value['price'],$from,$to);
    }

    return $this->session->data['membership_array'][$category_id];

  }

  public function getQuantityPriceDefault($customer_id) {
    $category_id = 0;
    $this->session->data['membership_array'][0]['price'] = -1;
    $this->session->data['membership_array'][0]['quantity'] = -1;
    $time = $this->getTime($this->config->get('wk_seller_group_time_zone'));

    $value = $this->db->query("SELECT * FROM `".DB_PREFIX."seller_group_customer` WHERE `customer_id` = '" . (int)$customer_id."' AND membership_expiry >= " . $time . "")->row;

    if (!$value) {
      $this->session->data['membership_array'][0] = 'empty';
      return 'empty';
    }
    if ($value['gcquantity'] >= 0) {
      if ($this->session->data['membership_array'][$category_id]['quantity'] == -1) {
        $this->session->data['membership_array'][$category_id]['quantity'] = 0;
      }
      $this->session->data['membership_array'][$category_id]['quantity'] += $value['gcquantity'];
    }

    if ($value['amount'] >= 0) {
      if ($this->session->data['membership_array'][$category_id]['price'] == -1) {
        $this->session->data['membership_array'][$category_id]['price'] = 0;
      }
      $from = $this->config->get('config_currency');
      $to = isset($this->session->data['currency']) ? $this->session->data['currency'] : $this->currency->getCode();

      $this->session->data['membership_array'][$category_id]['price'] +=   $this->currency->convert($value['amount'],$from,$to);
    }

    return $this->session->data['membership_array'][0];

  }

  public function productCategoryRemaining($category_id = 0, $customer_id = 0) {

    $this->session->data['membership_array'][$category_id] = -1;

    $time = $this->getTime($this->config->get('wk_seller_group_time_zone'));

    $value = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_product_quantity WHERE seller_id = '" . (int)$customer_id."' AND category_id = '" . (int)$category_id."' AND membership_expiry >= " . $time . "")->row;

    if (!$value) {
      $this->session->data['membership_array'][$category_id] = 'empty';
      return 'empty';
    }

    if ($value['product'] >= 0) {
      if ($this->session->data['membership_array'][$category_id] == -1) {
        $this->session->data['membership_array'][$category_id] = 0;
      }
      $this->session->data['membership_array'][$category_id] += $value['product'];
    }

    return $this->session->data['membership_array'][$category_id];
  }

  public function defaultCategoryRemaining($customer_id = 0){
    $category_id = 0;
    $this->session->data['membership_array'][0] = -1;

    $time = $this->getTime($this->config->get('wk_seller_group_time_zone'));
    $value = $this->db->query("SELECT * FROM `".DB_PREFIX."seller_group_customer` WHERE `customer_id` = '" . (int)$customer_id."' AND membership_expiry >= " . $time . "")->row;

    if (!$value) {
      $this->session->data['membership_array'][0] = 'empty';
      return 'empty';
    }

    if ($value['customerDefaultCategoryProduct'] >= 0) {
      if ($this->session->data['membership_array'][$category_id] == -1) {
        $this->session->data['membership_array'][$category_id] = 0;
      }
      $this->session->data['membership_array'][$category_id] += $value['customerDefaultCategoryProduct'];
    }

    return $this->session->data['membership_array'][0];

  }

  public function getRemainingListingDuration($seller_id = 0, $category_id = 0) {

    $listing_durations = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group_product_listing WHERE seller_id = '" . $seller_id."' AND category_id = '" . (int)$category_id . "'")->row;

    if ($listing_durations && $listing_durations['quantity']) {
      return $listing_durations['quantity'];
		} else {
      $listing_durations =$this->db->query("SELECT customerDefaultListingDuration FROM ".DB_PREFIX."seller_group_customer WHERE customer_id = '" . $seller_id."' ")->row;
      if($listing_durations['customerDefaultListingDuration'] > 0) {
        return $listing_durations['customerDefaultListingDuration'];
      }
    }
    return false;
  }

  public function mail($data, $value_index = array()){

    $mail_id = $data['mail_id'];
		$mail_from = $data['mail_from'];
		$mail_to = $data['mail_to'];

    $seller_info = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$data['seller_id'] . "'")->row;

    $mail_details = $this->getMailData($mail_id);

    if($mail_details){
      $data['store_name'] = $this->config->get('config_name');
      $data['store_url'] = HTTP_SERVER;
      $data['logo'] = HTTP_SERVER.'image/' . $this->config->get('config_logo');

      $membershipDetails = $this->db->query("SELECT * FROM ".DB_PREFIX."seller_group sg LEFT JOIN ".DB_PREFIX."seller_group_customer sgc ON (sg.groupid=sgc.gid) LEFT JOIN ".DB_PREFIX."seller_group_name sgn ON (sgn.id=sg.groupid) LEFT JOIN ".DB_PREFIX."customer c ON (c.customer_id=sgc.customer_id) WHERE sgc.customer_id = '" . $data['customer_id'] . "' AND sgn.language_id = '" . $this->config->get('config_language_id')."' ")->row;

      $commission = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customerpartner_to_customer` WHERE customer_id = '" . (int)$data['seller_id'] . "'")->row;

      if (isset($commission['commission'])) {
        $commission = $commission['commission'];
      } else {
        $commission = $this->config->get('marketplace_commission');
      }

      if (isset($membershipDetails['membership_expiry'])) {
        $end = strtotime($membershipDetails['membership_expiry']);
        $today = strtotime(date('Y-m-d'));
        $diff = $end - $today;
        $daysleft=floor($diff/(60*60*24));
        $membershipDetails['membership_commission'] = $commission + $membershipDetails['defaultGroupCommissionPercentage'] . "% + " . $this->membership->currencyformat($membershipDetails['defaultGroupCommissionFixed']);
      } else {
        $daysleft = '';
        $membershipDetails['membership_commission'] = 0.00;
      }

      $find = array(
        '{order}',
        '{seller_message}',
        '{customer_message}',
        '{commission}',
        '{product_name}',
        '{transaction_message}',
        '{transaction_amount}',
        '{seller_name}',
        '{config_logo}',
        '{config_icon}',
        '{config_currency}',
        '{config_image}',
        '{config_name}',
        '{config_owner}',
        '{config_address}',
        '{config_geocode}',
        '{config_email}',
        '{config_telephone}',
        '{membership_plan}',
        '{membership_expiry}',
        '{membership_type}',
        '{membership_date}',
        '{membership_commission}',
        '{days_left}',
        '{membership_product}',
        '{expiry_date}',
        '{transaction_id}',
        '{transaction_error}',
        '{membership_update_table}',
        );

      $replace = array(
        'order' => '',
        'seller_message' => '',
        'customer_message' => '',
        'commission' => '',
        'product_name' => '',
        'transaction_message' => '',
        'transaction_amount' => '',
        'seller_name' => $seller_info['firstname'].' '.$seller_info['lastname'],
        'config_logo' => '<a href="'.HTTP_SERVER.'" title="'.$data['store_name'].'"><img src="'.HTTP_SERVER.'image/' . $this->config->get('config_logo').'" alt="'.$data['store_name'].'" /></a>',
        'config_icon' => '<img src="'.HTTP_SERVER.'image/' . $this->config->get('config_icon').'">',
        'config_currency' => $this->config->get('config_currency'),
        'config_image' => '<img src="'.HTTP_SERVER.'image/' . $this->config->get('config_image').'">',
        'config_name' => $this->config->get('config_name'),
        'config_owner' => $this->config->get('config_owner'),
        'config_address' => $this->config->get('config_address'),
        'config_geocode' => $this->config->get('config_geocode'),
        'config_email' => $this->config->get('config_email'),
        'config_telephone' => $this->config->get('config_telephone'),

        // membership mail code
        'membership_plan' => isset($membershipDetails['name']) ? $membershipDetails['name'] : '',
        'membership_expiry' => isset($membershipDetails['membership_expiry']) ? $membershipDetails['membership_expiry'] : '',
        'membership_type' => $membershipDetails['membership_type'] == 'otm' ? 'One Time Membership' : 'Subscription Type Membership',
        'membership_date' => isset($membershipDetails['membership_date']) ? $membershipDetails['membership_date'] : '',
        'membership_commission'   => $membershipDetails['membership_commission'],
        'days_left' => isset($daysleft) ? $daysleft : '',
        'membership_product' => '',
        'expiry_date' => isset($membershipDetails['membership_expiry']) ? $membershipDetails['membership_expiry'] : '',
        'transaction_id' => '',
        'transaction_error' => '',
        'membership_update_table' => '',
      );

      $replace = array_merge($replace,$value_index);

      foreach ($find as $key => $value) {
        $sort_order[$value] = $value;
      }

      array_multisort($sort_order, SORT_ASC, $find);

      ksort($find);
      ksort($replace);

      $mail_details['message'] = trim(str_replace($find, $replace, $mail_details['message']));

      $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
      <html>
        <head>
          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
          <title>'.$mail_details['message'].'</title>
        </head>
        <body>
          <div class="content" >
           '.html_entity_decode($mail_details['message'], ENT_QUOTES, 'UTF-8').'
          </div>
        </body>
      </html>';

        if (preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $mail_to) AND preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $mail_from) ) {
          $mail = new Mail($this->config->get('config_mail_engine'));
          $mail->parameter = $this->config->get('config_mail_parameter');
          $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
          $mail->smtp_username = $this->config->get('config_mail_smtp_username');
          $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
          $mail->smtp_port = $this->config->get('config_mail_smtp_port');
          $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
          $mail->setTo($mail_to);
    			$mail->setFrom($mail_from);
    			$mail->setSender(html_entity_decode($data['store_name'], ENT_QUOTES, 'UTF-8'));
    			$mail->setSubject(html_entity_decode($mail_details['subject'], ENT_QUOTES, 'UTF-8'));
    			$mail->setText(strip_tags($html));
    			$mail->send();
      }
    }
  }

	public function getMailData($id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customerpartner_mail WHERE id='".(int)$id."'");
		return $query->row;
	}

  /**
   * [publishProduct to publish particular product]
   * @param  [integer] $product_id [id of product]
   * @return [boolean]             [true]
   */
  public function publishProduct($product_id) {
    if ($this->config->get('marketplace_productapprov')) {
      $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customerpartner_to_product` WHERE product_id = '" . (int)$product_id . "'")->row;
      if (isset($result['current_status']) && $result['current_status'] != 'expired' && $result['current_status'] != 'disabled') {
        $this->db->query("UPDATE ".DB_PREFIX."customerpartner_to_product SET current_status = 'active' WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("UPDATE ".DB_PREFIX."product SET status = '1' WHERE product_id = '".(int)$product_id."' ");
        $mailData = array();
        $this->load->model('customerpartner/mail');
        $mailData['mail_id'] = $this->config->get('marketplace_mail_admin_on_edit');
        $mailData['mail_from'] = $this->customer->getEmail() ? $this->customer->getEmail() : $this->config->get('config_email');
        $mailData['mail_to'] = $this->config->get('marketplace_adminmail') ? $this->config->get('marketplace_adminmail') : $this->config->get('config_email');
        $this->model_customerpartner_mail->mail($mailData,$values);
        return true;
      }
    }
    return false;
  }

  /**
   * [unpublishProduct to unpublish particular product]
   * @param  [integer] $product_id [id of product]
   * @return [boolean]             [true]
   */
  public function unpublishProduct($product_id) {
    $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customerpartner_to_product` WHERE product_id = '" . (int)$product_id . "'")->row;
    if (isset($result['current_status']) && $result['current_status'] != 'expired' && $result['current_status'] != 'disabled') {
      $this->db->query("UPDATE ".DB_PREFIX."customerpartner_to_product SET current_status = 'inactive' WHERE product_id = '" . (int)$product_id . "'");
      $this->db->query("UPDATE ".DB_PREFIX."product SET status = '0' WHERE product_id = '".(int)$product_id."' ");
      if($this->config->get('wk_mpaddproduct_assign_update_to_assignseller')){
        $this->load->model('account/wk_assignmail');
        $mail_data = array(
          'seller_id' => $this->customer->getId(),
          'mail_id' => $this->config->get('wk_mpaddproduct_assign_update_to_assignseller'),
          'mail_from' => $this->config->get('config_email'),
          'mail_to' => $this->customer->getEmail(),
          'product_link' => HTTPS_SERVER.'index.php?route=account/assignproduct/wk_assign_productlist',
        );
        $this->model_account_wk_assignmail->mail($mail_data, $values);
      }
      return true;
    }
    return false;
  }

}
