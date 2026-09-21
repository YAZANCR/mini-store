-- =========================================================
-- إنشاء قاعدة البيانات
-- =========================================================

CREATE DATABASE IF NOT EXISTS collection_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE collection_db;


-- =========================================================
-- جدول المستخدمين
-- =========================================================

CREATE TABLE IF NOT EXISTS users (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    email VARCHAR(190) NOT NULL UNIQUE,

    password_hash VARCHAR(255) NOT NULL,

    role ENUM('user', 'admin')
        NOT NULL DEFAULT 'user',

    created_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- =========================================================
-- جدول المتاجر
-- =========================================================

CREATE TABLE IF NOT EXISTS stores (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL UNIQUE,

    description TEXT NOT NULL,

    url VARCHAR(500) NOT NULL,

    image VARCHAR(255) DEFAULT NULL,

    created_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- =========================================================
-- جدول المنتجات
-- =========================================================

CREATE TABLE IF NOT EXISTS products (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(150) NOT NULL,

    description TEXT NOT NULL,

    price DECIMAL(10, 2)
        NOT NULL DEFAULT 0,

    stock INT UNSIGNED
        NOT NULL DEFAULT 0,

    image VARCHAR(255) DEFAULT NULL,

    created_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP
        NULL DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- =========================================================
-- جدول الطلبات
-- =========================================================

CREATE TABLE IF NOT EXISTS orders (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    store_id INT UNSIGNED NULL,

    product_id INT UNSIGNED NULL,

    quantity INT UNSIGNED
        NOT NULL DEFAULT 1,

    product_url VARCHAR(1000) NOT NULL,

    phone VARCHAR(30) NOT NULL,

    notes TEXT NULL,

    status ENUM(
        'new',
        'reviewing',
        'ordered',
        'shipped',
        'delivered',
        'cancelled'
    )
    NOT NULL DEFAULT 'new',

    created_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP
        NULL DEFAULT NULL
        ON UPDATE CURRENT_TIMESTAMP,


    -- علاقة الطلب بالمستخدم
    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,


    -- علاقة الطلب بالمتجر
    FOREIGN KEY (store_id)
        REFERENCES stores(id)
        ON DELETE SET NULL,


    -- علاقة الطلب بالمنتج
    FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE SET NULL

) ENGINE=InnoDB;


-- =========================================================
-- إضافة المتاجر
-- =========================================================

INSERT IGNORE INTO stores
    (name, description, url)
VALUES

(
    'SHEIN',
    'ملابس وأحذية وإكسسوارات ومستحضرات تجميل بأسعار مناسبة.',
    'https://www.shein.com'
),

(
    'ALIBABA',
    'منتجات بالجملة تشمل الإلكترونيات والأدوات المنزلية والمعدات.',
    'https://www.alibaba.com'
),

(
    'ALIEXPRESS',
    'منتجات متنوعة بالتجزئة مع شحن عالمي مباشر.',
    'https://www.aliexpress.com'
),

(
    'EBAY',
    'منصة عالمية للبيع والشراء والمزادات.',
    'https://www.ebay.com'
),

(
    'NOON',
    'منصة تسوق في الشرق الأوسط بمنتجات وخيارات دفع متعددة.',
    'https://www.noon.com'
),

(
    'TEMU',
    'منتجات متنوعة بأسعار منخفضة تشمل المنزل والإلكترونيات.',
    'https://www.temu.com'
),

(
    'TRENDYOL',
    'أزياء وأحذية وإكسسوارات ومنتجات منزلية تركية.',
    'https://www.trendyol.com'
),

(
    'AMAZON',
    'إلكترونيات وكتب وألعاب ومنتجات منزلية وصحية.',
    'https://www.amazon.com'
),

(
    'ASAF',
    'عطور فاخرة ومجوهرات ونظارات ومنتجات راقية.',
    'https://3saf.com/ar/'
),

(
    'IHERB',
    'فيتامينات ومكملات ومنتجات عناية شخصية وطبيعية.',
    'https://www.iherb.com'
);


-- =========================================================
-- إضافة المنتجات الافتراضية
-- =========================================================

INSERT INTO products
    (name, description, price, stock)

SELECT *
FROM (
    SELECT
        'سماعة لاسلكية',
        'سماعة بلوتوث بجودة صوت عالية وشحن سريع.',
        25.00,
        20
) AS p

WHERE NOT EXISTS (
    SELECT 1
    FROM products
    WHERE name = p.name
);


INSERT INTO products
    (name, description, price, stock)

SELECT *
FROM (
    SELECT
        'حقيبة عملية',
        'حقيبة يومية أنيقة ومناسبة للاستخدام المتكرر.',
        35.00,
        12
) AS p

WHERE NOT EXISTS (
    SELECT 1
    FROM products
    WHERE name = p.name
);


INSERT INTO products
    (name, description, price, stock)

SELECT *
FROM (
    SELECT
        'عطر فاخر',
        'عطر ثابت برائحة مميزة للاستخدام اليومي والمناسبات.',
        45.00,
        8
) AS p

WHERE NOT EXISTS (
    SELECT 1
    FROM products
    WHERE name = p.name
);


-- =========================================================
-- تحويل مستخدم إلى مسؤول
-- =========================================================

-- بعد إنشاء الحساب:
-- UPDATE users
-- SET role = 'admin'
-- WHERE email = 'admin@example.com';