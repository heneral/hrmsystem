# HotelHub

HotelHub is a Laravel-based hotel reservation and property-management system. It provides a customer booking portal and operational tools for reservations, rooms, housekeeping, payments, reporting, and auditability.

## Foundation

The project uses Laravel 12, PHP 8.2+, Blade, Tailwind CSS, Alpine.js, Vite, MySQL, Redis, and Docker. Business logic will be implemented through dedicated services, form requests, policies, events, jobs, notifications, and database transactions.

### Local setup

```bash
cp .env.example .env
php artisan key:generate
docker compose up -d mysql redis
php artisan migrate
npm install
npm run dev
```

Run the application in another terminal with `php artisan serve`.

## Implementation phases

1. Project foundation
2. Authentication and RBAC
3. Hotel and room management
4. Availability engine
5. Reservation engine
6. Pricing and rate management
7. Payments and invoices
8. Check-in and check-out
9. Housekeeping and maintenance
10. Notifications
11. Reports
12. Audit logs
13. REST API
14. Testing and optimization

The complete product requirements are maintained in [PROMPT.md](PROMPT.md).
