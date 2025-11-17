#!/bin/bash
echo "🔄 Switching to LOCAL environment..."
cp .env.local .env
echo "✅ Now using LOCAL configuration"
php artisan config:clear
php artisan cache:clear
