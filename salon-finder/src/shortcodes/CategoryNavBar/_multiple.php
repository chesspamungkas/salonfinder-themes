<div class="container" id="category-navbar">
    <div class="row justify-content-center p-3">
        <?php 
            $numOfItem = 1;
            $dcolumn = 1;
            $mcolumn = 1;
            foreach( $navbar as $menu_item ):
                echo $this->render( 'CategoryNavBar/_item1', [ 'menu_item' => $menu_item, 'numOfItem' => $numOfItem, 'desktop_col' => $dcolumn, 'mobile_col' => $mcolumn, 'menu_count' => $menu_count ] ); 

                $numOfItem++;

                if( $dcolumn == 3 ):
                    $dcolumn = 1;
                else:
                    $dcolumn++;
                endif;

                if( $mcolumn == 2 ):
                    $mcolumn = 1;
                else:
                    $mcolumn++;
                endif;
            endforeach;
        ?>
    </div>
</div>