# Symfony App - Web App

## Commands

### Database migration
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

### Recreate database
```bash
php bin/console doctrine:schema:drop --force --full-database
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:seed
```

### Clearing cache
```bash
php bin/console cache:clear
```

### Running tests
```bash
php bin/phpunit
```
