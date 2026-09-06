# Lustrous Glory International Quotation Portal

Laravel quotation workflow for the Muscat teams of Lustrous Glory International (Mais / Hadaya Muscat) and Mugdi Investments LLC (Mudgi brand asset), with a touch-first Dubai pricing desk.

## What is included

- Multiple authenticated users with `admin`, `preparer`, and `pricing` roles.
- Two separately branded Muscat quotation designs using the supplied logos.
- Product selection from MTC, Luxury Trading, Jasani, and HAK+.
- Mobile-first Dubai unit-price entry.
- OMR totals at three-decimal (baisa) precision and configurable 5% VAT defaults.
- PDF quotations with S.No, description, photo, quantity, price per unit, and total price.
- The preparer's name, phone number, and email on every quotation.
- Public supplier sitemap/structured-data synchronizer.
- No Node.js runtime or frontend build is required on Plesk.

## Local setup

Requirements: PHP 8.2+, Composer, SQLite or MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

For local SQLite development set `DB_CONNECTION=sqlite`, remove the other `DB_*` values, create `database/database.sqlite`, then run:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Demo accounts all use `ChangeMe123!` and must be changed before deployment:

- `admin@example.com` — administrator
- `sales@example.com` — Muscat quotation preparer
- `pricing@example.com` — Dubai mobile pricing

## Supplier catalog sync

The websites remain the source of truth. Only public product metadata is imported; price is deliberately excluded because Dubai supplies the current quotation price.

```bash
php artisan catalog:import --limit=20
php artisan catalog:import mtc
php artisan catalog:import luxury
php artisan catalog:import jasani
php artisan catalog:import hak
```

Omit `--limit` to process all product URLs found in the supplier's public sitemap. Supplier layouts and access policies may change. Obtain each supplier's permission to reuse product descriptions and photographs, and review their terms before production sync. A supplier that blocks or removes public structured product data is skipped without damaging the existing catalog.

## Plesk deployment without SSH

1. Put this project in a private Git repository. Do not commit `.env`.
2. In Plesk, choose PHP 8.2 or newer. Laravel 12 cannot run on PHP 8.1 or older.
3. Add the repository through **Websites & Domains → Git**, using HTTPS credentials or a repository deploy key supported by Plesk.
4. Set the domain document root to the repository's `/public` directory.
5. Create the MySQL database/user in Plesk and configure the production `.env` through the Plesk file manager.
6. Use the Plesk Composer page to run `composer install --no-dev --optimize-autoloader`.
7. Use a temporary protected deployment route or Plesk scheduled task to run `php artisan migrate --force`. Do not use `migrate:fresh` in production.
8. Make `storage` and `bootstrap/cache` writable by the subscription user.
9. Configure the deployment action to run `php artisan optimize` after each Git pull.
10. Add separate Plesk scheduled tasks for `php artisan catalog:import mtc`, `luxury`, `jasani`, and `hak`, staggered overnight. This avoids web-request timeouts.

Plesk normally pulls from the Git repository; VS Code pushes to GitHub/GitLab/Bitbucket, and Plesk then deploys that branch. Direct `git push` to the server generally requires SSH and therefore does not fit the stated hosting restriction.

## Production checklist

- Replace placeholder company addresses, phone numbers, VATINs, terms, and user details.
- Confirm the Mais logo belongs to Lustrous Glory International as its trading brand.
- Change every seeded password and set `APP_DEBUG=false`.
- Use HTTPS and secure cookies.
- Confirm VAT registration status with the company's accountant; the default 5% is editable in the database.
- Back up the database before deployments and migrations.
