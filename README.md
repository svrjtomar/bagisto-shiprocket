This guide explains how to install the Shiprocket integration in Bagisto using ZIP upload and terminal access.
No coding knowledge is required.

---

## ✅ Requirements

- Bagisto already installed
- Access to File Manager (cPanel / hosting panel)
- Access to Terminal / SSH
- Shiprocket Email & Password

---

## 📁 Step 1: Upload the ZIP File

1. Login to your hosting File Manager
2. Go to your Bagisto root folder (where artisan, vendor, packages folders exist)
3. Open the folder:

packages/Webkul

4. Upload the Shiprocket ZIP file
5. Extract the ZIP file

After extraction, the folder structure must look like this:

packages  
└── Webkul  
  └── Shiprocket  
    ├── Providers  
    ├── Services  
    ├── Http  
    ├── Routes  
    ├── Resources  
    └── composer.json  

⚠️ Folder name must be exactly Shiprocket

---

## 💻 Step 2: Open Terminal

Open Terminal / SSH and go to Bagisto root:

cd /home/your-username/public_html

(Path may vary depending on hosting)

---

## 📦 Step 3: Install the Package

Run the following command:

composer require webkul/shiprocket:@dev

This command tells Bagisto and Laravel that the Shiprocket module exists.

---

## 🧹 Step 4: Clear Cache

Run:

php artisan optimize:clear  
composer dump-autoload

---

## 🗄️ Step 5: Run Migration

Run:

php artisan migrate

This safely adds a column to prevent duplicate Shiprocket orders.

---

## 🔐 Step 6: Configure Shiprocket (Admin Panel)

1. Login to Bagisto Admin
2. Open Shiprocket Integration
3. Enter:
   - Shiprocket Email
   - Shiprocket Password
4. Click Save Credentials
5. Click Test API

You should see:
Shiprocket authenticated successfully

---

## 📦 Step 7: Place a Test Order

1. Place a COD or online payment order
2. Order status should be pending
3. Order will automatically appear in Shiprocket dashboard

---

## 🔁 Token Handling

- Token is cached automatically
- Token refreshes when expired
- No daily login required
- No cron job needed

---

## 🏠 Pickup Location

- Primary pickup location is auto-selected automatically
- No manual setup required

---

## 🧪 Logs (Optional)

Logs can be found at:

storage/logs/laravel.log

---

## 🗑️ Uninstall (Optional)

composer remove webkul/shiprocket  
php artisan optimize:clear

---

## ❓ Common Questions

Do I need to edit Bagisto core files?  
No.

Do I need Shiprocket Channel ID?  
No, API only.

Does this affect login, cart, or checkout?  
No.

---

## ✅ Done

Shiprocket is now successfully integrated with your Bagisto store.
"""

path = Path("/mnt/data/README_Shiprocket_Bagisto_SIMPLE.md")
path.write_text(content)

path
