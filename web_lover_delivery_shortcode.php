<?php

/*
Plugin Name: 	WC delivery date Product and Short Code
Plugin URI:		https://simplerscript.com/
Description: A Plugin for  Show delivery date using short code <strong>[web_lover_delivery_date order_img='https://img.icons8.com/flat-round/64/000000/hand.png' dispatch_img='https://img.icons8.com/dusk/64/000000/truck.png' deliver_img='https://img.icons8.com/color/48/000000/gift--v3.png'] or [web_lover_delivery_date] </strong>
Author: Yeasir Arafat (arafat.dml@gmail.com | fiverr.com/web_lover)
Author URI: https://www.fiverr.com/web_lover
Version: 1.0
*/

// web_lover_get_my_saved_data_by_id

if(!function_exists('web_lover_load_my_script')){
	function web_lover_load_my_script(){

		# loading Styles

		wp_register_style('web_lover_popover_css', 'https://www.jqueryscript.net/demo/minimal-clean-popover/popover.css');
		wp_enqueue_style('web_lover_popover_css');

		wp_register_style('web_lover_wp_track_delivery', plugins_url('assets/css/wp_track_delivery.css', __FILE__));
		wp_enqueue_style('web_lover_wp_track_delivery');

		wp_register_style('web_lover_custom_css', plugins_url('assets/css/web_lover_custom.css', __FILE__));
		wp_enqueue_style('web_lover_custom_css');

		# end Loading Styles

		# loading Scripts

	    wp_register_script( 
	        'web_lover_popover', 
	        'https://www.jqueryscript.net/demo/minimal-clean-popover/popover.js',
	        array( 'jquery' )
	    );
	    wp_enqueue_script( 'web_lover_popover' );

	    wp_register_script( 
	        'web_lover_custom_js',
	        plugins_url('assets/js/web_lover_custom.js', __FILE__),
	        array( 'web_lover_popover' )
	    );
	    wp_enqueue_script( 'web_lover_custom_js' );

	   # end loading Scripts

	}
}

add_action('wp_enqueue_scripts', 'web_lover_load_my_script');
# End of loading script


#---------------------------------------------------------
# Start Wp Admin Related Code
#---------------------------------------------------------


# Set up some Settings Here
global $web_lover_delivery_short_code_table_name;
global $wpdb;
global $web_lover_delivery_short_code_table_heading;
global $web_lover_delivery_short_code_web_lover_page_link;

$web_lover_delivery_short_code_web_lover_page_link     = "wc_delivery_data_with_short_code";
$web_lover_delivery_short_code_table_name              = $wpdb->prefix . 'web_lover_delivery_short_codes';
$web_lover_delivery_short_code_table_heading           = "My Saved Product Data";

global $web_lover_delivery_short_code_add_edit_field_name_arr;

$web_lover_delivery_short_code_add_edit_field_name_arr = array(
    array(
        'name' => 'product_id',
        'type' => 'select',
    ),

    array(
        'name' => 'days_take_to_dispatch',
        'type' => 'number',
    ),

    array(
        'name' => 'days_take_to_deliver',
        'type' => 'number',
    ),

    array(
        'name' => 'deliver_cost',
        'type' => 'number',
    ),

    array(
        'name' => 'created_at',
        'type' => 'my_sql_date_time',
    ),

);

#--------------------------------------------------------------------


function web_lover_delivery_short_code_install() {
    global $wpdb;
    global $web_lover_delivery_short_code_table_name;
    
    $charset_collate = $wpdb->get_charset_collate();
    $db_name = $wpdb->dbname;

    $sql = "CREATE TABLE `$db_name`.`$web_lover_delivery_short_code_table_name` ( `id` INT NOT NULL AUTO_INCREMENT, `product_id` VARCHAR(255) NULL DEFAULT NULL , `days_take_to_dispatch` VARCHAR(255) NULL DEFAULT NULL , `days_take_to_deliver` VARCHAR(255) NULL DEFAULT NULL , `deliver_cost` VARCHAR(255) NULL DEFAULT NULL , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY id (id) ) ENGINE = InnoDB";

    // echo "$db_name <br/> $charset_collate <br/>";
    // echo $sql;

    // die();

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

}


register_activation_hook( __FILE__, 'web_lover_delivery_short_code_install' );

