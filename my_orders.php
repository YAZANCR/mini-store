<?php

require_once __DIR__ . '/functions.php';

// التأكد من تسجيل الدخول
require_login();


// جلب طلبات المستخدم الحالي
$q = db()->prepare(
    'SELECT
        o.*,
        s.name AS store_name,
        p.name AS product_name
     FROM orders o
     LEFT JOIN stores s ON s.id = o.store_id
     LEFT JOIN products p ON p.id = o.product_id
     WHERE o.user_id = ?
     ORDER BY o.created_at DESC'
);

$q->execute([
    user()['id']
]);

$orders = $q->fetchAll();


// عنوان الصفحة
$pageTitle = 'طلباتي';


// استدعاء رأس الصفحة
require __DIR__ . '/header.php';

?>


<h1>
    طلباتي
</h1>


<div class="table-wrap">

    <table class="data-table">

        <thead>

            <tr>

                <th>#</th>

                <th>
                    المنتج/المتجر
                </th>

                <th>
                    الكمية
                </th>

                <th>
                    الرابط
                </th>

                <th>
                    الحالة
                </th>

                <th>
                    التاريخ
                </th>

            </tr>

        </thead>


        <tbody>

            <?php foreach ($orders as $o): ?>

                <tr>

                    <!-- رقم الطلب -->
                    <td>
                        <?= e($o['id']) ?>
                    </td>


                    <!-- المنتج أو المتجر -->
                    <td>

                        <?= e(
                            $o['product_name']
                            ?: ($o['store_name'] ?? 'طلب رابط')
                        ) ?>

                    </td>


                    <!-- الكمية -->
                    <td>
                        <?= e($o['quantity']) ?>
                    </td>


                    <!-- رابط المنتج -->
                    <td>

                        <?php if (
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

                        <?php else: ?>

                            منتج فوري

                        <?php endif; ?>

                    </td>


                    <!-- حالة الطلب -->
                    <td>

                        <span class="badge">
                            <?= e(
                                status_label(
                                    $o['status']
                                )
                            ) ?>
                        </span>

                    </td>


                    <!-- تاريخ الطلب -->
                    <td>
                        <?= e($o['created_at']) ?>
                    </td>

                </tr>

            <?php endforeach; ?>


            <!-- في حالة عدم وجود طلبات -->
            <?php if (!$orders): ?>

                <tr>

                    <td
                        colspan="6"
                        class="empty"
                    >
                        لا توجد طلبات حتى الآن.
                    </td>

                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>


<?php

require __DIR__ . '/footer.php';

?>
