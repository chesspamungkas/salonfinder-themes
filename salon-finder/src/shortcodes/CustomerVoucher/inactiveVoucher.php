<?php
if( $total ): 
  foreach($orderItems as $orderItem): 
?>
  <?= $this->render('CustomerVoucher/_inactiveVoucherItem', ['order'=>$orderItem['order'], 'item'=>$orderItem['item'], 'voucherMeta'=> $orderItem['voucherMeta'],
                    'voucherCode'=> $orderItem['voucherCode']]); ?>
<?php 
  endforeach;
endif;
?>