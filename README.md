# KeyVault - License Key Management System

A full-featured license key management system built with Laravel. Generate, manage, validate, and track software license keys with a clean web UI and REST API.

## Features

### Web Dashboard
- **Dashboard** with real-time statistics (total products, licenses, activations, expired/suspended counts)
- **Product Management** — CRUD operations for software products
- **License Management** — Create, edit, search, filter, suspend, revoke, and reactivate licenses
- **Bulk Key Generation** — Generate up to 500 license keys at once
- **Activation Tracking** — View all device activations per license

### License Key Formats
- **Standard**: `XXXX-XXXX-XXXX-XXXX`
- **Extended**: `XXXX-XXXX-XXXX-XXXX-XXXX`
- **Short**: `XXXX-XXXX-XXXX`
- **UUID**: Standard UUID v4

### License Types
- Trial, Standard, Extended, Lifetime

### REST API
Integrate license validation into your software:

```bash
# Validate a license
POST /api/v1/licenses/validate
{"license_key": "XXXX-XXXX-XXXX-XXXX"}

# Activate a license on a device
POST /api/v1/licenses/activate
{"license_key": "XXXX-XXXX-XXXX-XXXX", "hardware_id": "device-123", "machine_name": "My PC"}

# Deactivate a license
POST /api/v1/licenses/deactivate
{"license_key": "XXXX-XXXX-XXXX-XXXX", "hardware_id": "device-123"}
```

## Installation

### Requirements
- PHP 8.1+
- Composer
- SQLite (default) or MySQL/PostgreSQL

### Setup

```bash
# Clone the repository
git clone <repo-url>
cd license-management

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Create database (SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# (Optional) Seed demo data
php artisan db:seed --class=DemoSeeder

# Start the development server
php artisan serve
```

Visit `http://localhost:8000` to access the dashboard.

## Database Schema

### Products
| Column | Type | Description |
|--------|------|-------------|
| name | string | Product name |
| slug | string | URL-friendly identifier |
| description | text | Product description |
| version | string | Current version |
| is_active | boolean | Whether product is active |

### Licenses
| Column | Type | Description |
|--------|------|-------------|
| product_id | FK | Associated product |
| license_key | string | Unique license key |
| customer_name | string | Customer name |
| customer_email | string | Customer email |
| type | enum | trial, standard, extended, lifetime |
| status | enum | active, inactive, expired, suspended, revoked |
| max_activations | integer | Maximum allowed activations |
| current_activations | integer | Current active count |
| issued_at | timestamp | When the license was issued |
| expires_at | timestamp | Expiration date (null = never) |

### License Activations
| Column | Type | Description |
|--------|------|-------------|
| license_id | FK | Associated license |
| machine_name | string | Device name |
| hardware_id | string | Unique hardware identifier |
| ip_address | string | Client IP |
| domain | string | Associated domain |
| is_active | boolean | Whether activation is current |

## Tech Stack
- **Backend**: Laravel 10
- **Frontend**: Blade templates + Tailwind CSS (via CDN)
- **Database**: SQLite (configurable)
- **Icons**: Font Awesome 6

## License
MIT
