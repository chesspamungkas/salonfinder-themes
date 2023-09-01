<?php

namespace DV\helpers;
use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;

class SNSHelper {
  
  public static $_model;

  private $SNSClient;

  public function __construct() {
    $constant = new Constants();
    $this->SNSClient = new SnsClient([
      'region' => $constant->getConfig(Constants::SNS_AWS_REGION),
      'version' => '2010-03-31',
      'credentials'=>$constant->getCredential()
    ]);
  }

  public function publishToSNS($topicName, $body) {
    try {
      $result = $this->SNSClient->publish([
        'Message'=>json_encode($body),
        'TopicArn'=>$topicName
      ]);
      return $result;
    } catch(AwsException $e) {      
      return false;
    }
  }

  public static function publish($topicName, $body) {
    if(self::$_model==null) {
      self::$_model = new SNSHelper();
    }
    return self::$_model->publishToSNS($topicName, $body);
  }
}