# End of Step 1: Creating the Require table


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function web_lover_menu_creation_for_delivery_short_code(){
	#  add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null )

    global $web_lover_delivery_short_code_web_lover_page_link;

    add_menu_page( 'WC delivery short code', 'WC delivery Data', 'edit_pages', "$web_lover_delivery_short_code_web_lover_page_link", 'web_lover_delivery_short_code_plugin_init' );
}
add_action('admin_menu', 'web_lover_menu_creation_for_delivery_short_code');

 
function web_lover_delivery_short_code_plugin_init(){

    # Flash Method ...
    if(isset($_SESSION['web_lover_msg'])){

      $flash_msg = $_SESSION['web_lover_msg'];

      echo <<<EOD
      "<div class='updated notice' style='margin-left: 18%; margin-right: 15%; text-align: center;'>
    <h3> <strong> $flash_msg </strong> </h3>
</div>"
EOD;

      unset($_SESSION['web_lover_msg']);
    }

    include(__DIR__."/includes/data_table.php");

} // end of a function 


#-----------------------------------------------------------
# End Wp Admin related Code
#-----------------------------------------------------------


function web_lover_get_my_saved_data_by_id( $product_id ){
	global $wpdb;
	global $web_lover_delivery_short_code_table_name;

	$res = $wpdb->get_row( "SELECT * FROM $web_lover_delivery_short_code_table_name where product_id=$product_id", OBJECT );

	return $res;
}

function web_lover_delivery_date_calculate($dispatch, $delivery){

	#------------------------------------------------------
	$dispatch_arr = explode("-", $dispatch);

	$dispatch_one = (int) $dispatch_arr[0];
	$dispatch_two = 0;
	
	if( isset($dispatch_arr[1]) ){
		$dispatch_two = (int) $dispatch_arr[1];
	}

	# -------------------------------------------------
	# End Dispatch Break down for calculating delivery
	# -------------------------------------------------

	# -------------------------------------------------
	# Delivery Break down for calculating correct delivery
	# -------------------------------------------------

	$delivery_arr = explode("-", $delivery);

	$delivery_one = (int) $delivery_arr[0];
	$delivery_two = 0;
	
	if( isset($delivery_arr[1]) ){
		$delivery_two = (int) $delivery_arr[1];
	}

	# Correct delivery one 
	$delivery_one += $dispatch_one;
	$delivery_two += $dispatch_two;

	$delivery = $delivery_one."-".$delivery_two;

	#-------------------------------------------------------

	return $delivery;
}

function web_lover_date_format($my_given_date_range){

	$today = date("Y-m-d");

	$dispatch = $my_given_date_range;

	$dispatch_arr = explode("-", $dispatch);

	$dispatch_one = (int) $dispatch_arr[0];
	$dispatch_one = date('Y-m-d', strtotime($today. " + ".$dispatch_one." days"));
	$dispatch_one = date('d', strtotime($dispatch_one));


	$dispatch_two =  date('M', strtotime($dispatch_one));
	if( isset( $dispatch_arr[1] ) ){
		$dispatch_two = (int) $dispatch_arr[1];
		$dispatch_two = date('Y-m-d', strtotime($today. " + ".$dispatch_two." days"));
		$dispatch_two = date('d M', strtotime($dispatch_two));

		$dispatch = $dispatch_one."-".$dispatch_two.".";
	}else{
		$dispatch = $dispatch_one." ".$dispatch_two.".";
	}

	return $dispatch;

}

