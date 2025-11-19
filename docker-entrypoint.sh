#!/bin/sh

# Set the application environment (optional, but good practice)
export APP_ENV=${APP_ENV:-prod}



# Execute migrations and load fixtures only in the 'dev' environment
if [ "$APP_ENV" = "dev" ]; then
    echo "--- Running Doctrine Migrations for DEV environment ---"
    # Execute pending migrations
    # The --no-interaction flag is crucial for automation
    bin/console doctrine:migrations:migrate --no-interaction

    echo "--- Loading Doctrine Fixtures for DEV environment ---"
    # Load fixtures (using --purge-with-truncate ensures a clean slate)
    bin/console doctrine:fixtures:load --no-interaction --purge-with-truncate

    echo "--- Clearing cache ---"
    bin/console cache:clear --no-interaction

    echo "--- warmup cache ---"
    bin/console cache:warmup --no-interaction

    echo "--- Database setup complete ---"
fi

# Run the original PHP-FPM command
exec docker-php-entrypoint php-fpm
