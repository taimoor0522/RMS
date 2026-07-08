# RMS — Restro POS System

A PHP and MySQL based Point of Sale (POS) web application built for restaurants. The system provides three separate portals — Admin, Cashier, and Customer — each with role specific dashboards, order management, payment handling, and reporting.

![PHP](https://img.shields.io/badge/PHP-7.4-777BB4)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1)
![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap-7952B3)
![Status](https://img.shields.io/badge/Status-In%20Development-yellow)

---

## Table of Contents

1. [About This Project](#about-this-project)
2. [Tech Stack](#tech-stack)
3. [Features](#features)
4. [Folder Structure](#folder-structure)
5. [Database Schema](#database-schema)
6. [Installation and Setup](#installation-and-setup)
7. [Default Login Credentials](#default-login-credentials)
8. [What I Added / Customized](#what-i-added--customized)
9. [Skills Demonstrated](#skills-demonstrated)
10. [Planned Improvements](#planned-improvements)
11. [Credits](#credits)
12. [License](#license)

---

## About This Project

RMS (Restro POS System) is a restaurant management and point of sale system with three user roles:

- **Admin** — full control over products, staff, customers, suppliers, orders, and payments
- **Cashier** — day to day order creation, payment collection, and receipt printing
- **Customer** — self service ordering and order history tracking

This project is based on an open source Restaurant POS template originally developed by **Martin Mbithi Nzilani** and distributed via **CodeAstro**. I am using it as a base to study real world PHP application structure, and I am actively extending it with new modules of my own (see [What I Added / Customized](#what-i-added--customized)).

This repository is part of my personal learning portfolio while I build practical, hands on experience in PHP and MySQL development, and while I prepare to apply for web development internships. It is shared here in the interest of full transparency about what is original work and what is base template code.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP (procedural style, recommended PHP 7.4) |
| Database | MySQL (`rposystem`) |
| Database Access | `mysqli` and `PDO` |
| Frontend | HTML, CSS, SCSS, Bootstrap |
| JavaScript | jQuery, Chart.js, Bootstrap Datepicker, jQuery Scrollbar |
| Icons | Font Awesome, Nucleo Icons |
| Architecture | Multi portal, session based authentication, no framework |

---

## Features

### Admin Portal
- Dashboard with sales analytics
- Product management (add, update, categorize)
- Customer management with AJAX search
- Staff management
- Order creation and tracking
- Payment processing and payment reports
- Receipt generation and printing
- Sales and order reports
- Supplier management: suppliers, supplier items, supplier orders
- Profile management and password recovery

### Cashier Portal
- Dashboard
- Quick order creation
- Customer management
- Payment collection and receipts
- Order and payment reports
- Supplier item lookup

### Customer Portal
- Account registration and login
- Self service ordering
- Order history and payment history
- Profile management

---

## Folder Structure

```
RMS/
├── index.php                     # Landing page with links to all 3 portals
├── DATABASE FILE/
│   └── rposystem.sql             # Full database dump
└── Restro/
    ├── admin/
    │   ├── config/                # DB config (mysqli + PDO), login check
    │   ├── partials/                # Shared header, footer, sidebar, topnav
    │   ├── assets/                   # CSS, JS, SCSS, images, vendor libraries
    │   └── *.php                     # Feature pages
    ├── cashier/                   # Same structure as admin, scoped to cashier role
    └── customer/                  # Same structure as admin, scoped to customer role
```

---

## Database Schema

**Base tables** (included in `rposystem.sql`)

| Table | Purpose |
|---|---|
| `rpos_admin` | Admin accounts |
| `rpos_staff` | Staff and cashier accounts |
| `rpos_customers` | Customer accounts |
| `rpos_products` | Menu items |
| `rpos_orders` | Order records |
| `rpos_payments` | Payment records linked to orders |
| `rpos_pass_resets` | Password reset tokens |

**Extended tables** (created automatically on first run via `setup_supplier_tables.php`)

| Table | Purpose |
|---|---|
| `rpos_product_categories` | Product category management |
| `rpos_suppliers` | Supplier records |
| `rpos_supplier_items` | Items linked to a supplier and category |
| Supplier orders table | Tracks orders placed to suppliers |

---

## Installation and Setup

```bash
# 1. Install a local server stack (XAMPP, WAMP, or LAMP) with PHP 7.4 and MySQL

# 2. Clone the repository
git clone https://github.com/taimoor3122/RMS.git

# 3. Move the project folder into your server's htdocs (or www) directory

# 4. Create a MySQL database named "rposystem"

# 5. Import the schema
#    File: DATABASE FILE/rposystem.sql
#    Import it through phpMyAdmin or the MySQL CLI

# 6. Update database credentials if needed
#    Files: Restro/admin/config/config.php
#           Restro/admin/config/pdoconfig.php
#    Default is user "root" with an empty password

# 7. Start Apache and MySQL

# 8. Open in your browser
http://localhost/RMS/

# 9. Choose Admin, Cashier, or Customer login from the landing page
```

---

## Default Login Credentials

These are demo credentials for local testing only and must never be used in a production deployment.

| Role | Email | Password |
|---|---|---|
| Admin | admin@mail.com | codeastro.com |
| Cashier | cashier@mail.com | codeastro.com |

---

## What I Added / Customized

The base template did not include supplier management. I designed and implemented a full supplier module on top of the original codebase, including:

- Supplier records (`add_supplier.php`, `update_supplier.php`, `suppliers.php`)
- Supplier item catalog linked to product categories (`add_supplier_item.php`, `update_supplier_item.php`, `supplier_items.php`)
- Supplier ordering workflow (`supplier_orders.php`)
- Automatic schema setup for the above tables (`setup_supplier_tables.php`)

This module was added independently across the Admin and Cashier portals.

---

## Skills Demonstrated

Working on this project has given me hands on practice in:

- Reading, understanding, and extending an existing PHP codebase written by someone else
- Relational database design (foreign key relationships between suppliers, items, and categories)
- Writing raw SQL, including `CREATE TABLE` statements executed conditionally from PHP
- Working with both `mysqli` and `PDO` database access patterns
- Building multi role, session based authentication flows
- Structuring CRUD features (create, read, update) across multiple linked entities
- Identifying gaps in an existing system and designing a new module to fill them

---

## Planned Improvements

- Move duplicated code shared across Admin, Cashier, and Customer portals into a common shared layer
- Replace plaintext/basic credential handling with secure password hashing where not already present
- Add input validation and prepared statements consistently across all forms
- Add automated tests for order and payment flows

---

## Credits

- Original Restaurant POS template developed by **Martin Mbithi Nzilani**, distributed via **CodeAstro**
- Supplier management module and ongoing customizations by **Muhammad Taimoor**


## License

This repository is shared for educational and portfolio purposes only, and is not intended for commercial use or production deployment. The base template code belongs to its original developer and CodeAstro; any license terms attached to the original template apply to that portion of the code. The supplier management module and other original additions listed above may be reused with attribution to this repository.
