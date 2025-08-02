# GitHub Copilot Rules for CoreVsPhim Project

## Project Context Rules

### 🎯 Project Overview
- **Type:** Vietnamese Movie Streaming Website
- **Framework:** Laravel 8.x with OphimCMS
- **Admin Panel:** Backpack for Laravel CRUD
- **Theme:** MotChill responsive theme
- **Language:** Vietnamese content, English code

### 🏗️ Architecture Patterns

#### Always Follow These Patterns:
1. **Backpack CRUD Controllers:**
   ```php
   // Standard setup method
   public function setup()
   {
       CRUD::setModel(ModelName::class);
       CRUD::setRoute(config('backpack.base.route_prefix') . '/route-name');
       CRUD::setEntityNameStrings('singular', 'plural');
   }
   ```

2. **Field Validation (CRITICAL):**
   ```php
   // ALWAYS validate fields before adding
   foreach ($fields as $key => $field) {
       if (!isset($field['name']) || empty($field['name'])) {
           \Log::error('Field missing name key', ['key' => $key, 'field' => $field]);
           continue;
       }
       CRUD::addField($field);
   }
   ```

3. **Safe Value Mapping:**
   ```php
   // Check field existence before setting values
   foreach ($options as $k => $v) {
       if (array_key_exists($k, $fields)) {
           $fields[$k]['value'] = $v;
       } else {
           \Log::warning('Field not defined', ['field' => $k, 'value' => $v]);
       }
   }
   ```

## 🚨 Critical Rules (NEVER IGNORE)

### Error Handling
- **ALWAYS** add try-catch blocks for database operations
- **ALWAYS** log errors with context: `\Log::error('message', ['context' => $data])`
- **ALWAYS** validate field structure before CRUD operations
- **ALWAYS** provide user-friendly error messages via Alert::error()

### Security
- **ALWAYS** validate user inputs
- **ALWAYS** use Laravel's built-in validation rules
- **ALWAYS** sanitize data before database operations
- **NEVER** trust user input without validation

### Database
- **ALWAYS** use Eloquent ORM
- **ALWAYS** use migrations for schema changes
- **NEVER** write raw SQL unless absolutely necessary
- **ALWAYS** use proper relationships

## 📝 Coding Standards

### PHP/Laravel Conventions
- Use PSR-4 autoloading standards
- Follow Laravel naming conventions
- Use type hints for all parameters and return types
- Use strict comparison operators (=== instead of ==)
- Use meaningful variable and method names

### Comment Standards
```php
/**
 * Brief description of the method
 *
 * @param string $param Description
 * @return array Description
 * @throws Exception When...
 */
public function methodName(string $param): array
```

### File Organization
- Controllers in `app/Http/Controllers/`
- Models in `app/Models/`
- Custom libraries in `app/Library/`
- Traits in `app/Traits/`

## 🎬 Movie-Specific Rules

### Movie Data Handling
- **ALWAYS** validate movie data structure before saving
- Use proper data types: JSON for metadata, TEXT for descriptions
- Handle multiple languages (Vietnamese/English)
- Validate external URLs and media sources

### Crawler Integration
- **ALWAYS** validate crawler data before processing
- Log crawler operations for debugging
- Handle API rate limits gracefully
- Implement retry mechanisms for failed requests

## 🔧 OphimCMS Specific Rules

### Package Integration
- Extend vendor controllers rather than modifying them directly
- Use OphimCMS models and relationships when available
- Follow OphimCMS conventions for themes and views
- Respect package configuration patterns

### Custom Options
```php
// Use Option class for flexible configurations
Option::set('key', $value);
$value = Option::get('key', $default);
```

## 📱 Frontend Rules

### Blade Templates
- Use MotChill theme components
- Implement responsive design patterns
- Use Laravel's asset helpers
- Follow Bootstrap/Tailwind conventions

### JavaScript/CSS
- Use Laravel Mix for compilation
- Follow ES6+ standards
- Use semantic CSS class names
- Implement mobile-first responsive design

## 🔍 Debugging Rules

### Logging Strategy
```php
// Use appropriate log levels
\Log::debug('Debug info', $context);
\Log::info('General info', $context);
\Log::warning('Warning message', $context);
\Log::error('Error occurred', $context);
```

### Performance
- Use eager loading for relationships
- Implement proper caching strategies
- Optimize database queries
- Monitor memory usage in loops

## 🧪 Testing Rules

### Test Structure
- Write feature tests for user interactions
- Write unit tests for business logic
- Use factories for test data
- Mock external API calls

### Test Naming
```php
/** @test */
public function it_should_validate_movie_data_before_saving()
{
    // Test implementation
}
```

## 🚀 Deployment Rules

### Environment Configuration
- Use .env for environment-specific settings
- Never commit sensitive data
- Use proper cache and session drivers
- Configure proper error reporting levels

### Performance
- Enable opcache in production
- Use Redis for caching and sessions
- Optimize database indexes
- Compress assets

## 📚 When Suggesting Code

### Always Consider:
1. Is this following Laravel best practices?
2. Is proper error handling included?
3. Are fields validated before CRUD operations?
4. Is logging implemented appropriately?
5. Does this fit the OphimCMS architecture?
6. Is the code secure and validated?
7. Is it following the established patterns?

### Never Suggest:
- Raw SQL without proper escaping
- Unvalidated user input handling
- Missing error handling in critical operations
- Modifications to vendor files
- Hardcoded values that should be configurable
- Insecure file uploads or data handling

## 🎯 Current Development Focus

### Priority Areas (As of Current Context):
1. **CrawlerSettingController** validation and error handling
2. Field validation patterns across all controllers
3. Comprehensive logging implementation
4. Movie data management improvements
5. Admin panel UX enhancements

### Code Review Checklist:
- [ ] Field validation implemented
- [ ] Error handling with logging
- [ ] User feedback via alerts
- [ ] Security measures in place
- [ ] Follow established patterns
- [ ] Proper documentation/comments
- [ ] Test coverage considerations

Remember: This is a production movie streaming platform. Code quality, security, and user experience are paramount!
