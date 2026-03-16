<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/beGuided/marketplace-platform/actions"><img src="https://github.com/beGuided/marketplace-platform/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://github.com/beGuided/marketplace-platform"><img src="https://img.shields.io/github/license/beGuided/marketplace-platform" alt="License"></a>
</p>

# Marketplace Platform

## Overview

**Marketplace Platform** is a full-stack e-commerce web application inspired by platforms like Jiji.ng. It allows users to buy, sell, and communicate securely within the platform. Sellers can list products, buyers can browse and message sellers, and online payments are processed through Paystack for a seamless experience.

This project demonstrates **full-stack development skills**, including RESTful API design, React.js frontend integration, secure user authentication, and payment system integration.

---

## Features

- User authentication and profile management
- Product listing, editing, and deletion
- Messaging system between buyers and sellers
- Online payment integration using Paystack
- Admin dashboard to manage users, products, and transactions
- Responsive design for desktop and mobile
- Search and filter functionality for products

---

## Tech Stack

**Backend:**  
- Laravel (PHP)  
- MySQL  
- RESTful API

**Frontend:**  
- React.js  
- JavaScript  
- HTML / CSS / Tailwind CSS

**Payments & Tools:**  
- Paystack integration  
- Git version control  

---

## Installation

Follow these steps to set up the project locally:

### 1. Clone the repository

```bash
git clone https://github.com/beGuided/marketplace-platform.git
cd marketplace-platform
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

- Copy `.env.example` to `.env`  
- Configure your database and Paystack credentials in `.env`  
- Generate app key:

```bash
php artisan key:generate
```

### 4. Database setup

- Create a database in MySQL  
- Run migrations and seeders:

```bash
php artisan migrate --seed
```

### 5. Run the application

```bash
php artisan serve
npm run dev
```

- Open your browser and visit `http://localhost:8000`

---

## Screenshots | Demo
(https://joshuaadejoh.com.ng)


---

## Future Improvements

- Implement ratings and reviews for products     
- Real-time notifications for messages and transactions  
- Multi-language support

---

## Contributing

Contributions are welcome!  
1. Fork the repository  
2. Create a feature branch  
3. Commit your changes  
4. Push to your fork  
5. Open a Pull Request

---

## Author

**Joshua Adejoh**  
GitHub: (https://github.com/beGuided)  
Portfolio: (https://joshuaadejoh.com.ng)  

---

## License

This project is licensed under the MIT License.
