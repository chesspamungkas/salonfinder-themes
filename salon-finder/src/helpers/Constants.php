<?php

namespace DV\helpers;
use Aws\Credentials\Credentials;

class Constants {
  public static $VOUCHER_CODE = 'voucherCode';
  public static $REDEEMEDDATE = 'redeemDate';
  public static $REDEEMED = 'redeemed';
  public static $EXPIRED_DATE = 'expiredDate';

  const SNS_AWS_KEY='SNS_AWS_KEY';
  const SNS_AWS_SECRET='SNS_AWS_SECRET';
  const SNS_AWS_REGION='SNS_AWS_REGION';

  const SNS_SEARCH_RESULT_TOPIC='SNS_SEARCH_RESULT_TOPIC';

  public static $_model;

  public $_config = [];

  public function __construct() {
    $this->_config[self::SNS_SEARCH_RESULT_TOPIC] = SF_SNS_SEARCH_CALLBACK;    
    $this->_config[self::SNS_AWS_REGION] = AWS_REGION;
  }

  public static function getFactory() {
    if(!self::$_model) {
      self::$_model = new Constants;
    }
    return self::$_model;
  }

  public function getConfig($name) {
    if(!isset($this->_config[$name]))
      return null;
    return $this->_config[$name];
  }

  public static function getBaseDir() {
    $baseDir = dirname(__FILE__).'/../views/';
    return $baseDir;
  }

  public function getCredential() {
    return new Credentials(
      SF_AWS_ACCESS_KEY_ID,
      SF_AWS_SECRET_ACCESS_KEY
    );
  }
}