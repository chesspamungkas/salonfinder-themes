<?php
if( $total ): 
  foreach($orderItems as $orderItem): 
?>
  <?= $this->render('CustomerVoucher/_redeemedVoucherItem', ['order'=>$orderItem['order'], 'item'=>$orderItem['item'], 'voucherMeta'=> $orderItem['voucherMeta'],
                    'voucherCode'=> $orderItem['voucherCode']]); ?>
<?php 
  endforeach;
endif;
?>