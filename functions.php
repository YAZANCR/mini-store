<?php

require_once __DIR__ . '/config.php';


// تُستخدم لتسجيل الدخول، الدور، CSRF، والرسائل المؤقتة.

if (session_status() !== PHP_SESSION_ACTIVE) {

    $isHttps = !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_start();
}


// تذكر وقت آخر زيارة لمدة 30 يومًا فقط.

if (!isset($_COOKIE['collection_last_visit'])) {

    setcookie(
        'collection_last_visit',
        (string) time(),
        [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS'])
                && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}



function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


// إعادة التوجيه

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}


// CSRF Token

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf'];
}


// التحقق من CSRF Token

function check_csrf(): void
{
    if (
        !hash_equals(
            $_SESSION['csrf'] ?? '',
            $_POST['csrf'] ?? ''
        )
    ) {
        http_response_code(419);
        exit('طلب غير صالح. أعد تحميل الصفحة.');
    }
}


// المستخدم الحالي

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}


// التحقق من تسجيل الدخول

function require_login(): void
{
    if (!user()) {
        redirect('login.php');
    }
}


// التحقق من صلاحية المسؤول

function require_admin(): void
{
    if (!user()) {
        redirect('login.php');
    }

    if (user()['role'] !== 'admin') {
        http_response_code(403);
        exit('ليس لديك صلاحية الوصول إلى هذه الصفحة.');
    }
}


// الرسائل المؤقتة Flash Messages

function flash(
    ?string $message = null,
    string $type = 'success'
): ?array {

    if ($message !== null) {

        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type,
        ];

        return null;
    }

    $f = $_SESSION['flash'] ?? null;

    unset($_SESSION['flash']);

    return $f;
}


// ======================================================
// تحويل حالة الطلب إلى اللغة العربية
// ======================================================

function status_label(string $status): string
{
    return [
        'new' => 'جديد',
        'reviewing' => 'قيد المراجعة',
        'ordered' => 'تم الطلب',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التسليم',
        'cancelled' => 'ملغى',
    ][$status] ?? $status;
}


// ======================================================
// رفع الصور
// ======================================================

function upload_image(
    string $field,
    ?string $old = null
): ?string {

    // إذا لم يتم رفع صورة جديدة، نستخدم الصورة القديمة.
    if (
        empty($_FILES[$field])
        || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return $old;
    }


    // التحقق من وجود خطأ أو تجاوز حجم الصورة 3MB.
    if (
        $_FILES[$field]['error'] !== UPLOAD_ERR_OK
        || $_FILES[$field]['size'] > 3 * 1024 * 1024
    ) {
        throw new RuntimeException(
            'الصورة يجب أن تكون أقل من 3MB.'
        );
    }


    // معرفة نوع الملف الحقيقي.
    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mime = $finfo->file(
        $_FILES[$field]['tmp_name']
    );


    // أنواع الصور المسموح بها.
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];


    // رفض أي نوع ملف غير مسموح.
    if (!isset($allowed[$mime])) {
        throw new RuntimeException(
            'يسمح فقط بصور JPG أو PNG أو WEBP أو GIF.'
        );
    }


    // تحديد مجلد حفظ الصور.
    $dir = __DIR__ . '/assets/uploads';


    // إنشاء المجلد إذا لم يكن موجودًا.
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }


    // إنشاء اسم عشوائي للصورة.
    $name = bin2hex(
        random_bytes(16)
    ) . '.' . $allowed[$mime];


    // نقل الصورة من المجلد المؤقت إلى مجلد المشروع.
    if (
        !move_uploaded_file(
            $_FILES[$field]['tmp_name'],
            $dir . '/' . $name
        )
    ) {
        throw new RuntimeException(
            'تعذر حفظ الصورة.'
        );
    }


    // إرجاع مسار الصورة.
    return 'uploads/' . $name;
}

?>