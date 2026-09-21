<?php

require_once __DIR__ . '/../functions.php';


// التأكد من أن المستخدم مسؤول

require_admin();


// إحصائيات الطلبات

$stats = db()->query("
    SELECT
        COUNT(*) AS total,
        SUM(status = 'new') AS new_count,
        SUM(status = 'delivered') AS delivered_count
    FROM orders
")->fetch();


// جلب جميع الطلبات

$orders = db()->query("
    SELECT
        o.*,
        u.name AS user_name,
        u.email,
        p.name AS product_name,
        s.name AS store_name

    FROM orders o

    JOIN users u
        ON u.id = o.user_id

    LEFT JOIN products p
        ON p.id = o.product_id

    LEFT JOIN stores s
        ON s.id = o.store_id

    ORDER BY o.created_at DESC
")->fetchAll();


// جلب المتاجر

$stores = db()
    ->query('SELECT * FROM stores ORDER BY name')
    ->fetchAll();


// جلب المنتجات

$products = db()
    ->query('SELECT * FROM products ORDER BY created_at DESC')
    ->fetchAll();


// جلب المستخدمين

$users = db()
    ->query("
        SELECT
            id,
            name,
            email,
            role,
            created_at

        FROM users

        ORDER BY created_at DESC
    ")
    ->fetchAll();


// عنوان الصفحة

$pageTitle = 'لوحة التحكم';

require __DIR__ . '/../header.php';

?>


<h1>
    لوحة تحكم المسؤول
</h1>


    
<div class="stats">

    <div class="stat">

        <strong>
            <?= e($stats['total'] ?? 0) ?>
        </strong>

        كل الطلبات

    </div>


    <div class="stat">

        <strong>
            <?= e($stats['new_count'] ?? 0) ?>
        </strong>

        طلبات جديدة

    </div>


    <div class="stat">

        <strong>
            <?= e($stats['delivered_count'] ?? 0) ?>
        </strong>

        تم التسليم

    </div>


    <div class="stat">

        <strong>
            <?= e(count($users)) ?>
        </strong>

        المستخدمون

    </div>

</div>


    
]
<h2>
    جميع الطلبات
</h2>


<div class="table-wrap">

    <table class="data-table">

        <thead>

            <tr>

                <th>#</th>

                <th>العميل</th>

                <th>المنتج/المتجر</th>

                <th>الكمية</th>

                <th>الجوال</th>

                <th>الحالة</th>

                <th>الإجراء</th>

            </tr>

        </thead>


        <tbody>

        <?php if (!$orders): ?>

            <tr>

                <td
                    colspan="7"
                    class="empty"
                >
                    لا توجد طلبات.
                </td>

            </tr>

        <?php else: ?>

            <?php foreach ($orders as $o): ?>

                <tr>

                    <!-- رقم الطلب -->

                    <td>
                        <?= e($o['id']) ?>
                    </td>


                    <!-- بيانات العميل -->

                    <td>

                        <?= e($o['user_name']) ?>

                        <br>

                        <small>
                            <?= e($o['email']) ?>
                        </small>

                    </td>


                    <!-- المنتج أو المتجر -->

                    <td>

                        <?php if ($o['product_name']): ?>

                            <?= e($o['product_name']) ?>

                        <?php else: ?>

                            <?= e(
                                $o['store_name'] ?? 'طلب رابط'
                            ) ?>

                        <?php endif; ?>


                        <br>


                        <?php if (
                            !empty($o['product_url']) &&
                            str_starts_with(
                                $o['product_url'],
                                'http'
                            )
                        ): ?>

                            <a
                                href="<?= e($o['product_url']) ?>"
                                target="_blank"
                                rel="noopener"
                            >
                                فتح الرابط
                            </a>

                        <?php endif; ?>

                    </td>


                    <!-- الكمية -->

                    <td>
                        <?= e($o['quantity']) ?>
                    </td>


                    <!-- رقم الجوال -->

                    <td>
                        <?= e($o['phone']) ?>
                    </td>


                    <!-- حالة الطلب -->

                    <td>
                        <?= e(
                            status_label($o['status'])
                        ) ?>
                    </td>


                    <!-- الإجراءات -->

                    <td>

                        <form
                            class="actions"
                            method="post"
                            action="update_order.php"
                        >

                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= e(csrf_token()) ?>"
                            >


                            <input
                                type="hidden"
                                name="id"
                                value="<?= e($o['id']) ?>"
                            >


                            <select name="status">

                                <option
                                    value="new"
                                    <?= $o['status'] === 'new'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    جديد
                                </option>


                                <option
                                    value="reviewing"
                                    <?= $o['status'] === 'reviewing'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    قيد المراجعة
                                </option>


                                <option
                                    value="ordered"
                                    <?= $o['status'] === 'ordered'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    تم الطلب
                                </option>


                                <option
                                    value="shipped"
                                    <?= $o['status'] === 'shipped'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    تم الشحن
                                </option>


                                <option
                                    value="delivered"
                                    <?= $o['status'] === 'delivered'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    تم التسليم
                                </option>


                                <option
                                    value="cancelled"
                                    <?= $o['status'] === 'cancelled'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    ملغى
                                </option>

                            </select>


                            <button
                                class="btn"
                                type="submit"
                            >
                                حفظ
                            </button>

                        </form>


                        <!-- حذف الطلب -->

                        <form
                            method="post"
                            action="delete_order.php"
                            class="actions"
                            onsubmit="return confirm('هل تريد حذف الطلب؟');"
                        >

                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= e(csrf_token()) ?>"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= e($o['id']) ?>"
                            >

                            <button
                                class="btn danger"
                                type="submit"
                            >
                                حذف
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>




<h2>

    إدارة المنتجات

    <a
        class="btn gold"
        href="product_form.php"
    >
        إضافة منتج
    </a>

</h2>


<div class="table-wrap">

    <table class="data-table">

        <thead>

            <tr>

                <th>المنتج</th>

                <th>الوصف</th>

                <th>السعر</th>

                <th>المخزون</th>

                <th>إجراء</th>

            </tr>

        </thead>


        <tbody>

        <?php foreach ($products as $p): ?>

            <tr>

                <td>
                    <?= e($p['name']) ?>
                </td>


                <td>
                    <?= e($p['description']) ?>
                </td>


                <td>
                    <?= e(
                        number_format(
                            (float) $p['price'],
                            2
                        )
                    ) ?>

                    ريال
                </td>


                <td>
                    <?= e($p['stock']) ?>
                </td>


                <td class="actions">

                    <a
                        class="btn"
                        href="product_form.php?id=<?= e($p['id']) ?>"
                    >
                        تعديل
                    </a>


                    <form
                        method="post"
                        action="delete_product.php"
                        onsubmit="return confirm('حذف هذا المنتج؟');"
                    >

                        <input
                            type="hidden"
                            name="csrf"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= e($p['id']) ?>"
                        >

                        <button
                            class="btn danger"
                            type="submit"
                        >
                            حذف
                        </button>

                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>



<h2>

    إدارة المتاجر

    <a
        class="btn gold"
        href="store_form.php"
    >
        إضافة متجر
    </a>

</h2>


<div class="table-wrap">

    <table class="data-table">

        <thead>

            <tr>

                <th>الاسم</th>

                <th>الوصف</th>

                <th>الرابط</th>

                <th>إجراء</th>

            </tr>

        </thead>


        <tbody>

        <?php foreach ($stores as $s): ?>

            <tr>

                <td>
                    <?= e($s['name']) ?>
                </td>


                <td>
                    <?= e($s['description']) ?>
                </td>


                <td>

                    <a
                        href="<?= e($s['url']) ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        فتح
                    </a>

                </td>


                <td class="actions">

                    <a
                        class="btn"
                        href="store_form.php?id=<?= e($s['id']) ?>"
                    >
                        تعديل
                    </a>


                    <form
                        method="post"
                        action="delete_store.php"
                        onsubmit="return confirm('حذف هذا المتجر؟');"
                    >

                        <input
                            type="hidden"
                            name="csrf"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= e($s['id']) ?>"
                        >

                        <button
                            class="btn danger"
                            type="submit"
                        >
                            حذف
                        </button>

                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>


<h2>
    إدارة المستخدمين
</h2>


<div class="table-wrap">

    <table class="data-table">

        <thead>

            <tr>

                <th>الاسم</th>

                <th>البريد</th>

                <th>الدور</th>

                <th>الإجراء</th>

            </tr>

        </thead>


        <tbody>

        <?php foreach ($users as $u): ?>

            <tr>

                <td>
                    <?= e($u['name']) ?>
                </td>


                <td>
                    <?= e($u['email']) ?>
                </td>


                <td>

                    <?= e(
                        $u['role'] === 'admin'
                            ? 'مسؤول'
                            : 'مستخدم'
                    ) ?>

                </td>


                <td>

                    <form
                        class="actions"
                        method="post"
                        action="update_user.php"
                    >

                        <input
                            type="hidden"
                            name="csrf"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= e($u['id']) ?>"
                        >


                        <select name="role">

                            <option
                                value="user"
                                <?= $u['role'] === 'user'
                                    ? 'selected'
                                    : '' ?>
                            >
                                مستخدم
                            </option>


                            <option
                                value="admin"
                                <?= $u['role'] === 'admin'
                                    ? 'selected'
                                    : '' ?>
                            >
                                مسؤول
                            </option>

                        </select>


                        <button
                            class="btn"
                            type="submit"
                        >
                            حفظ الدور
                        </button>

                    </form>


                    <?php if (
                        (int) $u['id'] !==
                        (int) user()['id']
                    ): ?>

                        <form
                            method="post"
                            action="delete_user.php"
                            onsubmit="return confirm('حذف هذا المستخدم وجميع طلباته؟');"
                        >

                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= e(csrf_token()) ?>"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= e($u['id']) ?>"
                            >

                            <button
                                class="btn danger"
                                type="submit"
                            >
                                حذف
                            </button>

                        </form>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>


<?php

require __DIR__ . '/../footer.php';

?>