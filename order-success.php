<?php


define( 'WP_USE_THEMES', false );

/** Loads the WordPress Environment and Template */
require __DIR__ . '/../../../wp-blog-header.php';



global $woocommerce;

// var_dump($woocommerce);
// global $wpdb;

// $order = new WC_Order($post->ID);

// //to escape # from order id 

// $order_id = trim(str_replace('#', '', $order->get_order_number()));



// var_dump($_GET);

// $json_post = file_put_contents( __DIR__ . '/post_data.json', json_encode( $_POST ));


// $json_get = file_put_contents( __DIR__ . '/get_data.json', json_encode( $_GET ));

// var_dump($json_post);



// Read the JSON file 
$json = file_get_contents( __DIR__ .'/post_data.json');
// var_dump($json);          
// Decode the JSON file
$json_data = json_decode($json,true);


// $_POST = $json_data;
// var_dump($json_data);

if(!empty($_POST)) {
  /// check status
  $invoiceNo = $_POST['invoiceNo'];
  $orderId = ltrim($invoiceNo, "0");
  
  $status = $_POST['status'];



  // var_dump($order = new WC_Order($orderId));

  // success
  if($status == 'AP') {




    $order = new WC_Order($orderId);
    
    // if (!empty($order)) {
    
    //   $order->update_status( 'completed' );
    
    // }




      
      // update meta

      update_post_meta( $order->get_id(), '_mup_payment_order_paid', 'yes' );

      // add_post_meta( $order->get_id(), '_mup_invoice_no', $_POST['invoiceNo'] );

      // add_post_meta( $order->get_id(), '_mup_tran_ref', $_POST['tranRef'] );

      $message = '<br />' . sprintf( __( 'MPUPay Invoice No: %s', 'muppay' ), $_POST['invoiceNo'] );

      $message .= '<br />' . sprintf( __( 'MPUPay tran Ref: %s', 'muppay' ), $_POST['tranRef'] );

      $message .= '<br />' . sprintf( __( 'Payment Status: %s', 'muppay' ), $_POST['failReason'] );

      $message .= '<br />' . sprintf( __( 'Payment Time: %s', 'muppay' ), $_POST['dateTime'] );

      $message .= '<br />' . sprintf( __( 'Hash Value: %s', 'muppay' ), $_POST['hashValue'] );



      // add some order notes

      $order->add_order_note( apply_filters( 'mup_process_payment_note', $message, $order ), false );


      // $order = wc_get_order( $order_id );

      $order->payment_complete();

      wc_reduce_stock_levels($orderId);














     
  }
}





// header('Location: https://misfit-test.com/lifeplusmm/my-account-mm/orders/');



//echo "Hello";

//global $woocommerce, $post;

//$order_id =  $post->ID;

//$order = new WC_Order( $order_id );

//$order_received_url = wc_get_endpoint_url( 'order-received', $order->get_id(), wc_get_checkout_url() );

//$order_received_url = add_query_arg( 'key', $order->get_order_key(), $order_received_url );

//header('Location: '.$order_received_url);



















// //add_action( 'woocommerce_thankyou', 'capture_payment');

// //add_action( 'woocommerce_payment_complete', 'capture_payment' );

// function capture_payment($order_id) {



// //    global $woocommerce, $post;

// //

// //    $order_id =  $post->ID;

// //

// //echo $order_id;



//     $order = wc_get_order( $order_id );



//     $marchantId = "201104001306414";

// //          echo 'marchantId: ' . $marchantId . '<br>';



//     $secrectKey = "I5HBF43QIG8WU1ZEKJ161JFF21P08QTF";

// //          echo 'secrectKey: ' . $secrectKey . '<br>';



//     $currencryCode = "104";

// //          echo 'currencryCode: ' . $currencryCode . '<br>';



// //          $invoiceNumber = '12345678901228';

// //          echo 'InvoiceNo: ' . $invoiceNumber . '<br>';

//     $length = 14;

//     $invoiceNumber = substr(str_repeat(1, $length).$order_id, - $length);



// //          $amount = '000000200000';

// //          echo 'Amount: ' . $amount . '<br>';

//     $number = $order->get_total();

//     $price = $number * 100;

