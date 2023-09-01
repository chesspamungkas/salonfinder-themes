<?php

namespace DV\core\models;

class Customer
{
  public $parentProduct = null;

  private $_content = null;

  public static $CLASS = null;

  public static function getFactory()
  {
    if (self::$CLASS === null) {
      self::$CLASS = new Customer;
    }
    return self::$CLASS;
  }

  public static function registerFilters()
  {
    add_filter('woocommerce_save_account_details_required_fields', [self::getFactory(), 'removeRequired']);
    add_action('woocommerce_created_customer', [self::getFactory(), 'sendWelcomeEmail']);
    add_action('woocommerce_created_customer', [self::getFactory(), 'sendToMailChimp']);
  }

  public function removeRequired($fields)
  {
    return $fields;
  }

  public function sendWelcomeEmail($customer_id)
  {
    global $firstname, $email, $title, $user;
    $user = get_user_by('id', $customer_id);
    $firstname = $user->first_name;
    $email = $user->user_email;
    $title = 'Your account created with Daily Vanity';
    // Now we are ready to build our welcome email
    ob_start();
    get_template_part('src/templates/welcomeEmail.template');
    $body = ob_get_clean();
    $headers = array(
      'Content-Type : text/html; charset=UTF-8',
      'BCC : chesspamungkas@gmail.com'
    );
    if (wp_mail($email, $title, $body, $headers)) {
      error_log("email has been successfully sent to user whose email is " . $email);
    } else {
      error_log("email failed to sent to user whose email is " . $email);
    }
  }

  public function sendToMailChimp($user_id)
  {
    $apiKey = MC_KEY;
    $listId = MC_LIST_ID;

    $user = get_user_by('id', $user_id);
    $memberId = md5(strtolower($user->user_email));
    $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

    $mch = curl_init($url);

    $jsonObj = json_encode([
      'fields' => 'unique_email_id'
    ]);

    curl_setopt($mch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($mch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($mch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($mch, CURLOPT_TIMEOUT, 10);
    curl_setopt($mch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($mch, CURLOPT_POSTFIELDS, $jsonObj);
    $tempRes = curl_exec($mch);
    $httpCode = curl_getinfo($mch, CURLINFO_HTTP_CODE);
    curl_close($mch);

    $res = json_decode($tempRes);
    $stat = 'subscribed';
    if ($res->status == "unsubscribed") {
      $stat = 'pending';
    }

    $json = json_encode([
      'email_address' => $user->user_email,
      'status'        => $stat,
      'interests' => [
        '89a83d40c8' => true,
        '2bf86dfbed' => true,
        'af817af93a' => true
      ],
      'merge_fields'  => [
        'FNAME'     => $user->first_name,
        'LNAME'     => $user->last_name,
        'REGISTERED'    => 'yes',
        'FBCONNECT'    => 'no',
      ]
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
  }
}
