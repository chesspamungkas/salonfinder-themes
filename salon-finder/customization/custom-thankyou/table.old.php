<?php

	$args = array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'cookies' => array(),
		'body' => array('order_key' => $order->get_id(), 'email' => $order->get_billing_email()),
	);
	$response = wp_remote_post( VOUCHERS_API . 'vouchers', $args);
	$unredeemed = '';
	if( is_array($response) && isset( $response['body'] ) ) {
		$unredeemed = json_decode( $response['body']);
		$unredeemed = $unredeemed->data->unredeemed;
	}

	$ids = array();

	for( $i=0;$i<=1;$i++ ) {
		if( ! ( is_array( $unredeemed ) && sizeof( $unredeemed ) ) ) {
			$response = wp_remote_post( VOUCHERS_API . 'vouchers', $args);
			if( is_array($response) && isset( $response['body'] ) ) {
				$unredeemed = json_decode( $response['body']);
				$unredeemed = $unredeemed->data->unredeemed;
			}
		}
		if( is_array( $unredeemed ) && sizeof( $unredeemed ) ) {
			break;
		}
	}
	
	/* mailchimp update number of orders and last date of order fields upon checkout. */    
	
	 $apiKey = 'ab4e063ad57111402c303011b1227ee1-us9';
	 $listId = 'ff1e372d54';
	 
	$current_user = wp_get_current_user();

	$memberId = md5(strtolower($current_user->user_email));
	$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
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

	if( $res->status == "unsubscribed" ) {
		$stat = 'pending';
	} else {
		$stat = 'subscribed';
	}
	
	$customer_orders = get_posts( array(
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => get_current_user_id(),
		'post_type'   => wc_get_order_types(),
		'post_status' => array_keys( wc_get_order_statuses() ),
	) );
	
	$number_orders = sizeof( $customer_orders );
	$last_order = $customer_orders[0]->post_date;
	
	
	$first_name = get_user_meta($current_user->ID, 'first_name', true);
	$last_name = get_user_meta($current_user->ID, 'last_name', true);
	$contact_num = get_user_meta($current_user->ID, 'billing_phone',true);
	$genders = get_user_meta($current_user->ID, 'gender', true);
	$dob = get_user_meta($current_user->ID, 'dob', true);
	$skin_type = get_user_meta($current_user->ID, 'skin_type', true);
	$skin_tone = get_user_meta($current_user->ID, 'skin_tone', true);
	$skin_under_tone = get_user_meta($current_user->ID, 'skin_under_tone', true);
	$ann_salary = get_user_meta($current_user->ID, 'ann_salary', true);
	$history = get_user_meta($current_user->ID, '_history_tags', true);
	
	$type = get_user_meta($current_user->ID, 'register_type', true);
	$is_facebook = $type == 'facebook' ? 'yes' : 'no';
	$is_website = $type == 'website' ? 'yes' : 'no';
	
	$dob_args = explode("/", $dob);
	
	$dob = $dob ? strtotime( str_replace( '/', '-', $dob ) ) : '';
	
	$category = array();
	$child_category = array();
	$parent = '';
	$category_key = array(
		'Weight Management'		=> 'MMERGE14',
		'Nail'					=> 'MMERGE15',
		'Makeup'				=> 'MMERGE17',
		'Hair Removal'			=> 'MMERGE22',
		'Facial'				=> 'MMERGE4',
		'Massage'				=> 'MMERGE5',
		'Hair'					=> 'MMERGE8',
		'Brows/Lashes'			=> 'MMERGE11',
		'Aesthetics Treatment'	=> 'MMERGE12',
		'Aesthetics Surgery'	=> 'MMERGE13'
	);
	if ( sizeof( $order->get_items() ) > 0 ) {
		foreach( $order->get_items() as $item ) {
			$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
			$parent_product = $_product->get_parent_id() ? $_product->get_parent_id() : $_product->get_id();
			$product_cat = get_the_terms($_product->get_parent_id(), 'product_cat');
			foreach( $product_cat as $cat ) {
				if( $cat->parent == 0 ) {
					$category[$cat->name] = date("m/d/Y");
					$parent = $cat->name;
				}
			}
			foreach( $product_cat as $cat ) {
				if( $cat->parent != 0 ) {
					$child_category[] = $parent . '-'. $cat->name;
				}
			}
		}
	}
	// var_dump($category);
	
	$history = explode(',',$history);
	$history = array_map( 'trim', $history );
	if( sizeof( $history ) ) {
		$history = array_unique($history);
	}
	$child_category = array_merge( $child_category, $history );
	$child_category = array_map( 'trim', $child_category );
	$child_category = array_unique( $child_category );
	
	// if( $child_category ) {
		$child_category = implode(", ", $child_category);
	// }
	
	// $child_category = ltrim( $history . ', ' . $child_category, ', ' );
	
	$json = json_encode([
		'email_address' => $current_user->user_email,
		'status'        => $stat,
		'interests' => [
			// '843fa90b86' => true,
			'89a83d40c8' => true,
			'2bf86dfbed' => true,
			'af817af93a' => true
		],
		'merge_fields'  => [
			'FNAME'     => $first_name,
			'LNAME'     => $last_name,
			'DOB'  		=> ( $dob ? date("m/d/Y", $dob ) : '' ),
			'MMERGE6'  		=> absint( $dob_args[0] ),
			'MMERGE23'  	=> absint( $dob_args[1] ),
			'MMERGE24'  	=> absint( $dob_args[2] ),
			'NBORDERS'  	=> $number_orders,
			'LASTORDER'  	=> date('m/d/Y', strtotime($last_order)),
			'PHONE'  		=> $contact_num,
			'GENDER'    	=> ucwords($genders),
			'REGISTERED'    => $is_website,
			'FBCONNECT'    => $is_facebook,
			'SKINTYPE'    =>  str_replace('\\','', $skin_type ),
			'SKINTONE'    => str_replace('\\','', $skin_tone ),
			'SKINUTONE'    => str_replace('\\','', $skin_under_tone ),
			'ANNSALARY'    => str_replace('\\','', $ann_salary ),
			'MMERGE14'    => ( $category['Weight Management'] ? $category['Weight Management'] : '' ),
			'MMERGE15'    => ( $category['Nail'] ? $category['Nail'] : '' ),
			'MMERGE17'    => ( $category['Makeup'] ? $category['Makeup'] : '' ),
			'MMERGE22'    => ( $category['Hair Removal'] ? $category['Hair Removal'] : '' ),
			'MMERGE4'    => ( $category['Facial'] ? $category['Facial'] : '' ),
			'MMERGE5'    => ( $category['Massage'] ? $category['Massage'] : '' ),
			'MMERGE8'    => ( $category['Hair'] ? $category['Hair'] : '' ),
			'MMERGE11'    => ( $category['Brows/Lashes'] ? $category['Brows/Lashes'] : '' ),
			'MMERGE12'    => ( $category['Aesthetics Treatment'] ? $category['Aesthetics Treatment'] : '' ),
			'MMERGE13'    => ( $category['Aesthetics Surgery'] ? $category['Aesthetics Surgery'] : '' ),
			'MMERGE20'    => $child_category
		]
	]);

	update_user_meta( $current_user->ID, '_history_tags', $child_category );
	
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
?>
<h2><?php _e( 'Order Details', 'woocommerce' ); ?></h2>
<br>
<table class="shop_table order_details">
    <thead>
        <tr>
            <th class="product-name"><?php _e( 'Item', 'woocommerce' ); ?></th>
            <th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ( sizeof( $order->get_items() ) > 0 ) {
//echo '<pre>';
//print_r($order->get_items() );
            foreach( $order->get_items() as $item ) {
                $_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
                $item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );
     // print_r($item);
                ?>
                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                    <td class="product-name">
                        <?php
						// print_r( $item . '---------------------' );
                            if ( $_product && ! $_product->is_visible() )
                               echo '';
                               // echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
                            else
                                // echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), ucfirst(implode(" ",explode("-",trim(str_replace(get_post_meta($item['variation_id'], 'attribute_pa_advertiser', true)."-","",get_post_meta($item['variation_id'], 'attribute_pa_advertiser', true)))))).' - '.get_the_title($item['product_id'])), $item );
									// print_r( $item['product_id'] );
									$product_permalink = get_permalink( $item['product_id'] );
									if( isset($item['variation_id']) && !empty($item['variation_id']) ) {
										$product_permalink .= '?varid='. $item['variation_id'];
									} 
									
									$merchant_name_by_var = '';
									$merchant_name = get_the_terms($_product->get_parent_id(), 'merchant');
									foreach( $merchant_name as $term ) {
										if( $term->parent == 0 ) {
											$merchant_name_by_var = $term->name;
										}
									}

									list($product_name, $outlet) = explode( '-', $item[ 'name' ] );
									
									$title = change_promotion_title( $product_name, $item['variation_id'] );

									// $title = $product_name;
									
                                    // echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', $product_permalink, str_replace( '-', ' ', get_post_meta($item['variation_id'])['attribute_pa_advertiser'][0] ) .' - '.get_the_title($item['product_id'])), $item );
									
                                    echo apply_filters( 'woocommerce_order_item_name', sprintf('<a href="%s">%s</a><p><b>Provider: </b>%s</p>', esc_url($product_permalink), $title, $merchant_name_by_var), $item );
   //echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), get_the_title($item['product_id'])), $item );
                                 //echo get_the_title($item['product_id']);
             //print_r (get_post_meta($item['variation_id']));
 
 
                             
 
                             
 
                             echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );

                            $item_meta->display();

                            if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

                                $download_files = $order->get_item_downloads( $item );
                                $i              = 0;
                                $links          = array();

                                foreach ( $download_files as $download_id => $file ) {
                                    $i++;

                                    $links[] = '<small><a href="' . esc_url( $file['download_url'] ) . '">' . sprintf( __( 'Download file%s', 'woocommerce' ), ( count( $download_files ) > 1 ? ' ' . $i . ': ' : ': ' ) ) . esc_html( $file['name'] ) . '</a></small>';
                                }

                                echo '<br/>' . implode( '<br/>', $links );
                            }
                        ?>
                    </td>
                    <td class="product-total">
                        <?php echo 'S'.$order->get_formatted_line_subtotal( $item ); ?>
                    </td>
                </tr>
                <?php

                if ( $order->has_status( array( 'completed', 'processing' ) ) && ( $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) ) {
                    ?>
                    <tr class="product-purchase-note">
                        <td colspan="3"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
                    </tr>
                    <?php
                }
            }
        }

        do_action( 'woocommerce_order_items_table', $order );
        ?>
    </tbody>
    <tfoot>
    <?php
        if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
    ?>
            <tr>
                <th scope="row"><?php echo $total['label']; ?> <?php echo ( strpos( $total['value'], '&#36;' ) === false )?'<span class="payment-method">'.$total['value'].'</span>':''; ?></th>
                <td<?php echo ( strpos( $total['value'], '&#36;' ) !== false )?' class="total-col"':''; ?>><?php echo ( strpos( $total['value'], '&#36;' ) !== false )?'S'.$total['value']:'-'; ?></td>
            </tr>
    <?php
        endforeach;
    ?>
    </tfoot>
</table>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>