function web_lover_get_dispatch_deliver_date($has_data){

	$today = date("Y-m-d");

	$my_formatted_data = array(
		'order_placed' => date('d M', strtotime($today)).".",
		'order_dispatches' => '',
		'order_delivered' => '',
		'cost_to_deliver' => "<span style='color: green;'>FREE</span>",
	);
	
	if( is_object( $has_data ) ){

		# -------------------------------------------------
		# Dispatch Break down for calculating delivery
		# -------------------------------------------------

		$dispatch_arr = explode("-", $has_data->days_take_to_dispatch);

		$dispatch_one = (int) $dispatch_arr[0];
		$dispatch_two = 0;
		
		if( isset($dispatch_arr[1]) ){
			$dispatch_two = (int) $dispatch_arr[1];
		}

		# -------------------------------------------------
		# End Dispatch Break down for calculating delivery
		# -------------------------------------------------

		# -------------------------------------------------
		# Delivery Break down for calculating correct delivery
		# -------------------------------------------------

		$delivery_arr = explode("-", $has_data->days_take_to_deliver);

		$delivery_one = (int) $delivery_arr[0];
		$delivery_two = 0;
		
		if( isset($delivery_arr[1]) ){
			$delivery_two = (int) $delivery_arr[1];
		}

		# Correct delivery one 
		$delivery_one += $dispatch_one;
		$delivery_two += $dispatch_two;

		$delivery = $delivery_one."-".$delivery_two;

		# ----------------------------------------------------------------
		# End Delivery Break down for calculating correct delivery
		# ---------------------------------------------------------------
		$order_placed =  date('d M', strtotime($today)).".";
		$dispatch = web_lover_date_format( $has_data->days_take_to_dispatch );
		$delivered 	  = web_lover_date_format($delivery);
		
		$cost_to_deliver = "<span style='color: green;'>FREE</span>";
		
		if( $has_data && $has_data->deliver_cost > 0){
		    
		    $cost_to_deliver = $has_data->deliver_cost;
		}
		

		$my_formatted_data = array(
			'order_placed' => $order_placed,
			'order_dispatches' => $dispatch,
			'order_delivered' => $delivered,
			'cost_to_deliver' => $cost_to_deliver,
		);

	}

	return $my_formatted_data;

}

function ppp($data){
	echo "<pre>";
	var_dump($data);
	echo "</pre>";
}



#----------------------------------------------------------
# Render the custom product field in cart and checkout
#----------------------------------------------------------


# ------------------------------------------------------------------
# Creating extra field in product page using wc-metabox
# ------------------------------------------------------------------

add_action( 'add_meta_boxes', 'web_lover_create_custom_meta_box' );
if ( ! function_exists( 'web_lover_create_custom_meta_box' ) )
{
    function web_lover_create_custom_meta_box()
    {
        add_meta_box(
            'custom_product_meta_box',
            __( 'Additional Product Information', 'cmb' ),
            'web_lover_add_custom_content_meta_box',
            'product',
            'normal',
            'default'
        );
    }
}


if ( ! function_exists( 'web_lover_add_custom_content_meta_box' ) ){
    function web_lover_add_custom_content_meta_box( $post ){
       
        echo '<div class="option_group"> <h3 style="text-align: center;"> WC Delivery Short Code Plugin Data <hr/></h3>'; 

        $product_id =  get_the_ID();

        $has_data = web_lover_get_my_saved_data_by_id($product_id);

        woocommerce_wp_text_input( array(
                'id'                => 'web_lover_wc_meta_box_id',
                'name'              => 'days_take_to_dispatch',
                'value'             => isset($has_data->days_take_to_dispatch) ? $has_data->days_take_to_dispatch : "1-2",
                'label'             => 'Days till dispatch',
        ) );

        woocommerce_wp_text_input( array(
                'id'                => 'web_lover_wc_meta_box_id',
                'name'              => 'days_take_to_deliver',
                'value'             => isset($has_data->days_take_to_deliver) ? $has_data->days_take_to_deliver : "2-3",
                'label'             => 'Days to deliver',
        ) );

        woocommerce_wp_text_input( array(
                'id'                => 'web_lover_wc_meta_box_id',
                'name'              => 'deliver_cost',
                'value'             => isset($has_data->deliver_cost) ? $has_data->deliver_cost : 3,
                'label'             => 'Delivery cost',
        ) );


        echo '</div>';

    }
}

# -------------------------------------------------
# Save the data of the Meta field
# -------------------------------------------------


add_action( 'save_post', 'web_lover_save_custom_content_meta_box', 10, 1 );
if ( ! function_exists( 'web_lover_save_custom_content_meta_box' ) )
{
    function web_lover_save_custom_content_meta_box( $post_id ) {

    	if( isset( $_POST['post_type'] ) && $_POST['post_type'] == "product" )
    	{

    		global $web_lover_delivery_short_code_table_name;
    		global $wpdb;

    		$data_array = array(
    			"product_id" => $post_id,
    			"days_take_to_dispatch" => esc_html($_POST['days_take_to_dispatch']),
    			"days_take_to_deliver"  => esc_html($_POST['days_take_to_deliver']),
    			"deliver_cost"			=> esc_html($_POST['deliver_cost']),
    		);

    		# If have already insert that data then update
    		$has_data = web_lover_get_my_saved_data_by_id($post_id);

    		if( $has_data ){
    			# update
    			$res = $wpdb->update( $web_lover_delivery_short_code_table_name, $data_array, array("product_id" => $post_id) );

    		}else{
    			# New data Create
    			$res = $wpdb->insert($web_lover_delivery_short_code_table_name, $data_array);
    		}

    	}else{
    		return $post_id;
    	}

    	

    }
}


