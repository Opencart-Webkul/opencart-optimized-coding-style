<?php
/**
 * @version [1.0.0.0] [Supported opencart version 3.x.x.x]
 * @category Webkul
 * @package Opencart-Webkul
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

class Ocvalidator {
	
	public function __construct($registry) {
		$this->config 	= $registry->get('config');
		$this->db 		  = $registry->get('db');
		$this->request 	= $registry->get('request');
		$this->session 	= $registry->get('session');
	}

	private function _isValidNumber($value) {
		if(preg_match('/[^0-9]+$/', trim($value))){
		  return 1;
		 } else {
		   return  0;
		 }
	}
	
	private function _isValidAlphaNumeric($value) {
				return (bool) preg_match("/^[\p{L}\p{Nd}]+$/u", $value);
	}
	
	private function _isValidAlphabetic($value) {
			return (bool) preg_match("/^[\p{L}]+$/u", $value);
	}

	private function _isArray($value) {
			return is_array($value);
	}

	private function _isAssocArray($value) {
		return [] === $value ? false : array_keys($value) !== range(0, count($value) - 1);
	}

	private function _isBool($value) {
		return is_bool($value);
	}

	private function _isValidEmail($value) {
		return false !== \filter_var($value, FILTER_VALIDATE_EMAIL);;
	}

	private function _isNotEmpty($value) {
			return strlen(trim($value)) > 0;
	}

	private function _validateMaxLength($value, $constraint = 25){
			return strlen($value) >= $constraint;
	}

	private function _validateMinLength($value, $constraint = 10){
			return strlen($value) <= $constraint;
	}

	private function isValidString($value){
			return $this->_isNotEmpty($value) ? false: is_string($value);
	}

	private function _isMax($value, $constraint) {
			return $value >= $constraint;
	}

	private function _isMin($value, $constraint)	{
			return $value <= $constraint;
	}

	private function _isInt($value)	{
			return is_int($value);
	}

	private function _isValidUrl($value) {
			return filter_var($value, FILTER_VALIDATE_URL);
	}

	private function _notEmpty($value)	{
			return is_string($value) ? (bool)trim($value) : (bool)$value;
	}

}
