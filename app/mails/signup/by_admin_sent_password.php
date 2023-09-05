<?php

// --- Create account by admin from admin panel. Sent random ganarate password to user --- //

$user = $data;

$from_name = 'Support '.SITE_NAME;
$subject = 'Create profile at '.SITE_NAME;
$message = '<html><head><title>Create profile at '.SITE_NAME.'</title></head><body>'.
			'<p>Hi, <b>'.$user->name.'</b>!</p><p>At '.date("Y.n.d H:i:s", $user->registered).' to your e-mail <b>'.$user->email.' created account in site '.SITE_URL.
			'. Password: <b>'.$user->password.'</b></p><p><a href="'.SITE_URL.'login">Login to admin panel</a></p></body></html>';

// echo $message;
// exit;