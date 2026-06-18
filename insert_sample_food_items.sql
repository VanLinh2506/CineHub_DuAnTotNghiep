-- ============================================
-- Insert Sample Food Items for CineHub
-- ============================================

-- Clear existing food items (optional, comment out if not needed)
-- DELETE FROM food_items;

-- Insert Combo Items
INSERT INTO food_items (name, type, price, description, is_active, created_at, updated_at) VALUES
('Combo 1 - Bắp + Nước', 'combo', 89000.00, '1 Bắp rang bơ size L + 2 Nước ngọt size L', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Combo 2 - Bắp + Nước + Khoai', 'combo', 129000.00, '1 Bắp size L + 2 Nước size L + 1 Khoai tây chiên', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Combo 3 - Family', 'combo', 249000.00, '2 Bắp size L + 4 Nước size L + 2 Khoai tây chiên', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Combo 4 - Couple', 'combo', 159000.00, '1 Bắp size XL + 2 Nước size L + 1 Bánh nachos', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Insert Drinks
INSERT INTO food_items (name, type, price, description, is_active, created_at, updated_at) VALUES
('Nước ngọt Pepsi', 'drink', 35000.00, 'Size L (700ml)', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Nước ngọt Coca Cola', 'drink', 35000.00, 'Size L (700ml)', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('7-Up', 'drink', 35000.00, 'Size L (700ml)', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Nước cam ép', 'drink', 45000.00, 'Size M (500ml)', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Trà đào cam sả', 'drink', 45000.00, 'Size M (500ml)', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Café đen', 'drink', 35000.00, 'Size M', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Café sữa', 'drink', 40000.00, 'Size M', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Insert Snacks
INSERT INTO food_items (name, type, price, description, is_active, created_at, updated_at) VALUES
('Bắp rang bơ', 'snack', 45000.00, 'Size L', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Bắp rang phô mai', 'snack', 50000.00, 'Size L', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Khoai tây chiên', 'snack', 40000.00, 'Size M', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Bánh nachos phô mai', 'snack', 45000.00, 'Size M', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Hotdog', 'snack', 35000.00, '1 cái', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Pizza mini', 'snack', 55000.00, '4 miếng', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Verify insertion
SELECT 
    id, 
    name, 
    type, 
    price, 
    description,
    is_active
FROM food_items
ORDER BY type, id;

-- Count by type
SELECT 
    type,
    COUNT(*) as count,
    SUM(price) as total_value
FROM food_items
WHERE is_active = 1
GROUP BY type;
