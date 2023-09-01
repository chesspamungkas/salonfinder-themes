<?php

namespace DV\core\models;

class OrderItemProduct extends \WC_Order_Item_Product
{

  const ORDER_FOREIGN_KEY = 'sf_order_id';

  const SF_FULLY_REDEEMED = 'sf-fully-redeemed';

  const STATUS_REDEEMED = 2;
  const STATUS_INACTIVE = 0;
  const STATUS_ACTIVE = 1;

  const VOUCHER_CODE = 'voucherCode';
  const VOUCHER_REDEEMED = 'voucherRedeemed';
  const VOUCHER_HAS_REDEEMED = 'voucherHasRedeemed';

  private $_voucher = [];
  private $expiryDate = '';
  private $_unredeemedVoucher = [];
  private $_redeemedVoucher = [];
  private $_voucherQrCode = [];

  private $_voucherReady = false;

  public static function init($wc_order_item)
  {
    // echo '<pre>'; print_r($wc_order_item); echo '</pre>';
    $item = new OrderItemProduct($wc_order_item);
    // echo '<pre>'; print_r($item); echo '</pre>';
    $item->populate();
    return $item;
  }
  public function populate()
  {
    // print_r(self::VOUCHER_CODE);
    $this->_voucher = $this->get_meta(self::VOUCHER_CODE, true);
    // echo '<pre>'; print_r($this->_voucher); echo '</pre>';
    if ($this->_voucher && count($this->_voucher)) {
      $this->_voucherReady = true;
      foreach ($this->_voucher as $voucher) {
        // echo '<pre>'; print_r($voucher['expiryDate']); echo '</pre>';
        $this->expiryDate = new \DateTime($voucher['expiryDate']);
        $today = new \DateTime();
        $expired = false;
        if ($this->expiryDate < $today) {
          $expired = true;
        }
        if ($voucher['status'] == self::STATUS_REDEEMED || $voucher['status'] == self::STATUS_INACTIVE || $voucher['status'] == self::STATUS_ACTIVE || $expired) {
          $displayDate = new \DateTime();
          // print_r("A");
          if ($voucher['status'] == self::STATUS_INACTIVE || $expired)
            $displayDate = new \DateTime($voucher['expiryDate']);
          else
            $displayDate = new \DateTime($voucher['redeemedDate']);
          $this->_redeemedVoucher[] = [
            'voucher' => $voucher['voucher'],
            'displayDate' => $displayDate
          ];
        } else {
          $this->_unredeemedVoucher[] = $voucher['voucher'];
          // print_r("B");
        }
        $this->_voucherQrCode[] = $voucher['qrCode'];
      }
    }
  }

  public function getUnRedeemedVoucher()
  {
    return $this->_unredeemedVoucher;
  }

  public function getRedeemedVoucher()
  {
    return $this->_redeemedVoucher;
  }

  public function getExpiryDate()
  {
    if ($this->_voucherReady)
      return $this->expiryDate->format('d M Y');
    return "-";
  }

  public function getVoucher()
  {
    return $this->_voucher;
  }

  public function getQrCode()
  {
    return $this->_voucherQrCode[0];
  }
}
