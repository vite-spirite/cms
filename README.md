# CMS

A modular content management system built with Laravel, Vue 3, Inertia.js, and TypeScript.

## Stack

Laravel 12 · Vue 3 · Inertia.js · Nuxt UI · Tailwind CSS · TypeScript · Vite

## Requirements

- PHP 8.2+
- Node.js 18+
- Composer

## Installation

```bash
git clone https://github.com/vite-spirite/cms.git
cd cms

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
npm run build

php artisan serve
```

## First user

```bash
php artisan auth:create-user "John Doe" john@example.com
php artisan permissions:owner make john@example.com
```

Then log in at `/admin/login`.

## Documentation

[https://vite-spirite.github.io/cms](https://vite-spirite.github.io/cms)

## License

MIT
