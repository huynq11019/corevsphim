# Quick Reference Guide

## 🚀 Common Commands

### Artisan Commands
```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Generate controllers/models
php artisan make:controller MovieController
php artisan make:model Movie -m
```

### Composer Commands
```bash
# Install dependencies
composer install

# Update packages
composer update

# Autoload optimization
composer dump-autoload
```

## 🎛️ Backpack CRUD Quick Patterns

### Basic Controller Setup
```php
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class YourController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\YourModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/your-route');
        CRUD::setEntityNameStrings('item', 'items');
    }
}
```

### Field Types Reference
```php
// Text input
CRUD::addField(['name' => 'title', 'type' => 'text', 'label' => 'Title']);

// Textarea
CRUD::addField(['name' => 'description', 'type' => 'textarea', 'label' => 'Description']);

// Select dropdown
CRUD::addField([
    'name' => 'category_id',
    'type' => 'select',
    'label' => 'Category',
    'entity' => 'category',
    'model' => "App\Models\Category",
    'attribute' => 'name',
]);

// Image upload
CRUD::addField([
    'name' => 'image',
    'type' => 'upload',
    'label' => 'Image',
    'upload' => true,
    'disk' => 'public',
]);
```

## 🎬 Movie-Specific Patterns

### Movie Model Relationships
```php
// In Movie model
public function category()
{
    return $this->belongsTo(Category::class);
}

public function episodes()
{
    return $this->hasMany(Episode::class);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}
```

### Common Movie Fields
```php
CRUD::addField(['name' => 'name', 'type' => 'text', 'label' => 'Movie Name']);
CRUD::addField(['name' => 'slug', 'type' => 'text', 'label' => 'URL Slug']);
CRUD::addField(['name' => 'description', 'type' => 'textarea', 'label' => 'Description']);
CRUD::addField(['name' => 'poster_url', 'type' => 'url', 'label' => 'Poster URL']);
CRUD::addField(['name' => 'trailer_url', 'type' => 'url', 'label' => 'Trailer URL']);
CRUD::addField(['name' => 'year', 'type' => 'number', 'label' => 'Release Year']);
CRUD::addField(['name' => 'quality', 'type' => 'text', 'label' => 'Quality']);
CRUD::addField(['name' => 'language', 'type' => 'text', 'label' => 'Language']);
CRUD::addField(['name' => 'is_featured', 'type' => 'boolean', 'label' => 'Featured']);
```

## 🔧 Error Handling Templates

### Controller Error Handling
```php
public function update(Request $request, $id)
{
    try {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:movies,slug,' . $id,
        ]);

        $movie = Movie::findOrFail($id);
        $movie->update($request->all());

        Alert::success('Movie updated successfully')->flash();
        return redirect()->back();
        
    } catch (ValidationException $e) {
        Alert::error('Validation failed: ' . $e->getMessage())->flash();
        return redirect()->back()->withErrors($e->errors());
        
    } catch (Exception $e) {
        \Log::error('Movie update failed', [
            'movie_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        Alert::error('Something went wrong. Please try again.')->flash();
        return redirect()->back();
    }
}
```

### Field Validation Template
```php
foreach ($fields as $key => $field) {
    // Validate required field structure
    if (!isset($field['name']) || empty($field['name'])) {
        \Log::error('Invalid field structure', [
            'key' => $key,
            'field' => $field,
            'controller' => static::class
        ]);
        continue;
    }
    
    // Additional field validation
    if (!isset($field['type'])) {
        $field['type'] = 'text'; // Default type
        \Log::warning('Field missing type, using default', [
            'field_name' => $field['name']
        ]);
    }
    
    CRUD::addField($field);
}
```

## 📊 Database Patterns

### Migration Template
```php
Schema::create('movies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('poster_url')->nullable();
    $table->string('trailer_url')->nullable();
    $table->year('year')->nullable();
    $table->string('quality')->nullable();
    $table->string('language')->default('vi');
    $table->boolean('is_featured')->default(false);
    $table->json('metadata')->nullable();
    $table->timestamps();
    
    $table->index(['is_featured', 'year']);
    $table->index('slug');
});
```

### Model Template
```php
class Movie extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'poster_url', 
        'trailer_url', 'year', 'quality', 'language', 
        'is_featured', 'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'is_featured' => 'boolean',
        'year' => 'integer'
    ];
    
    protected $dates = ['created_at', 'updated_at'];
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
```

## 🎨 Frontend Quick References

### Blade Template Patterns
```blade
@extends('layouts.app')

@section('title', $movie->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>{{ $movie->name }}</h1>
            <p>{{ $movie->description }}</p>
        </div>
        <div class="col-md-4">
            @if($movie->poster_url)
                <img src="{{ $movie->poster_url }}" alt="{{ $movie->name }}" class="img-fluid">
            @endif
        </div>
    </div>
</div>
@endsection
```

### Asset Compilation
```javascript
// webpack.mix.js
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
       processCssUrls: false
   });
```

Remember: Always test your changes and follow the established patterns!