# ------------------------------------------------------------------
# Creating extra field in product page using wc-metabox
# ------------------------------------------------------------------



add_filter( 'woocommerce_after_cart_item_name', 'web_lover_woo_cart_modify', 10, 2 );
function web_lover_woo_cart_modify( $cart_data, $cart_item ) 
{
	// ppp($cart_data);
	// ppp($cart_item);
	#------------------------------------------------

	$product_id = $cart_data["product_id"];

	// var_dump($product_id);
	$has_data = web_lover_get_my_saved_data_by_id($product_id);

	if($has_data){

		$cart_page_currency    = "";
		$delivered         = "12-16 Mar.";
		$cost_to_deliver   = "<span style='color: green;'>FREE</span>";
		$from_country 	  = "United Kingdom";

		$my_format_data = web_lover_get_dispatch_deliver_date($has_data);
		// pr($my_format_data);

		$delivered = $my_format_data["order_delivered"];

		if( $has_data && $has_data->deliver_cost ){
			$cart_page_currency    = get_woocommerce_currency();
			$cost_to_deliver   = $has_data->deliver_cost;
		}

		# ----------------------------------------
		# If the product is a variation Product
		#------------------------------------------

		# For Supporting Variation Producr
		if( $cart_data["variation_id"] ){
			$variation_id = $cart_data["variation_id"];

			$var_dispatch = get_post_meta($variation_id, "_text_field_1", true);
			$var_deliver = get_post_meta($variation_id, "_text_field_2", true);
			$var_cost = get_post_meta($variation_id, "_text_field_3", true);

			$var_deliver = web_lover_delivery_date_calculate($var_dispatch, $var_deliver);
			$var_dispatch = web_lover_date_format($var_dispatch);
			$var_deliver = web_lover_date_format($var_deliver);

			# set The New Data Here
			$delivered 		   = $var_deliver;
			$cost_to_deliver   = $var_cost;

			# end setting the new data Here

		}

		#------------------------------------------
		# End of Variation Product Checking
		# -----------------------------------------


		$estimate_arrival_popover_msg = "The estimated delivery date is based on your purchase date, the recipient's location, the seller's processing time and location, and the delivery company. Other factors — like placing an order on a weekend or a bank holiday — may end up pushing the arrival of your item beyond the estimated delivery date. It's our hope that your item gets where it's going as soon as possible, but given the factors involved, this is only an estimate.";

		# ---------------------------------------------
		# If it is cart/checkout page
			echo  $cart_data_to_append =  "
			 	<div>
			 	   <div class='wt-mb-xs-2 wt-mb-lg-1'>
		 	      <div class='wt-text-body-01 wt-line-height-tight' style='display:none;margin-left: 38px; margin-top: 25px;'>
			 	         Delivery:
			 	         <span class='monetary'> <span class='money'><span class='currency-symbol'> $cart_page_currency </span><span class='currency-value'>$cost_to_deliver</span></span> </span>
			 	      </div>
			 	   </div>
			 	   <ul class='wt-list-unstyled wt-text-gray wt-text-caption'>
			 	      <li style='border-bottom: none;'>
			 	         <div class='wt-popover estimate_arrival_popover' data-wt-popover='' data-popover='edd-policy' style='margin-bottom: 8px;'>
			 	            Estimated delivery:
			 	            <a class='wt-popover__trigger wt-popover__trigger--underline' data-wt-popover-trigger='' tabindex='0' aria-describedby='popover-edd-policy-14599765' aria-disabled='true'>$delivered</a>
			 	            <div id='popover-edd-policy-14599765' class='wt-text-left-xs' role='tooltip' data-is-edd-policy-updated='false' data-popper-placement='bottom' style='position: absolute; left: 2px; top: 16px; margin: 0px;'>
			 	            </div>
			 	         </div>
			 	      </li>
			 	      <li>from $from_country</li>
			 	   </ul>
			 	   <div class='estimate_arrival_popover_msg'>$estimate_arrival_popover_msg</div>
			 	   <div id='order_placed_popover_msg'></div>
			 	   <div id='order_dispatches_popover_msg'></div>
			 	   <div id='order_delivered_popover_msg'></div>

			 	</div>
			 ";

	}

    
}

