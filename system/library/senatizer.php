<?php
class Senatizer {
  // public function __construct($registry) {

  // }

  public function number_int($number ='' ,$type = '') {
   return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
  }

  public function number_float($float ='' ,$type = '') {
    return filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT);
  }

  public function string($str ='' ,$type = '') {
    return filter_var($str, FILTER_SANITIZE_STRING);
  }

  public function url($url ='' ,$type = '') {
    return filter_var($url, FILTER_SANITIZE_URL);
  }

  public function email($email ='' ,$type = '') {
    return filter_var($email, FILTER_SANITIZE_EMAIL);
  }





}
