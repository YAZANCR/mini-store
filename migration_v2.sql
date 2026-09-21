USE collection_db;
ALTER TABLE orders MODIFY status ENUM('new','reviewing','ordered','shipped','delivered','cancelled') NOT NULL DEFAULT 'new';
ALTER TABLE orders ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;
-- نفّذ السطر التالي فقط إذا لم يكن هناك متجران بالاسم نفسه:
-- ALTER TABLE stores ADD UNIQUE KEY unique_store_name (name);
