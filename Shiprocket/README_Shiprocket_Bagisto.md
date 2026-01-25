# 🚚 Shiprocket API Integration for Bagisto (API-Only)

This module integrates **Shiprocket Shipping API** with **Bagisto**.
It is **API-only**, lightweight, and does **not modify core files** by default.

---

## ✅ What this module does
- Connects Bagisto to Shiprocket using API credentials
- Automatically creates Shiprocket orders on order placement
- Uses primary pickup location automatically
- Prevents duplicate Shiprocket orders
- Provides Admin UI for credentials & API testing

---

## 📦 Installation Steps (Non-Coder Friendly)

### 1️⃣ Upload Module
Upload the folder to:
```
packages/Webkul/Shiprocket
```

### 2️⃣ Database Update (Required)
Run once in phpMyAdmin:
```sql
ALTER TABLE orders
ADD shiprocket_order_created TINYINT(1) NOT NULL DEFAULT 0;
```

### 3️⃣ Clear Cache
```bash
php artisan optimize:clear
```

### 4️⃣ Configure in Admin
Open:
```
/admin/shiprocket
```
Save credentials → Test API

### 5️⃣ Place Test Order
Place a COD order to confirm Shiprocket order creation.

---

## ➕ Optional: Add Admin Menu (Core Change)
Edit:
```
packages/Webkul/Admin/src/Config/menu.php
```
Add:
```php
[
    'key' => 'shiprocket',
    'name' => 'Shiprocket',
    'route' => 'admin.shiprocket.config',
    'icon-class' => 'icon-truck',
],
```

Clear cache again.

---

## 🧾 Notes
- No Channel integration
- No Concord required
- API-only & lightweight

---

🎉 Shiprocket is now integrated!
