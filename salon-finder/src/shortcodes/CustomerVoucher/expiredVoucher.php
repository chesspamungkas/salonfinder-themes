
<?php if($total): ?>
  <?php foreach($orderItems as $orderItem): ?>
    <?= $this->render('CustomerVoucher/_expiredVoucherItem', ['order'=>$orderItem['order'], 'item'=>$orderItem['item'], 'voucherMeta'=> $orderItem['voucherMeta'],
                    'voucherCode'=> $orderItem['voucherCode']]); ?>
  <?php endforeach;?>
<?php endif; ?>