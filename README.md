# Second Life Bot — Repaired Edition

Telegram life-simulation game bot written in PHP + SQLite.

## What was repaired
- Restored PHP syntax across the project.
- Fixed asset purchase callback parsing (`prop:buy:key`, `car:buy:key`).
- Completed the business-name flow: typed names now create the selected business.
- Disabled the public test endpoint.
- Added optional Telegram webhook secret validation.
- Added `.gitignore` rules for secrets and runtime data.
- Economy operations use transactional balance updates.

## Requirements
PHP 8.1+ with `curl`, `pdo_sqlite`, `sqlite3`, `mbstring`.

## Setup
1. Copy `.env.example` to `.env`.
2. Set `BOT_TOKEN`, optional `WEBHOOK_SECRET`, and `ADMIN_IDS`.
3. Point your web server document root to `public/`.
4. Configure Telegram webhook to `public/index.php`; if using a secret, set the same secret in Telegram.
5. Keep `data/` writable by PHP.

## Important security
Never commit `.env`, database files, or bot tokens. If an old token was exposed, revoke it in BotFather and generate a new one.