# ---------------------------------------------
# End rendering In cart Page
# ---------------------------------------------


#-----------------------------------------------------------

function ar_delivery_date_shortcode( $atts = [], $content = null, $tag = '' ){

	global $product;

	$my_atts  = shortcode_atts(
	        array(
	            'order_img'  => plugins_url('assets/img/hand.svg', __FILE__),
	            'dispatch_img' =>  plugins_url('assets/img/truck.svg', __FILE__),
	            'deliver_img' => plugins_url('assets/img/gift.svg', __FILE__),
	        ), $atts, $tag
	    );
	
	if( is_a($product, 'WC_Product') ) {

		# ------------------------------------------
		$product_page_currency = "";
		$order_placed      = "";
		$order_dispatches  = "";
		$delivered         = "";
		$cost_to_deliver   = "<span style='color: green;'>FREE</span>";
		# ---------------------------------------------
	       
	       $product_id = get_the_id();

	       $has_data = web_lover_get_my_saved_data_by_id($product_id);
	       $my_format_data = web_lover_get_dispatch_deliver_date($has_data);

	       // pr($has_data);
	       // pr($my_format_data);

	       # -----------------------------------------------
	       # If no data found then return false
	       # -----------------------------------------------
	       if(!$has_data){
	       		return '';
	       }
	       # -----------------------------------------------
	       # END If no data found then return false
	       # -----------------------------------------------


	       if( $has_data && $has_data->deliver_cost  ){

	       		if( is_numeric( $has_data->deliver_cost ) ){

	       			$product_page_currency = get_woocommerce_currency_symbol();
	       		}
	       		
	       		$cost_to_deliver   = $product_page_currency.$has_data->deliver_cost;

	       }

	       # ------------------------------------------
	       
	       $order_placed      = $my_format_data['order_placed'];
	       $order_dispatches  = $my_format_data['order_dispatches'];
	       $delivered         = $my_format_data['order_delivered'];

	       # ---------------------------------------------
	       $my_cnge = str_replace(".","", $delivered);

	       # Set the Message
	       $estimate_arrival_popover_msg = "The estimated delivery date is based on your purchase date, the recipient's location, the seller's processing time and location, and the delivery company. Other factors — like placing an order on a weekend or a bank holiday — may end up pushing the arrival of your item beyond the estimated delivery date. It's our hope that your item gets where it's going as soon as possible, but given the factors involved, this is only an estimate.";
	       $order_placed_popover_msg = "After you place your order, we will take 1-2 business days to prepare it for dispatch.";
	       $order_dispatches_popover_msg = "Your order is posted.";
	       $order_delivered_popover_msg = "Estimated to arrive at your doorstep<br/> {$my_cnge}!";

	       $order_img     = $my_atts['order_img'];
	       $dispatch_img  = $my_atts['dispatch_img'];
	       $deliver_img   = $my_atts['deliver_img'];
	       

	       return $web_lover_html_div = "
	       <div style='margin-top: 100px;'>
	          <div class='js-estimated-delivery wt-mb-xs-3
	             wt-grid__item-xs-12
	             wt-pr-xs-2' data-estimated-delivery=''>
	             <div class='wt-width-full' style='width: 500px; !important;'>
	                <div>
	                   <span class='wt-popover wt-popover--right estimate_arrival_popover' data-wt-popover='' data-popover='edd-policy'>
	                      <span data-wt-popover-trigger='' tabindex='0' class='wt-popover__trigger wt-popover__trigger--underline wt-display-inline-flex-xs wt-align-items-center' aria-describedby='edd-description'>
	                         <p class='wt-text-caption wt-text-gray' style='font-size:26px;'>Estimated arrival</p>
	                      </span>
	                   </span>
	                   <p class='wt-text-body-03 wt-mt-xs-1 wt-line-height-tight var_estimated_arrival' data-edd-absolute=''> $delivered </p>
	                </div>
	                <div class='wt-grid wt-mt-xs-3 wt-mb-md-3'>
	                   <div class='wt-grid__item-xs-4 fulfillment_timeline_date'>
	                      <div class='wt-popover wt-display-flex-xs wt-flex-direction-column-xs wt-align-items-flex-start' data-wt-popover='' style='cursor:help'>
	                         <div class='wt-display-flex-xs wt-flex-direction-row-xs wt-width-full'>
	                            <div>
	                               <span class='wt-icon wt-circle wt-bg-gray order_placed_popover' style='padding:3px'>
	                                  <img src='$order_img' alt='svg'>
	                               </span>
	                            </div>
	                            <div class='wt-flex-grow-xs-1 wt-pl-xs-1'>
	                               <div class='wt-width-full wt-height-half wt-bb-xs' style='border-width:2px !important'></div>
	                            </div>
	                         </div>
	                         <p class='wt-mt-xs-2 wt-text-black wt-text-caption-title wt-line-height-tight'>$order_placed </p>
	                         <span data-wt-popover-trigger='' tabindex='0' class='wt-popover__trigger wt-popover__trigger--underline wt-display-inline-flex-xs wt-align-items-center' aria-describedby='edd-description'>
	                         <span class='wt-mt-xs-1 wt-text-black wt-text-caption wt-line-height-tight wt-text-left-xs order_placed_popover'>Order placed</span>
	                         </span>
	                         <span id='edd-description' role='tooltip' style='position: absolute; left: -109px; top: 76px; margin: 0px;' data-popper-placement='bottom'>
	                            <span class='wt-popover__arrow' style='position: absolute; left: 143px;'></span>
	                         </span>
	                      </div>
	                   </div>
	                   <div class='wt-grid__item-xs-4 fulfillment_timeline_date'>
	                      <div class='wt-popover wt-display-flex-xs wt-flex-direction-column-xs wt-align-items-center' data-wt-popover='' style='cursor:help'>
	                         <div class='wt-display-flex-xs wt-flex-direction-row-xs wt-width-full'>
	                            <div class='wt-flex-grow-xs-1 wt-pr-xs-1'>
	                               <div class='wt-width-full wt-height-half wt-bb-xs' style='border-width:2px !important'></div>
	                            </div>
	                            <div>
	                               <span class='wt-icon wt-circle wt-bg-gray order_dispatches_popover' style='padding:3px'>
	                                 <img src='$dispatch_img' alt='svg'>
	                               </span>
	                            </div>
	                            <div class='wt-flex-grow-xs-1 wt-pl-xs-1'>
	                               <div class='wt-width-full wt-height-half wt-bb-xs' style='border-width:2px !important'></div>
	                            </div>
	                         </div>
	                         <p class='wt-mt-xs-2 wt-text-black wt-text-caption-title wt-line-height-tight var_order_dispatches'>$order_dispatches</p>
	                         <span data-wt-popover-trigger='' tabindex='0' class='wt-popover__trigger wt-popover__trigger--underline wt-display-inline-flex-xs wt-align-items-center' aria-describedby='edd-description'>
	                         <span class='wt-mt-xs-1 wt-text-black wt-text-caption wt-line-height-tight wt-text-center-xs order_dispatches_popover'>Order dispatches</span>
	                         </span>
	                         <span id='edd-description' role='tooltip'>
	                            <span class='wt-popover__arrow'></span>
	                         </span>
	                      </div>
	                   </div>
	                   <div class='wt-grid__item-xs-4 fulfillment_timeline_date'>
	                      <div class='wt-popover wt-display-flex-xs wt-flex-direction-column-xs wt-align-items-flex-end' data-wt-popover='' style='cursor:help'>
	                         <div class='wt-display-flex-xs wt-flex-direction-row-xs wt-width-full'>
	                            <div class='wt-flex-grow-xs-1 wt-pr-xs-1'>
	                               <div class='wt-width-full wt-height-half wt-bb-xs' style='border-width:2px !important'></div>
	                            </div>
	                            <div>
	                               <span class='wt-icon wt-circle wt-bg-gray order_delivered_popover' style='padding:3px'>
	                                  <img src='$deliver_img' alt='svg'>
	                               </span>
	                            </div>
	                         </div>
	                         <p class='wt-mt-xs-2 wt-text-black wt-text-caption-title wt-line-height-tight var_deliver'>$delivered</p>
	                         <span data-wt-popover-trigger='' tabindex='0' class='wt-popover__trigger wt-popover__trigger--underline wt-display-inline-flex-xs wt-align-items-center' aria-describedby='edd-description'>
	                         <span class='wt-mt-xs-1 wt-text-black wt-text-caption wt-line-height-tight wt-text-right-xs order_delivered_popover'>Delivered!</span>
	                         </span>
	                         <span id='edd-description' role='tooltip'>
	                            <p class='wt-text-caption'>
	                               
	                            </p>
	                            <span class='wt-popover__arrow'></span>
	                         </span>
	                      </div>
	                   </div>
	                </div>
	             </div>
	          </div>
	          <div data-estimated-shipping='' class='wt-grid__item-xs-6  wt-mb-md-5 wt-mb-xs-4'>
	             <div>
	                <span class='wt-text-caption wt-text-gray'>Cost to deliver</span>
	                <p class='wt-text-body-03 wt-mt-xs-1 wt-line-height-tight'><span class='currency-symbol'></span><span class='currency-value var_cost_to_deliver'>$cost_to_deliver
	                   </span></p>
	             </div>
	          </div>
	          <div class='estimate_arrival_popover_msg'>$estimate_arrival_popover_msg</div>
	          <div id='order_placed_popover_msg'>$order_placed_popover_msg</div>
	          <div id='order_dispatches_popover_msg'>$order_dispatches_popover_msg</div>
	          <div id='order_delivered_popover_msg'>$order_delivered_popover_msg</div>
	       </div>
	       ";
	 }


}


