# Marketplace Platform

## Overview

Marketplace Platform is a full-stack e-commerce application inspired by Jiji.ng. Users can browse products, message sellers, and make payments securely. The platform includes a robust admin dashboard for managing users, products, and transactions.

---

## Features

- User authentication and profile management  
- Product listing, editing, and deletion  
- Buyer-seller messaging system  
- Online payment integration (Paystack)  
- Admin dashboard for users, products, and transactions  
- Responsive UI for desktop and mobile  
- Product search and filtering  

---

## Tech Stack

**Backend:** Laravel, PHP, MySQL, REST API  
**Frontend:** React.js, JavaScript, HTML, CSS, Tailwind CSS  
**Payments & Tools:** Paystack, Git, Composer  

---

## Installation

```bash
git clone https://github.com/beGuided/marketplace-platform.git
cd marketplace-platform
composer install
npm install
cp .env.example .env
php artisan key:generate
```

- Configure database and Paystack credentials in `.env`  
- Run migrations and seeders:  
```bash
php artisan migrate --seed
```
- Serve the app:  
```bash
php artisan serve
npm run dev
```

Visit: `http://localhost:8000`

---

## Admin Access

- URL: `http://localhost:8000/admin`  
- Username: `admin@email.com`  
- Password: `123456`  

---


---

## Author

**Joshua Adejoh**  
GitHub: [https://github.com/beGuided](https://github.com/beGuided)  
Portfolio: [https://joshuaadejoh.com.ng](https://joshuaadejoh.com.ng)
