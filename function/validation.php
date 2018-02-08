<?php

function getRandomNum() {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $hash_key = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < 11; $i ++) {
        $hash_key .= $characters [mt_rand(0, $max)];
    }
    return $hash_key;
}
function getPasswordNum() {
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $hash_key = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < 7; $i ++) {
        $hash_key .= $characters [mt_rand(0, $max)];
    }
    return $hash_key;
}
function sendMail($to, $password) {
    $subject = "Forgot Password";
    $message = "Your Password: " .$password;
    $headers = "From: deeptha@siliconkraft.com";

    if (mail($to, $subject, $message, $headers)) {
        return 1;
    } else {
        return 0;
    }
}

?>
