<?php

require_once __DIR__ . '/functions.php';

// حذف جميع بيانات الجلسة
$_SESSION = [];

// حذف Cookie الخاصة بالجلسة
if (ini_get('session.use_cookies')) {

    $p = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => 'Lax'
        ]
    );
}

// إنهاء الجلسة
session_destroy();

// العودة إلى الصفحة الرئيسية
redirect('index.php');
