# Installation

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- npm or yarn
- SQLite, MySQL, or PostgreSQL

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/vitespirite/cms.git
cd cms
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Build Assets

```bash
npm run build
```

### 6. Start the Server

```bash
php artisan serve
```

Visit `http://localhost:8000` 🎉

## Next Steps

- [Quick Start Guide](/guide/quick-start)
- [Architecture Overview](/guide/architecture)
- [Creating Your First Module](/modules/creating-module)
