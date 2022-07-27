<?php
/*
Plugin Name: MPU Payment Gateway
Description: MPU payment gateway
Author: Khandaker Ikrama
Author URI: https://ikrama.info/
*/

add_filter( 'woocommerce_payment_gateways', 'muppay_add_gateway_class' );
  function muppay_add_gateway_class( $gateways ) {
  $gateways[] = 'WC_MUPPay_Gateway';
  return $gateways;
}
add_action( 'plugins_loaded', 'muppay_init_gateway_class' );
function muppay_init_gateway_class() {
  class WC_MUPPay_Gateway extends WC_Payment_Gateway {
    public function __construct() {
      $this->id = 'muppay';
      $this->icon = '';
      $this->has_fields = false;
      $this->method_title = 'MPUPay';
      $this->method_description = 'Accepts payments with the MPUPay Gateway for WooCommerce';
      $this->supports = array('products');
      $this->init_form_fields();
      $this->init_settings();
      $this->enabled = $this->get_option( 'enabled' );
      $this->title = $this->get_option( 'title' );
      $this->description = $this->get_option( 'description' );
      $this->clientId = $this->get_option( 'clientId' );
      $this->clientSecret = $this->get_option( 'clientSecret' );
      add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
//      add_action( 'woocommerce_pre_payment_complete', array( $this, 'process_payment' ) );
//      add_action( 'woocommerce_api_muppay_webhook_' . $this->id, array( $this, 'webhook' ) );
      add_action( 'woocommerce_receipt_'.$this->id, array( $this, 'generate_qr_code' ) );
      add_action( 'woocommerce_post_payment_complete', array( $this, 'capture_payment' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

      


    }

    public function init_form_fields(){
      $this->form_fields = array(
        'enabled' => array(
          'title'       => 'Enable/Disable',
          'label'       => 'Enable MPUPay',
          'type'        => 'checkbox',
          'description' => '',
          'default'     => 'no'
        ),
        'title' => array(
          'title'       => 'Title',
          'type'        => 'text',
          'description' => 'This controls the title which the user sees during checkout.',
          'default'     => 'MPUPay'
        ),
        'description' => array(
          'title'       => 'Description',
          'type'        => 'textarea',
          'description' => 'This controls the description which the user sees during checkout.',
          'default'     => 'Pay with the MPUPay payment gateway.'
        ),
        'clientId' => array(
          'title'       => 'App ID',
          'type'        => 'text',
          'description'       => 'Enter your MPUPay App ID'
        ),
        'clientSecret' => array(
          'title'       => 'App key',
          'type'        => 'text',
          'description'       => 'Enter your MPUPay App key'
        )
      );
    }

    public function process_payment( $order_id ) {
      global $woocommerce;
//      $endpoint = 'https://www.mpuecomuat.com/UAT/Payment/Payment/pay';

        $order = new WC_Order( $order_id );

        $secrectKey = "I5HBF43QIG8WU1ZEKJ161JFF21P08QTF";

        $invoiceNumber = '12345678901223';
        $amount = '000000001100';
        $currencryCode = "104";
        $marchantId = "201104001306414";
        $productDesc = "productDesc2";


      $message = __( 'Awaiting MPU Payment!', 'muppay' );
//      $muppay_items = array();
//      $muppay_item = array();
//      foreach ($items as $item) {
//        $muppay_item = array(
//          'Name' => $item['name'],
//          'Notes' => $item['quantity'],
//          'Amount' => $item['total']
//        );
//        $muppay_items[] = json_encode($muppay_item);
//      }

        // Generate Sign
        $full_string = array($invoiceNumber, $amount, $currencryCode, $marchantId, $productDesc);

//        echo 'string'.$full_string . '<br><br>';

        sort($full_string);
        $sortdata = implode($full_string);

        $signData = hash_hmac('sha1', $sortdata, $secrectKey, false);
        $hashValue = strtoupper($signData);

//        echo 'Hash'.$hashValue . '<br>';




//
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://www.mpuecomuat.com/UAT/Payment/Payment/pay',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_HEADER => true,
//            CURLOPT_POSTFIELDS =>'{
//              "Request": {
//                "merchantID": "'.$marchantId.'",
//                "invoiceNo": "'.$invoiceNumber.'",
//                "productDesc": "'.$productDesc.'",
//                "amount": "'.$amount.'",
//                "currencyCode": "'.$currencryCode.'",
//                "hashValue": "'.$hashValue.'"
//                }
//              }
//            }',
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//
//        curl_close($curl);
//
//
//        echo $response;

//        $mup_admin_data = json_encode($response, true);
//        $object_encoded = json_encode($response);
//        $object_decoded = json_decode( $response, true );

//        var_dump($object_decoded);

//        $mup_admin_data[] = '';
//
//        foreach ($object_decoded as $obj) {
//            $mup_admin_data = $obj;
//        }





        //update_post_meta($order_id, '_mup_payment_method', mysql_real_escape_string($_POST['mup_payment_method']));
        // update meta
        update_post_meta( $order->get_id(), '_mup_payment_order_paid', 'no' );

//        if ( $mup_admin_data['result'] == 0 ) {
//            add_post_meta( $order->get_id(), '_prepay_id', $mup_admin_data['prepay_id'] );
//            add_post_meta( $order->get_id(), '_qr_code', $mup_admin_data['qrCode'] );
//            $message .= '<br />' . sprintf( __( 'QR Code: %s', 'muppay' ), $mup_admin_data['qrCode'] );
////            $message .= '<br />' . sprintf( __( 'Prepay ID: %s', 'muppay' ), $mup_admin_data['prepay_id'] );
////            $message .= '<br />' . sprintf( __( 'nonce_str: %s', 'muppay' ), $mup_admin_data['nonce_str'] );
////            $message .= '<br />' . sprintf( __( 'sign: %s', 'muppay' ), $mup_admin_data['sign'] );
//        }
//
//        // add some order notes
        $order->add_order_note( apply_filters( 'mup_process_payment_note', $message, $order ), false );

        if ( apply_filters( 'mup_payment_empty_cart', false ) ) {
            // Empty cart
            WC()->cart->empty_cart();
        }

        do_action( 'mup_after_payment_init', $order_id, $order );


        return array(
        'result' => 'success',
//        'redirect' => 'https://www.mpuecomuat.com/UAT/Payment/Payment/pay',
//        'redirect' => $order->get_checkout_payment_url( true ),
//        'redirect' => add_query_arg('order', $order_id, add_query_arg('key', $order->order_key, $order->get_checkout_payment_url(true))),
//        'redirect' => $this->get_return_url( $order ),
        'redirect'	=> apply_filters( 'mup_process_payment_redirect', $order->get_checkout_payment_url( true ), $order )
      );


    }




      public function generate_qr_code( $order_id ) {
          $order = wc_get_order( $order_id );

          $marchantId = "201104001306414";
//          echo 'marchantId: ' . $marchantId . '<br>';

          $secrectKey = "I5HBF43QIG8WU1ZEKJ161JFF21P08QTF";
//          echo 'secrectKey: ' . $secrectKey . '<br>';

          $currencryCode = "104";
//          echo 'currencryCode: ' . $currencryCode . '<br>';

//          $invoiceNumber = '12345678901228';
//          echo 'InvoiceNo: ' . $invoiceNumber . '<br>';
          $length = 14;
          $invoiceNumber = substr(str_repeat(0, $length).$order_id, - $length);
          // $invoiceNumber = $order_id;

//          $amount = '000000200000';
//          echo 'Amount: ' . $amount . '<br>';
          $number = $order->get_total();
          $price = $number * 100;
          $length2 = 12;
          $amount = substr(str_repeat(0, $length2).$price, - $length2);

          $productDesc = "productDesc2";
//          echo 'productDesc: ' . $productDesc . '<br>';
//          $productDesc = $order->get_items();
          // Generate Sign
          $full_string = array($invoiceNumber, $amount, $currencryCode, $marchantId, $productDesc);

          $full_string1 = $invoiceNumber . $amount . $currencryCode . $marchantId . $productDesc;

          $full_string22cc = $amount . $currencryCode . $invoiceNumber . $marchantId . $productDesc;

          // echo 'full_string: ' . $full_string . '<br>';

      //  echo 'string'.$full_string . '<br><br>';

          sort($full_string);
          $sortdata = implode($full_string);

//          echo 'sortdata: ' . $sortdata . '<br>';

//          $signData = hash_hmac('sha1', $sortdata, $secrectKey, false);
          $signData = hash_hmac('sha1', $full_string1, $secrectKey, false);

          $hashValue = strtoupper($signData);
//          echo 'hashValue: ' . $hashValue . '<br>';


          ?>

          <div class="qr-code-area ">
              <form method="post" action="https://www.mpuecomuat.com/UAT/Payment/Payment/pay">
                  <input type="hidden" id="merchantID" name="merchantID" value="<?php echo $marchantId; ?>"/>
                  <input type="hidden" id="invoiceNo" name="invoiceNo" value="<?php echo $invoiceNumber; ?>"/>
                  <input type="hidden" id="productDesc" name="productDesc" value="<?php echo $productDesc; ?>"/>
                  <input type="hidden" id="amount" name="amount" value="<?php echo $amount; ?>"/>
                  <input type="hidden" id="currencyCode" name="currencyCode" value="<?php echo $currencryCode; ?>"/>
                  <input type="hidden" id="hashValue" name="hashValue" value="<?php echo $hashValue; ?>"/>

                  <input style="width: 60%;" class="muppay-btn single_add_to_cart_button button alt" type="submit" name="submit" value="Pay Now using MPUPay">
              </form>
          </div>

          <?php

      }




//    public function capture_payment($order_id) {

//        $order = wc_get_order( $order_id );

       
          

// //        $marchantId = "201104001306414";
// // //          echo 'marchantId: ' . $marchantId . '<br>';

// //        $secrectKey = "I5HBF43QIG8WU1ZEKJ161JFF21P08QTF";
// // //          echo 'secrectKey: ' . $secrectKey . '<br>';

// //        $currencryCode = "104";
// // //          echo 'currencryCode: ' . $currencryCode . '<br>';

// // //          $invoiceNumber = '12345678901228';
// // //          echo 'InvoiceNo: ' . $invoiceNumber . '<br>';
// //        $length = 14;
// //        $invoiceNumber = substr(str_repeat(1, $length).$order_id, - $length);

// // //          $amount = '000000200000';
// // //          echo 'Amount: ' . $amount . '<br>';
// //        $number = $order->get_total();
// //        $price = $number * 100;
// //        $length2 = 12;
// //        $amount = substr(str_repeat(0, $length2).$price, - $length2);

// //        $productDesc = "productDesc2";
// // //          echo 'productDesc: ' . $productDesc . '<br>';
// // //          $productDesc = $order->get_items();
// //        // Generate Sign
// //        $full_string = array($invoiceNumber, $amount, $currencryCode, $marchantId, $productDesc);

// //        $full_string22cc = $amount . $currencryCode . $invoiceNumber . $marchantId . $productDesc;

// //        // echo 'full_string: ' . $full_string . '<br>';

// //        //  echo 'string'.$full_string . '<br><br>';

// //        sort($full_string);
// //        $sortdata = implode($full_string);

// // //          echo 'sortdata: ' . $sortdata . '<br>';

// // //          $signData = hash_hmac('sha1', $sortdata, $secrectKey, false);
// //        $signData = hash_hmac('sha1', $full_string22cc, $secrectKey, false);

// //        $hashValue = strtoupper($signData);
// // //          echo 'hashValue: ' . $hashValue . '<br>';



// // //          $url = 'https://www.mpuecomuat.com/UAT/Payment/Action/api';
// //        $url = 'https://www.mpuecomuat.com/UAT/Payment/Action/api?merchantID='.$marchantId.'& invoiceNo='.$invoiceNumber.'&actionType=I&hashValue='.$hashValue.'';

// // //          $args = array(
// // //              'timeout'     => 300,
// // //              'headers' => array(
// // //                  'Authorization' => ERP_AUTHORIZATION,
// // //              )
// // //          );

// // //          $response = wp_remote_get( $url, $args );
// //        $response = wp_remote_get( $url );
// //        $response_code = wp_remote_retrieve_response_code( $response );


// //        echo $response;
// //        echo $response_code;


// //        $object_decoded = json_decode( $response, true );

// // //        var_dump($object_decoded);

// //        $mup_admin_data[] = '';

// //        foreach ($object_decoded as $obj) {
// //            $mup_admin_data = $obj;
// //        }

// //        // echo $mup_admin_data['trade_status'];


// //        if(isset($_POST['payment_complete']) && $mup_admin_data['trade_status'] == 'PAY_SUCCESS') {

// //            // echo 'yes';

// //            // update meta
// //            update_post_meta( $order->get_id(), '_mup_payment_order_paid', 'yes' );

// //            add_post_meta( $order->get_id(), '_mup_order_id', $mup_admin_data['mup_order_id'] );
// //            add_post_meta( $order->get_id(), '_mup_order_status', $mup_admin_data['mup_order_status'] );
// //            $message = '<br />' . sprintf( __( 'MUPPay Order ID: %s', 'muppay' ), $mup_admin_data['mm_order_id'] );
// //            $message .= '<br />' . sprintf( __( 'Payment Status: %s', 'muppay' ), $mup_admin_data['trade_status'] );
// //            $message .= '<br />' . sprintf( __( 'Payment Time: %s', 'muppay' ), $mup_admin_data['pay_success_time'] );

// //            // add some order notes
// //            $order->add_order_note( apply_filters( 'mup_process_payment_note', $message, $order ), false );

// //            if ( apply_filters( 'mup_payment_empty_cart', true ) ) {
// //                // Empty cart
// //                WC()->cart->empty_cart();
// //            }


// //            // do_action( 'mup_after_payment_init', $order_id, $order );

// //            // $order = wc_get_order( $order_id );
// //            $order->payment_complete();
// //            wc_reduce_stock_levels($order_id);


//            $order_received_url = wc_get_endpoint_url( 'order-received', $order->get_id(), wc_get_checkout_url() );
//            $order_received_url = add_query_arg( 'key', $order->get_order_key(), $order_received_url );

//            // echo $order_received_url;


// //             return array(
// //                 'result' => 'success',
// // //                'redirect' => $response,
// //                 // 'redirect' => $this->get_return_url( $order )
// //                   'redirect' => $order_received_url
// //             );

//            header('Location: '.$order_received_url);

//        } 
//        else {
//            // echo 'No';
//            return array(
//                'result' => 'success',
// //                'redirect' => $response,
//                'redirect'	=> apply_filters( 'mup_process_payment_redirect', $order->get_checkout_payment_url( true ), $order )
//            );
//        }

  //  }
















  }
}