//     $length2 = 12;

//     $amount = substr(str_repeat(0, $length2).$price, - $length2);



//     $productDesc = "productDesc2";

// //          echo 'productDesc: ' . $productDesc . '<br>';

// //          $productDesc = $order->get_items();

//     // Generate Sign

//     $full_string = array($invoiceNumber, $amount, $currencryCode, $marchantId, $productDesc);



//     $full_string22cc = $amount . $currencryCode . $invoiceNumber . $marchantId . $productDesc;



//     // echo 'full_string: ' . $full_string . '<br>';



//     //  echo 'string'.$full_string . '<br><br>';



//     sort($full_string);

//     $sortdata = implode($full_string);



// //          echo 'sortdata: ' . $sortdata . '<br>';



// //          $signData = hash_hmac('sha1', $sortdata, $secrectKey, false);

//     $signData = hash_hmac('sha1', $full_string22cc, $secrectKey, false);



//     $hashValue = strtoupper($signData);

// //          echo 'hashValue: ' . $hashValue . '<br>';







// //          $url = 'https://www.mpuecomuat.com/UAT/Payment/Action/api';

//     $url = 'https://www.mpuecomuat.com/UAT/Payment/Action/api?merchantID='.$marchantId.'& invoiceNo='.$invoiceNumber.'&actionType=I&hashValue='.$hashValue.'';



// //          $args = array(

// //              'timeout'     => 300,

// //              'headers' => array(

// //                  'Authorization' => ERP_AUTHORIZATION,

// //              )

// //          );



// //          $response = wp_remote_get( $url, $args );

//     $response = wp_remote_get( $url );

//     $response_code = wp_remote_retrieve_response_code( $response );





//     echo $response;

//     echo $response_code;





//     $object_decoded = json_decode( $response, true );



// //        var_dump($object_decoded);



//     $mup_admin_data[] = '';



//     foreach ($object_decoded as $obj) {

//         $mup_admin_data = $obj;

//     }



//     // echo $mup_admin_data['trade_status'];





//     if(isset($_POST['payment_complete']) && $mup_admin_data['trade_status'] == 'PAY_SUCCESS') {



//         // echo 'yes';



//         // update meta

//         update_post_meta( $order->get_id(), '_mup_payment_order_paid', 'yes' );



//         add_post_meta( $order->get_id(), '_mup_order_id', $mup_admin_data['mup_order_id'] );

//         add_post_meta( $order->get_id(), '_mup_order_status', $mup_admin_data['mup_order_status'] );

//         $message = '<br />' . sprintf( __( 'MUPPay Order ID: %s', 'muppay' ), $mup_admin_data['mm_order_id'] );

//         $message .= '<br />' . sprintf( __( 'Payment Status: %s', 'muppay' ), $mup_admin_data['trade_status'] );

//         $message .= '<br />' . sprintf( __( 'Payment Time: %s', 'muppay' ), $mup_admin_data['pay_success_time'] );



//         // add some order notes

//         $order->add_order_note( apply_filters( 'mup_process_payment_note', $message, $order ), false );



//         if ( apply_filters( 'mup_payment_empty_cart', true ) ) {

//             // Empty cart

//             WC()->cart->empty_cart();

//         }





//         // do_action( 'mup_after_payment_init', $order_id, $order );



//         // $order = wc_get_order( $order_id );

//         $order->payment_complete();

//         wc_reduce_stock_levels($order_id);





//         $order_received_url = wc_get_endpoint_url( 'order-received', $order->get_id(), wc_get_checkout_url() );

//         $order_received_url = add_query_arg( 'key', $order->get_order_key(), $order_received_url );



//         // echo $order_received_url;





// //             return array(

// //                 'result' => 'success',

// // //                'redirect' => $response,

// //                 // 'redirect' => $this->get_return_url( $order )

// //                   'redirect' => $order_received_url

// //             );



//         header('Location: '.$order_received_url);



//     } else {

//         // echo 'No';

//         return array(

//             'result' => 'success',

// //                'redirect' => $response,

//             'redirect'	=> apply_filters( 'mup_process_payment_redirect', $order->get_checkout_payment_url( true ), $order )

//         );

//     }







// }