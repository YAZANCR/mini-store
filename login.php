<?php

require_once __DIR__ . '/functions.php';



// إذا كان المستخدم مسجل دخول بالفعل

if (user()) {

    redirect(
        user()['role'] === 'admin'
            ? 'admin/index.php'
            : 'index.php'
    );
}



$error = null;


// معالجة نموذج تسجيل الدخول

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // التحقق من CSRF Token.
    check_csrf();


    // الحصول على البريد الإلكتروني وكلمة المرور.
    $email = strtolower(
        trim($_POST['email'] ?? '')
    );

    $password = $_POST['password'] ?? '';


    // البحث عن المستخدم بواسطة البريد الإلكتروني.
    $q = db()->prepare(
        'SELECT * FROM users WHERE email = ? LIMIT 1'
    );

    $q->execute([$email]);

    $account = $q->fetch();


    // التحقق من وجود الحساب وصحة كلمة المرور.
    if (
        $account
        && password_verify(
            $password,
            $account['password_hash']
        )
    ) {

        // تغيير Session ID بعد تسجيل الدخول.
        session_regenerate_id(true);


        // حفظ بيانات المستخدم داخل Session.
        $_SESSION['user'] = [
            'id' => (int) $account['id'],
            'name' => $account['name'],
            'email' => $account['email'],
            'role' => $account['role'],
        ];


        // حفظ وقت تسجيل الدخول.
        $_SESSION['logged_in_at'] = time();


        // تحويل المستخدم حسب صلاحياته.
        redirect(
            $account['role'] === 'admin'
                ? 'admin/index.php'
                : 'index.php'
        );
    }


    $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
}


// عنوان الصفحة وملف Header

$pageTitle = 'تسجيل الدخول';

require __DIR__ . '/header.php';

?>

<section class="form-card">

    <h1>تسجيل الدخول</h1>

    <p class="muted">
        سيتم نقلك تلقائيًا حسب نوع حسابك: مستخدم أو مسؤول.
    </p>


    <?php if ($error): ?>

        <div class="alert error">
            <?= e($error) ?>
        </div>

    <?php endif; ?>


    <form method="post" data-validate>

        <input
            type="hidden"
            name="csrf"
            value="<?= e(csrf_token()) ?>"
        >


        <div class="form-row">

            <label>البريد الإلكتروني</label>

            <input
                name="email"
                type="email"
                required
            >

        </div>


        <div class="form-row">

            <label>كلمة المرور</label>

            <input
                name="password"
                type="password"
                required
            >

        </div>


        <button
            class="btn"
            type="submit"
        >
            دخول
        </button>

    </form>


    <p>
        ليس لديك حساب؟
        <a href="register.php">
            إنشاء حساب جديد
        </a>
    </p>

</section>


<?php require __DIR__ . '/footer.php'; ?>