# -------------------------------------------------------------
#----------------------------------------------------------------

# New Version for supporting variable Product

# ------------------------------------------------------------

// Add a custom field to variation settings
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
function variation_settings_fields( $loop, $variation_data, $variation ) {
	
	# -------------------------------------------------------
	# Save Default value form here
	#-------------------------------------------------------
	
	$text_1 = get_post_meta( $variation->ID, '_text_field_1', true );
	
	if( !$text_1 ){
		add_post_meta( $variation->ID, '_text_field_1', "1-2" );
	}
	
	$text_2 = get_post_meta( $variation->ID, '_text_field_2', true );
	
	if( !$text_2 ){
        add_post_meta( $variation->ID, '_text_field_2', "2-3" );
	}
	
	$text_3 = get_post_meta( $variation->ID, '_text_field_3', true );
	
	if( !$text_3 ){
        add_post_meta( $variation->ID, '_text_field_3', "3" );
	}
	
	
	#--------------------------------------------------------
	# End saving default value from here
	#--------------------------------------------------------
	
	

    woocommerce_wp_text_input( array(
        'id'          => '_text_field_1[' . $loop . ']',
        'label'       => __( 'Days till dispatch', 'woocommerce' ),
        'placeholder' => '1-2',
        'desc_tip'    => 'true',
        'description' => __( 'Enter the custom value here.', 'woocommerce' ),
        'value'       => get_post_meta( $variation->ID, '_text_field_1', true ),
    ) );

    woocommerce_wp_text_input( array(
        'id'          => '_text_field_2[' . $loop . ']',
        'label'       => __( 'Days to deliver', 'woocommerce' ),
        'placeholder' => '2-3',
        'desc_tip'    => 'true',
        'description' => __( 'Enter the custom value here.', 'woocommerce' ),
        'value'       => get_post_meta( $variation->ID, '_text_field_2', true ),
    ) );

    woocommerce_wp_text_input( array(
        'id'          => '_text_field_3[' . $loop . ']',
        'label'       => __( 'Delivery cost', 'woocommerce' ),
        'placeholder' => '3',
        'desc_tip'    => 'true',
        'description' => __( 'Enter the custom value here.', 'woocommerce' ),
        'value'       => get_post_meta( $variation->ID, '_text_field_3', true ),
    ) );
}

