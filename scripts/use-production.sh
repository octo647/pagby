#!/bin/bash
echo "🔄 Switching to PRODUCTION environment..."
cp .env.production .env
echo "✅ Now using PRODUCTION configuration"
php artisan config:clear
php artisan cache:clear
