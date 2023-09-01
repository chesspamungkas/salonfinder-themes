<div class="selectContainer">
  <ul class="panel-collapse">
    <?php foreach($results as $result): ?>
      <?php $split_result = explode(" - ", $result); ?>
      <li class="container">
        <ul class="row">
            <li class="col-md-12 py-2 listItem">
              <?php if ($split_result[0]=='SERVICE'): ?>
                <span class="poppins-medium service-text"><?= $split_result[0]; ?></span>
              <?php elseif ($split_result[0]=='SALON'): ?>
                <span class="poppins-medium salon-text"><?= $split_result[0]; ?></span>
              <?php endif; ?>
               <span class="poppins-semibold parent-text"><?= $split_result[1]; ?></span> - <span class="poppins-light child-text"><?= $split_result[2]; ?></span>
            </li>
        </ul>
      </li>
    <?php endforeach; ?>
  </ul>
</div>