// Save custom field value from variation settings
add_action( 'woocommerce_admin_process_variation_object', 'save_variation_settings_fields', 10, 2 );
function save_variation_settings_fields( $variation, $loop ) {
    if( isset($_POST['_text_field_1'][$loop]) ) {
    	
		
// 		echo "<pre>";
//     	print_r($loop);
//     	echo "</pre>";
		
//     	echo "<pre>";
//     	print_r($_POST);
//     	echo "</pre>";
		
        $variation->update_meta_data( '_text_field_1', esc_html( $_POST['_text_field_1'][$loop] ) );

        $variation->update_meta_data( '_text_field_2', esc_html( $_POST['_text_field_2'][$loop] ) );

        $variation->update_meta_data( '_text_field_3', esc_html( $_POST['_text_field_3'][$loop] ) );

    }
}

// Add variation custom field to single variable product form
// This will show in the Front-end



add_filter( 'woocommerce_available_variation', 'add_variation_custom_field_to_variable_form', 10, 3 );
function add_variation_custom_field_to_variable_form( $variation_data, $product, $variation ) {

	if(!isset($custom_config)){
		$custom_config = "";
	}

	$field_1 =  $variation->get_meta('_text_field_1');
	$field_2 =  $variation->get_meta('_text_field_2');
	$field_3 = $variation->get_meta('_text_field_3');
	
// 	var_dump($field_3);

	# Check If it is a Proper Numeric Field
	if( is_numeric( $field_3 ) ){
		$field_3 = get_woocommerce_currency_symbol().$field_3;
	}

	// ppp($field_1);
	// ppp($field_2);
	// 	ppp($field_3);

	#------------------------------------------------------

	$dispatch_arr = explode("-", $field_1);

	$dispatch_one = (int) $dispatch_arr[0];
	$dispatch_two = 0;
	
	if( isset($dispatch_arr[1]) ){
		$dispatch_two = (int) $dispatch_arr[1];
	}

	# -------------------------------------------------
	# End Dispatch Break down for calculating delivery
	# -------------------------------------------------

	# -------------------------------------------------
	# Delivery Break down for calculating correct delivery
	# -------------------------------------------------

	$delivery_arr = explode("-", $field_2);

	$delivery_one = (int) $delivery_arr[0];
	$delivery_two = 0;
	
	if( isset($delivery_arr[1]) ){
		$delivery_two = (int) $delivery_arr[1];
	}

	# Correct delivery one 
	$delivery_one += $dispatch_one;
	$delivery_two += $dispatch_two;

	$delivery = $delivery_one."-".$delivery_two;

	#-------------------------------------------------------

	$field_1 = web_lover_date_format($field_1);
	$field_2 = web_lover_date_format($delivery);

    $custom_config  .=  $field_1.":".$field_2.":".$field_3.":";
    $variation_data['text_field'] = $custom_config;


    return $variation_data;

    // echo "<script>alert('".$custom_config."');</script>";
}

