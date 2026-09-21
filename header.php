
<?php

require_once __DIR__ . '/functions.php';

// عنوان الصفحة
$pageTitle = $pageTitle ?? 'كولكشن';

// تحديد مسار ملفات CSS والصفحات
$assetPrefix = str_contains(
    $_SERVER['SCRIPT_NAME'] ?? '',
    '/admin/'
) ? '../' : '';

?>

<!doctype html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        <?= e($pageTitle) ?> | كولكشن
    </title>

    <!-- ملفات التصميم -->
    <link
        rel="stylesheet"
        href="<?= $assetPrefix ?>assets/style.css"
    >

    <link
        rel="stylesheet"
        href="<?= $assetPrefix ?>assets/all.min.css"
    >

</head>

<body>

<header class="site-header">

    <!-- شعار الموقع -->
    <a
        class="brand"
        href="<?= $assetPrefix ?>index.php"
    >
        كولكشن
    </a>

    <!-- القائمة الرئيسية -->
    <nav>

        <a href="<?= $assetPrefix ?>index.php">
            الرئيسية
        </a>

        <a href="<?= $assetPrefix ?>products.php">
            المنتجات الفورية
        </a>

        <a href="<?= $assetPrefix ?>about.php">
            حولنا
        </a>

        <?php if (user()): ?>

            <!-- يظهر للمستخدم المسجل -->
            <a href="<?= $assetPrefix ?>my_orders.php">
                طلباتي
            </a>

            <?php if (user()['role'] === 'admin'): ?>

                <!-- يظهر للمدير فقط -->
                <a href="<?= $assetPrefix ?>admin/index.php">
                    لوحة التحكم
                </a>

            <?php endif; ?>

            <a href="<?= $assetPrefix ?>logout.php">
                خروج
            </a>

        <?php else: ?>

            <!-- يظهر للزائر -->
            <a href="<?= $assetPrefix ?>login.php">
                تسجيل الدخول
            </a>

            <a
                class="nav-cta"
                href="<?= $assetPrefix ?>register.php"
            >
                إنشاء حساب
            </a>

        <?php endif; ?>

    </nav>

</header>

<main class="container">

    <?php if ($f = flash()): ?>

        <div class="alert <?= e($f['type']) ?>">
            <?= e($f['message']) ?>
        </div>

    <?php endif; ?>

