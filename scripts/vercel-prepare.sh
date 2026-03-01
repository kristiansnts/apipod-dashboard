#!/bin/bash
set -e
mkdir -p api/laravel
for d in app bootstrap config database public resources routes storage; do
  [ -d "$d" ] && cp -r "$d" api/laravel/ || true
done
for f in artisan composer.json composer.lock; do
  [ -f "$f" ] && cp "$f" api/laravel/ || true
done
echo "==> Laravel files copied to api/laravel/"