add_action( 'woocommerce_product_additional_information', 'add_html_container_to_display_selected_variation_custom_field' );
function add_html_container_to_display_selected_variation_custom_field( $product ){
    echo '<div class="custom_variation-text-field"></div>';
}

// Display selected variation custom field value to product the tab
add_action( 'woocommerce_after_variations_form', 'display_selected_variation_custom_field_js' );
function display_selected_variation_custom_field_js(){
    ?>
    <script type="text/javascript">
    (function($){
        $('form.cart').on('show_variation', function(event, data) {
        	// alert(data);
//         	console.log(event);
//         	console.log(data);
//             $('.custom_variation-text-field').text(data.text_field);
            // $('.posted_in').html(data.text_field);

            var my_all_data = data.text_field;

            var my_res = my_all_data.split(":");

            // console.log(my_res);
            $(".var_estimated_arrival").html(my_res[1]);
            $(".var_order_dispatches").html(my_res[0]);
            $(".var_deliver").html(my_res[1]);
            $(".var_cost_to_deliver").html(my_res[2]);



        }).on('hide_variation', function(event) {
            $('.custom_variation-text-field').text('');
        });
    })(jQuery);
    </script>
    <?php
}

# -------------------------------------------------------------
#----------------------------------------------------------------
# End Supporting Variable Product
#--------------------------------------------------------------


function ar_delivery_date_init() {
    add_shortcode( 'web_lover_delivery_date', 'ar_delivery_date_shortcode' );
}
 
add_action( 'init', 'ar_delivery_date_init' );

// use 
// [web_lover_delivery_date order_img='' dispatch_img='' deliver_img='']
// [web_lover_delivery_date]

?>