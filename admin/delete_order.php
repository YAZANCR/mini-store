<?php

require_once __DIR__ . '/../functions.php';


// التأكد من أن المستخدم مسؤول

require_admin();




if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}


// التحقق من CSRF

check_csrf();


// الحصول على رقم الطلب

$id = (int) ($_POST['id'] ?? 0);



if ($id > 0) {

    $q = db()->prepare(
        'DELETE FROM orders WHERE id = ?'
    );

    $q->execute([
        $id
    ]);


    flash(
        'تم حذف الطلب.'
    );
}



redirect('index.php');