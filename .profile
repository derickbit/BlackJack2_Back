# Heroku Cedar loads Config Vars before sourcing this file for each dyno.
# Rebuild the build-time Laravel cache using this release's runtime values.
# Do not start the web process with stale configuration if rebuilding fails.
php artisan config:cache --no-interaction || exit $?
