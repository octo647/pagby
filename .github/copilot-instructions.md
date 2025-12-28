# Pagby - Copilot Instructions

## Architecture Overview

**Pagby** is a **Laravel 11 multi-tenant SaaS platform** for beauty salons and barbershops. It uses **domain-based tenancy** (via `stancl/tenancy`) where each salon gets a subdomain and separate database (`tenant{id}`). The central app manages tenant registration and subscriptions; tenant apps handle salon operations.

### Tech Stack
- **Backend**: Laravel 11 (PHP 8.2+), Livewire 3, multi-tenancy via `stancl/tenancy`
- **Frontend**: Blade templates, Alpine.js, Tailwind CSS, Chart.js
- **Payments**: Asaas (via `app/Services/AsaasService.php`), MercadoPago
- **Database**: MySQL (central + per-tenant databases with `TENANT_DB_PREFIX`)

## Multi-Tenancy Patterns

### Tenant Identification
- Routes use `InitializeTenancyByDomain::class` middleware in [routes/tenant.php](routes/tenant.php)
- Access tenant context with `tenant()` helper or `tenancy()->tenant`
- Central domain routes in [routes/web.php](routes/web.php) handle registration/payments

### Database Separation
- Each tenant gets database: `tenant{uuid}` (e.g., `tenant12345-abcd`)
- Central DB stores: tenants, domains, central payments ([app/Models/PagByPayment.php](app/Models/PagByPayment.php))
- Tenant DB stores: users, appointments, services, branches, subscriptions
- **Never manually initialize tenancy** in Livewire components—routing middleware handles it

### Storage Structure
- Tenant files: `storage/tenant{id}/` and `public/tenants/{id}/`
- Avoid cross-tenant data access; use `tenancy()->initialize($tenant)` carefully

## Role-Based Access Control

### User Roles
Three roles defined in [database/seeders/RolesTableSeeder.php](database/seeders/RolesTableSeeder.php):
- **Proprietário** (Owner): Full salon management, sees [app/Livewire/Proprietario/](app/Livewire/Proprietario) components
- **Funcionário** (Employee): Manages own agenda/services, sees [app/Livewire/Funcionario/](app/Livewire/Funcionario) components
- **Cliente** (Customer): Books appointments, views history, uses [app/Livewire/Cliente/](app/Livewire/Cliente) components

