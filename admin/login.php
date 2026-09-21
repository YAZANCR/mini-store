<?php

require_once __DIR__ . '/../functions.php';


if (user()) {

    // إذا كان المستخدم مسؤولًا
    if (user()['role'] === 'admin') {

        redirect('index.php');

    }

    // إذا كان مستخدمًا عاديًا
    redirect('../products.php');
}




redirect('../login.php');