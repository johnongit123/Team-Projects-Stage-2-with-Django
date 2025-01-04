<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 1800,
        'path' => '/',
        'secure' => true
    ]);
    session_start();
}
?>