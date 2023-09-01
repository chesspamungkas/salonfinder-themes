<?php
namespace DV\shortcodes\ProductDetails;

use DateTimeZone;
use DV\core\RenderShortCode;
use DV\core\models\Outlet;

class ProductDetails extends RenderShortCode {
    public static function init( $args ) {
        $model = new ProductDetails();
        $model->args = shortcode_atts( array(
        //   'header' => "Header",
        //   'subheader'=>''
        ), $args );
        $model->generate();
    }

    function get_how_to_redeem() {
        $slug = 'how-to-redeem';
        $args = array(
            'pagename'  => $slug,
        );

        $page = new \WP_Query( $args );

        return $page->queried_object->post_content;
    }

    function get_operating_hours( $metaKey ) {
        $days = array(
            'monday'        => 'Mon',
            'tuesday'       => 'Tue',
            'wednesday'     => 'Wed',
            'thursday'      => 'Thur',
            'friday'        => 'Fri',
            'saturday'      => 'Sat',
            'sunday'        => 'Sun'
        );

        $outlet_oper = get_field( 'outlet_operating_hours', $metaKey );

        // print_r( $outlet_oper );

        $content = '';
		
        $j = 0;
        $startDay = '';
        $endDay = '';
        $operationHour = '';

		if( is_array( $outlet_oper ) ) {
            for( $i = 0; $i < count( $outlet_oper ); $i++ ) {
                if( $j == 0 ) {
                    $startDay = $days[ strtolower( $outlet_oper[$i]["day"] ) ];
                }

                if( $outlet_oper[$i]["is_outlet_closed"] ) {
                    if( $outlet_oper[$i+1]["is_outlet_closed"] && $i+1 < count( $outlet_oper ) ) {
                        $j++;
                        continue;
                    } else {
                        if( $j != 0 ) {
                            $endDay = $days[ strtolower( $outlet_oper[$i]["day"] ) ];
                            $content .= '<p>' . $startDay . '-' . $endDay . ': Closed</p>';
                        } else {
                            $content .= '<p>' . $startDay . ': Closed</p>';
                        }

                        $j = 0;
                        continue;
                    }
                } else {
                    if( $i+1 < count( $outlet_oper ) && $outlet_oper[$i]["open_time"] == $outlet_oper[$i+1]["open_time"] && $outlet_oper[$i]["close_time"] == $outlet_oper[$i+1]["close_time"] && !$outlet_oper[$i+1]["is_outlet_closed"] ) {
                        $j++;
                        continue;
                    } else {
                        $endDay = $days[ strtolower(  $outlet_oper[$i]["day"] ) ];
                        $time = wp_date( get_option('time_format'), strtotime( $outlet_oper[$i]["open_time"] ) + 8*3600 ) . ' to ' . wp_date( get_option('time_format'), strtotime( $outlet_oper[$i]["close_time"] ) + 8*3600 );

                        if( $j != 0 ) {
                            $content .= '<p>' . $startDay . '-' . $endDay . ': ' . $time . '</p>';
                        } else {
                            $content .= '<p>' . $startDay . ': ' . $time . '</p>';
                        }

                        $j = 0;
                        continue;
                    }
                }
            }
		}

        return $content;
    }

    public function generate() {
        global $product;

        $product = new \DV\core\models\Product( $product );

        $merchant = $product->getMerchant();

        echo $this->render( 'ProductDetails/display', [
            'product'       => $product,
            'merchant'      => $merchant
        ] );
    }
}