### Role Check Pattern
```php
// In controllers/Livewire
if (!auth()->user()->hasRole('Proprietário')) {
    abort(403);
}
```
Method defined in [app/Models/User.php](app/Models/User.php#L84): `hasRole(string $role): bool`

## Subscription System

### Status Flow (see [SUBSCRIPTION_SYSTEM.md](SUBSCRIPTION_SYSTEM.md))
1. New tenant → 30-day trial (`subscription_status='trial'`)
2. Trial expires → `subscription_status='expired'`, `is_blocked=true`
3. Select plan → `subscription_status='active'`, unlocked
4. Payment expires → blocked again

### Key Components
- **Middleware**: [CheckTenantSubscription](app/Http/Middleware/CheckTenantSubscription.php) blocks expired tenants
- **Command**: `php artisan tenants:check-expired-subscriptions` (run daily via cron)
- **Tenant Methods**: `isInTrial()`, `shouldBeBlocked()`, `getCurrentPricePerEmployee()` ([app/Models/Tenant.php](app/Models/Tenant.php))
- **Plans**: Básico (R$29.90/employee), Premium (R$59.90/employee) with promo pricing

### Routes Always Available
Even when blocked: `/subscription/plans`, `/subscription/select`, `/blocked`, `/logout`

## Livewire Component Organization

Components structured by role:
- [app/Livewire/Admin/](app/Livewire/Admin): Central admin (tenant management, [Saloes.php](app/Livewire/Admin/Saloes.php))
- [app/Livewire/Proprietario/](app/Livewire/Proprietario): Owner dashboards ([ServicosRealizados.php](app/Livewire/Proprietario/ServicosRealizados.php), [BalancoDiario.php](app/Livewire/Proprietario/BalancoDiario.php))
- [app/Livewire/Funcionario/](app/Livewire/Funcionario): Employee views ([Agenda.php](app/Livewire/Funcionario/Agenda.php))
- [app/Livewire/Cliente/](app/Livewire/Cliente): Customer booking flows

Use `@livewire('proprietario.servicos-realizados')` in views, not `<livewire:>` tags.

## Domain Model Relationships

### Core Models
- **Appointment** ([app/Models/Appointment.php](app/Models/Appointment.php)): Links employee, customer, branch, services (JSON), payments
- **User**: Has roles (many-to-many), branches, services (for employees), appointments
- **Branch**: Tenant locations, has users, schedules, services
- **Service**: Offered by employees at branches, linked via pivot tables

### Key Relationships
```php
User → roles (BelongsToMany)
User → branches (BelongsToMany for employees)
Appointment → employee (BelongsTo User)
Appointment → payments (HasMany Payment)
Tenant → pagByPayments (HasMany PagByPayment) // central DB
```

## Development Workflows

### Local Setup
```bash
composer install
npm install
cp .env.example .env  # Configure TENANT_DB_PREFIX
php artisan key:generate
php artisan migrate  # Central DB
npm run dev  # Vite dev server with --host
```

### Seeding (see [SEEDERS.md](SEEDERS.md))
Follow seeding order: roles → users → services → branches → role_user → service_user → schedules → appointments
```bash
php artisan db:seed --class=SeederMaster  # Complete realistic data
```

### Deployment ([scripts/deploy.sh](scripts/deploy.sh))
```bash
./scripts/deploy.sh  # Compiles assets, rsyncs to server, runs composer/cache commands
```
- Excludes: `.env`, `vendor/`, `node_modules/`, `storage/`, tenant data
- Sends `.env.production` as `.env` to server
- Runs `composer install --no-dev`, clears/caches config

### Key Commands
```bash
php artisan tenants:check-expired-subscriptions  # Check/block expired tenants
php artisan config:clear && php artisan cache:clear  # After env changes
```

## Payment Integration

### Asaas Service ([app/Services/AsaasService.php](app/Services/AsaasService.php))
```php
// Create payment link
$asaas = new AsaasService();
$result = $asaas->criarCheckout($customerData, $paymentData);

// Check payment status
$status = $asaas->consultarCobranca($asaasPaymentId);
```
Config in `config/services.asaas` (api_key, api_url)

### Payment Records
- **Central**: [PagByPayment](app/Models/PagByPayment.php) for tenant subscriptions
- **Tenant**: [Payment](app/Models/Payment.php) for customer appointment payments

## Conventions & Patterns

### Portuguese Naming
- Models/DB use Portuguese: `Proprietário`, `Funcionário`, `avaliacoes`, `comandas`
- Keep Portuguese in roles, migration names, seeder data

### Date/Time Handling
- Store dates as `Y-m-d`, times as `H:i:s`
- Appointments have `appointment_date`, `start_time`, `end_time`
- Use Carbon for calculations: `$tenant->trial_ends_at->diffInDays(now())`

### JSON Fields
- `Appointment.services`: JSON string of service IDs/names
- Cast carefully: `protected $casts = ['services' => 'string'];`

### File Organization
- Controllers: Group by feature (Auth/, Payment, Subscription)
- Livewire: Always nest by role (Admin/, Proprietario/, Funcionario/, Cliente/)
- Views: Mirror Livewire structure in `resources/views/livewire/`

## Critical Files

- [config/tenancy.php](config/tenancy.php): Tenancy config, central_domains, bootstrappers
- [SUBSCRIPTION_SYSTEM.md](SUBSCRIPTION_SYSTEM.md): Complete subscription workflow documentation
- [routes/tenant.php](routes/tenant.php): All tenant-scoped routes with tenancy middleware
- [routes/web.php](routes/web.php): Central domain routes (registration, landing page)
- [app/Models/Tenant.php](app/Models/Tenant.php): Custom subscription logic, pricing

## Testing & Debugging

- Use `tenant()` helper to check current tenant context
- Test multi-tenancy by accessing different subdomains
- Check `storage/logs/laravel.log` for tenant-specific errors
- Verify middleware stack: tenancy must initialize before auth checks
