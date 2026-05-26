# MangaShop

A modern and feature-rich Manga e-commerce and custom printing web application built with PHP and MySQL, designed to provide a premium shopping experience across all modern browsers.

* 🌐 **Live Website**: [http://mangashop.infinityfree.me](http://mangashop.infinityfree.me)
* 🔑 **Admin Panel**: [http://mangashop.infinityfree.me/admin/](http://mangashop.infinityfree.me/admin/)
  * **Email**: `admin@mangashop.ma`
  * **Password**: `admin123`

## Features

### Authentication
* Email & Password Authentication
* Role-based Access Control (User & Admin)
* Password security via bcrypt hashing
* Login attempts logging and security protection

### Manga & Packs Ordering
* Browse manga categories and custom bundles
* Instant AJAX-powered search and autocompletion
* Product details screen with book specs & chapters
* Add to cart and quantity management in dynamic drawers

### Cart & Checkout
* Dynamic free shipping meter
* Promo code support
* Cash on Delivery (COD) & Stripe payment options
* Order summary and automated email confirmations

### Custom Printing Service
* Advanced PDF upload form
* Material options (Format, Cover Type, Pages, Quantity)
* Custom print quote estimation and requests

### Admin Panel
* Analytics dashboard with graphical sales trends
* Complete CRUD for Products, Categories, Bundles, Coupons, Orders, and Devis
* Detailed administrator activity logs
* CSV data exporter for orders

### User Experience
* Premium SaaS-like UI design with Glassmorphism
* Interactive 3D cover tilt effect following mouse movement
* Smooth micro-animations
* Dark & Light Mode toggle with local persistence
* Interactive reviews and ratings system

## Tech Stack
* PHP (Native MVC)
* MySQL / PDO
* JavaScript (ES6+ / AJAX)
* CSS3 (Vanilla CSS with custom design tokens)
* HTML5 (Semantic UI)
* Stripe API
* PHPMailer

## Architecture
The application follows a clean, modular MVC structure with separate routing, views, controllers, and models.

### Key Components
* **config.php** (Dynamic environment detection for Local/Production)
* **functions.php** (Core database model handlers)
* **mailer.php** (Email notification handlers)
* **admin/** (Secure administration workspace)
* **actions/** (Asynchronous AJAX-powered request controllers)

## Project Statistics
* 15+ Core Web Screens
* 10+ AJAX API Actions
* Full MVC Directory Structure
* Responsive and browser-compatible layouts

## Installation

### Clone the repository:
```bash
git clone https://github.com/mohammedbouaouin-1/mangashop-site-e-com.git
```

### Navigate to the project:
```bash
cd mangashop
```

### Set up Database & Config:
1. Create a blank database named `mangashop` in your local MySQL (phpMyAdmin).
2. Copy `includes/config.sample.php` to `includes/config.php` and set your credentials.
3. Access the application in your browser (it will auto-install the tables automatically!).

### Deploy to Production (InfinityFree):
1. Import `database.sql` into your InfinityFree phpMyAdmin database.
2. Upload the project files directly into the `/htdocs` folder using FileZilla.

## Author
Mohammed Bouaouin

LinkedIn: [https://www.linkedin.com/in/mohammed-bouaouin-8a9720360](https://www.linkedin.com/in/mohammed-bouaouin-8a9720360)

GitHub: [https://github.com/mohammedbouaouin-1](https://github.com/mohammedbouaouin-1)
