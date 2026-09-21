<?php

require_once __DIR__ . '/functions.php';


if (user()) {
    redirect('index.php');
}


$errors = [];


// معالجة نموذج التسجيل
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // التحقق من CSRF
    check_csrf();


    // استقبال البيانات
    $name = trim(
        $_POST['name'] ?? ''
    );

    $email = strtolower(
        trim($_POST['email'] ?? '')
    );

    $password = $_POST['password'] ?? '';

    $confirm = $_POST['password_confirmation'] ?? '';



    // التحقق من الاسم
    if (mb_strlen($name) < 2) {

        $errors[] = 'الاسم قصير جدًا.';
    }


    // التحقق من البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errors[] = 'البريد الإلكتروني غير صحيح.';
    }


    // التحقق من طول كلمة المرور
    if (strlen($password) < 8) {

        $errors[] =
            'كلمة المرور يجب أن تكون 8 أحرف على الأقل.';
    }


    // التحقق من تطابق كلمة المرور
    if ($password !== $confirm) {

        $errors[] =
            'تأكيد كلمة المرور غير مطابق.';
    }


    // إنشاء الحساب

    if (!$errors) {

        try {

            // تجهيز استعلام الإدخال
            $s = db()->prepare(
                'INSERT INTO users
                (name, email, password_hash)
                VALUES (?, ?, ?)'
            );


            // تنفيذ الاستعلام
            $s->execute([
                $name,
                $email,

                // تشفير كلمة المرور
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                )
            ]);


            // رسالة نجاح
            flash(
                'تم إنشاء الحساب. يمكنك تسجيل الدخول الآن.'
            );


            // الانتقال إلى صفحة تسجيل الدخول
            redirect('login.php');


        } catch (PDOException $e) {

            // البريد مستخدم مسبقًا
            if ($e->getCode() === '23000') {

                $errors[] =
                    'البريد الإلكتروني مستخدم مسبقًا.';

            } else {

                $errors[] =
                    'حدث خطأ أثناء التسجيل.';
            }
        }
    }
}


// عنوان الصفحة
$pageTitle = 'إنشاء حساب';


// استدعاء رأس الصفحة
require __DIR__ . '/header.php';

?>


<section class="form-card">

    <h1>
        إنشاء حساب
    </h1>


    <!-- عرض الأخطاء -->
    <?php foreach ($errors as $error): ?>

        <div class="alert error">
            <?= e($error) ?>
        </div>

    <?php endforeach; ?>


    <!-- نموذج إنشاء الحساب -->
    <form
        method="post"
        data-validate
    >

        <!-- حماية CSRF -->
        <input
            type="hidden"
            name="csrf"
            value="<?= e(csrf_token()) ?>"
        >


        <!-- الاسم -->
        <div class="form-row">

            <label>
                الاسم
            </label>

            <input
                name="name"
                required
                value="<?= e(
                    $_POST['name'] ?? ''
                ) ?>"
            >

        </div>


        <!-- البريد الإلكتروني -->
        <div class="form-row">

            <label>
                البريد الإلكتروني
            </label>

            <input
                name="email"
                type="email"
                required
                value="<?= e(
                    $_POST['email'] ?? ''
                ) ?>"
            >

        </div>


        <!-- كلمة المرور -->
        <div class="form-row">

            <label>
                كلمة المرور
            </label>

            <input
                name="password"
                type="password"
                required
            >

        </div>


        <!-- تأكيد كلمة المرور -->
        <div class="form-row">

            <label>
                تأكيد كلمة المرور
            </label>

            <input
                name="password_confirmation"
                type="password"
                required
            >

        </div>


        <!-- زر التسجيل -->
        <button
            class="btn"
            type="submit"
        >
            تسجيل
        </button>

    </form>

</section>


<?php

require __DIR__ . '/footer.php';

?>
