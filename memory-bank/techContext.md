# Technical Context

## Technology Stack

### Backend Framework
- **Laravel 8.x:** Core PHP framework (based on composer.json)
- **PHP:** ^7.3|^8.0 compatibility
- **Composer:** Dependency management

### CMS & Admin
- **OphimCMS:** Movie-specific CMS core (`hacoidev/ophim-core: ^1.2`)
- **Backpack for Laravel:** Admin panel and CRUD operations
- **OphimCrawler:** Automated content crawler (`hacoidev/ophim-crawler: ^1.1`)

### Frontend & Theming
- **MotChill Theme:** Primary theme (`ophimcms/theme-motchill: ^1.2`)
- **Laravel Mix:** Asset compilation
- **Tailwind CSS:** Utility-first CSS framework
- **PostCSS:** CSS processing

### Key Dependencies
- **Guzzle HTTP:** HTTP client for API calls
- **Laravel Sanctum:** API authentication
- **Laravel Socialite:** Social login integration
- **Laravel CORS:** Cross-origin resource sharing

## Architecture Patterns

### MVC Structure
- **Models:** Eloquent ORM for data management
- **Views:** Blade templating with theme integration
- **Controllers:** CRUD operations via Backpack

### Key Directories
```
app/
├── Http/Controllers/     # Application controllers
├── Models/              # Eloquent models
├── Library/             # Custom libraries and helpers
└── Traits/              # Reusable traits

vendor/hacoidev/ophim-crawler/
└── src/Controllers/     # Crawler-specific controllers
```

### Database
- **MySQL/MariaDB:** Primary database
- **Migrations:** Schema versioning
- **Seeders:** Initial data population

## Development Tools
- **Artisan:** Laravel command-line tool
- **PHPUnit:** Testing framework
- **Docker:** Containerization support
- **Make:** Build automation
