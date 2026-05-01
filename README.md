# Symfony App - Web App

## Description

This app allows users to share their photos. 

## Basic features
- Homepage with photo galleries (photos contain number of likes)
- Photos like/unlike
- Login with access token
- Logout
- Profiles views

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
php vendor/phpunit/phpunit/phpunit --configuration phpunit.xml.dist
```
