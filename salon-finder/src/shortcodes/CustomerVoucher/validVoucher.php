<?php 
if($total): ?>
  <?php foreach($orderItems as $orderItem): ?>
    <?= $this->render('CustomerVoucher/_validVoucherItem', ['order'=>$orderItem['order'], 'item'=>$orderItem['item'], 'voucherMeta'=> $orderItem['voucherMeta'],
                    'voucherCode'=> $orderItem['voucherCode']]); ?>
  <?php endforeach;?>
<?php endif; ?>

<script>
  var seconds = 5;
  var foo;

  function updateSecs() {
      document.getElementById( "seconds" ).innerHTML = seconds;
      seconds--;
      if ( seconds == -1 ) {
          clearInterval( foo );
          location.reload( true );
      }
  }

  function countdownTimer() {
      foo = setInterval( function () {
          updateSecs()
      }, 1000 );
  }
</script>