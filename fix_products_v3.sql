USE collection_db;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- نفّذ أوامر ALTER التالية مرة واحدة فقط على قاعدة البيانات القديمة.
ALTER TABLE orders ADD COLUMN product_id INT UNSIGNED NULL AFTER store_id;
ALTER TABLE orders ADD COLUMN quantity INT UNSIGNED NOT NULL DEFAULT 1 AFTER product_id;
ALTER TABLE orders ADD CONSTRAINT fk_orders_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL;

INSERT INTO products (name, description, price, stock) VALUES
('سماعة لاسلكية','سماعة بلوتوث بجودة صوت عالية وشحن سريع.',25.00,20),
('حقيبة عملية','حقيبة يومية أنيقة ومناسبة للاستخدام المتكرر.',35.00,12),
('عطر فاخر','عطر ثابت برائحة مميزة للاستخدام اليومي والمناسبات.',45.00,8);
