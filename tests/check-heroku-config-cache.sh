#!/usr/bin/env bash
set -euo pipefail

# Run only in disposable CI. Never connect this check to production services.
export APP_ENV=testing
export DB_CONNECTION=sqlite
export DB_DATABASE=:memory:
export CACHE_STORE=array
export SESSION_DRIVER=array
export QUEUE_CONNECTION=sync
export MAIL_MAILER=array
export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
trap 'php artisan config:clear --no-interaction >/dev/null' EXIT

# Reproduce a slug built with debug enabled, then changed via Config Vars.
export APP_DEBUG=true
php artisan config:cache --no-interaction
export APP_DEBUG=false
php artisan tinker --execute='if (config("app.debug") !== true) { throw new RuntimeException("The stale-cache setup was not reproduced."); }'

# Heroku sources .profile before both web and one-off dyno commands.
source ./.profile
php artisan tinker --execute='if (!app()->configurationIsCached() || config("app.debug") !== false) { throw new RuntimeException("Runtime APP_DEBUG=false was not applied."); }'

# A failed rebuild must prevent startup, not silently use stale configuration.
if (php() { return 17; }; source ./.profile; exit 0); then
    echo 'Startup incorrectly continued after a failed cache rebuild.' >&2
    exit 1
else
    test "$?" -eq 17
fi

echo 'Heroku startup configuration checks passed.'
