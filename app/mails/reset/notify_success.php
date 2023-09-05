<?php

// --- user info reset mail --- //

$from_name = 'Support '.SITE_NAME;
$subject = 'Password Reset in '.SITE_NAME;
$message = '<html><head><title>Password Reset in '.SITE_NAME.'</title></head><body><p>Hello <b>'.$data['name'].'</b>!</p><p>At '.date("Y.n.d H:i:s").' the password to your user profile on the site has been changed '.SITE_NAME.'. </p><p>This is an informational message for the security of your data. If you have not changed your password on the site, contact the administration by mail as soon as possible '.SITE_EMAIL.'. Otherwise, just ignore this letter.</p><p>Best regards, administration '.SITE_NAME.'</p></body></html>';

// echo $message; exit;