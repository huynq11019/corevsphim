# System Patterns & Best Practices

## CRUD Controller Patterns

### Backpack CRUD Standard Structure
```php
// Standard setup method pattern
public function setup()
{
    CRUD::setModel(ModelName::class);
    CRUD::setRoute(config('backpack.base.route_prefix') . '/route-name');
    CRUD::setEntityNameStrings('entity name', 'entity names');
}
```

### Field Validation Pattern
```php
// Always validate fields before adding to CRUD
foreach (Option::getAllOptions() as $key => $field) {
    if (!isset($field['name']) || empty($field['name'])) {
        \Log::error('Field missing name key', ['key' => $key, 'field' => $field]);
        continue; // Skip invalid fields
    }
    CRUD::addField($field);
}
```

### Safe Field Value Mapping
```php
// Check field existence before setting values
foreach ($options as $k => $v) {
    if (array_key_exists($k, $fields)) {
        $fields[$k]['value'] = $v;
    } else {
        \Log::warning('Field exists in DB but not in definitions', [
            'field_name' => $k, 'value' => $v
        ]);
    }
}
```

## Error Handling Patterns

### Logging Standards
- Use descriptive log messages with context
- Include relevant data in log arrays
- Use appropriate log levels (error, warning, info)
- Always log field validation failures

### Controller Error Handling
```php
// Graceful degradation pattern
try {
    // Main operation
} catch (Exception $e) {
    \Log::error('Operation failed', ['error' => $e->getMessage()]);
    Alert::error('User-friendly error message')->flash();
    return back();
}
```

## Code Organization Patterns

### Controller Responsibilities
- **Setup:** Configure CRUD operations
- **Edit/Update:** Handle form rendering and data persistence
- **Validation:** Ensure data integrity
- **Response:** Provide appropriate feedback

### Vendor Package Integration
- Extend vendor controllers when needed
- Override specific methods rather than entire classes
- Maintain backward compatibility
- Document custom modifications

## Database Patterns

### Option/Setting Management
- Use JSON storage for flexible configurations
- Validate JSON structure before saving
- Provide fallback values for missing options
- Use typed accessors for complex data

### Model Relationships
- Follow Laravel naming conventions
- Use appropriate relationship types
- Include inverse relationships
- Consider eager loading for performance

## Security Patterns

### Input Validation
- Validate all user inputs
- Sanitize data before database operations
- Use Laravel's validation rules
- Implement CSRF protection

### Access Control
- Use Backpack's permission system
- Implement role-based access
- Protect sensitive operations
- Audit important actions
