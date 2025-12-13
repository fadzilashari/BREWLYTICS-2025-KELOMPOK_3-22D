# Documentation

## User Documentation

This project make it for efficient operational management and give it advice to decision making about stock required and best product

### Feature

#### Management

- Dashboard (Stats widget to record sales total and review)
- Product Management
- Record Transaction
- Sales Management
- Review Management

#### Security

- Login Page
- Editing Profile
- Activity Logs

## Developer Documentation

To developer this project make it some technology like this

### Tech-Stack

This program is build with:

- Framework: Laravel
- Login: Breeze
- Role Permission: Spatie
- Dashboard Management: Filament

### How To Use

Just running this syntax if you using VScode you can Right Click and Run. This file I saved in custom-tools/automation-command in root file.

#### Step 1

This script functional to installing all dependency required

> install-dependency.bat

#### Step 2

This script functional to freshing your database and add seeder in your database

> new-migration.bat

### Step 3

```Running Vite
npm run dev
```

### Step 4

```Running PHP
php artisan serve
```

#### Optional

This script functional to adding seeder in your database

> add-seeder.bat

#### Tips

If you want to create new resource just follow this syntax or read Filament documentation
> Create Resource

```Create Resource
php artisan make:filament-resource {Name Model}
```

> Create model

```Create model
php artisan make:model {Name Model} -m
```
