<?php

// --- user reset mail --- //

$from_name = 'Support '.SITE_NAME;
// $from_mail = SITE_EMAIL;
$subject = 'Password Reset in '.SITE_NAME;
$message = '<html><head><title>Password Reset in '.SITE_NAME.'</title></head><body><p>Hello <b>'.$data['name'].'</b>!</p><p>At '.date("Y.n.d H:i:s").' A password reset message has been received in your name. If you did not send us your details, just ignore this message, otherwise follow the link below to reset your password and log in: </p><a href = "'.SITE_URL.'reset/go?id='.$data['id'].'&code='.$data['reset_key'].'">'.SITE_URL.'reset/go?id='.$data['id'].'&code='.$data['reset_key'].'</a>; The link is valid until <b><i>'.date("Y.n.d H:i:s", $data['reset_expires']).'.</i></b><p><p>An error?</p><p>Check the validity of the link. If you still cannot complete the password recovery, write to us: '.SITE_EMAIL.'</p></body></html>';

// echo $message; exit;