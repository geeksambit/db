<?php

// API access key from Google API's Console
//sendFcm("fusEDqLtC_8:APA91bF_6qxfHlWE9Q9R6w4TcBjGMb5rP-BAPtf31Tu_wvAylpUZitVvVQ5CDxQtaghAVQ-AChB0zOrXxs4pLiIvS2eSAAtAyquEQb76tBVI8YyUg16DfiAd7oXN8U_zDFpn7vfhiEIj", "Hello");

function sendFcm($fcmId, $message) {
    
    define('API_ACCESS_KEY', 'AIzaSyAf7IQ2JNkNuuphd5755PW8wfCb8Vr5Kj4');
     $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
        
        $fields = array(
            'to' => $fcmId,
            'notification' => array('title' => 'Order Details', 'body' => $message),
            'data' => array('message' => $message)
        );
 
        $headers = array(
            'Authorization:key=' . API_ACCESS_KEY,
            'Content-Type:application/json'
        );		
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
        $result = curl_exec($ch);
        curl_close($ch);
    //echo $result;
}
?>

