# MimimalCMS

Ultra-lightweight and simple PHP MVC framework

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)

**Languages:** [English](README_EN.md) | [日本語](README.md)

## Overview

MimimalCMS is an ultra-lightweight PHP framework that achieves maximum flexibility with minimal code. Based on MVC architecture, it provides essential features for web application development including convention-based routing, dependency injection, and security features.

### Key Features

- 🚀 **Super Simple Routing** - Just create a file and your page is ready
- 🎯 **Flexible Route Definition** - Path parameters, multiple HTTP methods, custom validation
- 🔒 **Security Features** - CSRF protection, XSS protection, type-safe parameter validation
- 📦 **Dependency Injection Container** - Automatic constructor injection, interface binding
- 🛠️ **RESTful API Support** - Convention-based automatic routing for API/Page controllers
- 🖼️ **Image Processing** - Safe image validation, resizing, and saving
- 📝 **Simple Template Engine** - PHP-based with automatic XSS escaping

## Built With This Framework

**OpenChat Graph** - A web service that visualizes LINE OpenChat membership trends
🔗 https://openchat-review.me
📦 GitHub: https://github.com/pika-0203/Open-Chat-Graph

A production-grade web application that crawls over 150,000 OpenChats hourly, providing statistical data. Serves as a practical reference for using MimimalCMS.

## Quick Start

### Basic Usage

#### ① Create a Page Controller

**File:** [`app/Controllers/Pages/TestPageController.php`](app/Controllers/Pages/TestPageController.php)

```php
namespace App\Controllers\Pages;

class TestPageController
{
    public function index()
    {
        return view('test_content', ['message' => 'Hello World!']);
    }
}
```

That's it! Now accessible at `http://example.com/test`.

#### ② Create a View Template

**File:** [`app/Views/test_content.php`](app/Views/test_content.php)

```php
<!DOCTYPE html>
<html>
<head>
    <title>Test Page</title>
</head>
<body>
    <h1><?php echo $message ?></h1> <!-- Automatically escaped -->
</body>
</html>
```

### Routing

MimimalCMS provides two routing approaches.

**Core Implementation:** [`shadow/Kernel/Route.php`](shadow/Kernel/Route.php), [`shadow/Kernel/Dispatcher/Routing.php`](shadow/Kernel/Dispatcher/Routing.php)

#### 1. Explicit Route Definition

Explicitly specify controllers and methods for flexible routing with validation support.

**Configuration File:** [`app/Config/routing.php`](app/Config/routing.php)

```php
use Shadow\Kernel\Route;

// Basic route definition
Route::path('user/show', [UserController::class, 'show']);

// With path parameters
Route::path('user/{userId}/profile', [UserController::class, 'profile'])
    ->matchNum('userId', min: 1);

// Validation only (controller resolved by convention)
Route::path('article/{id}')
    ->matchNum('id', min: 1);
// → ArticlePageController::index($id) called by convention

Route::path('article/{id}/edit')
    ->matchNum('id', min: 1)
    ->matchStr('title', maxLen: 200);
// → ArticlePageController::edit($id, $title) called by convention

// Multiple HTTP methods
Route::path(
    'article/{id}@get@post',
    [ArticleViewController::class, 'show', 'get'],
    [ArticleUpdateApiController::class, 'update', 'post']
)
    ->matchNum('id', min: 1)
    ->matchStr('title', 'post', maxLen: 200)
    ->middleware([VerifyCsrfToken::class], 'post');

// Execute routes (at the end of app/Config/routing.php)
Route::run();
```

#### 2. Convention-Based Routing (Default Behavior)

When routes are not explicitly defined, controllers and methods are automatically resolved from URLs.

**For GET Requests:**

| URL | Controller | Method | File Location |
|-----|------------|--------|---------------|
| `/` | `App\Controllers\Pages\IndexPageController` | `index` | `app/Controllers/Pages/IndexPageController.php` |
| `/foo` | `App\Controllers\Pages\FooPageController` | `index` | `app/Controllers/Pages/FooPageController.php` |
| `/foo/bar` | `App\Controllers\Pages\FooPageController` | `bar` | `app/Controllers/Pages/FooPageController.php` |

**For POST/PUT/DELETE/PATCH Requests:**

| URL | Controller | Method | File Location |
|-----|------------|--------|---------------|
| `/foo` | `App\Controllers\Api\FooApiController` | `index` | `app/Controllers/Api/FooApiController.php` |
| `/foo/bar` | `App\Controllers\Api\FooApiController` | `bar` | `app/Controllers/Api/FooApiController.php` |

※ All HTTP methods except GET (POST/PUT/DELETE/PATCH, etc.) resolve to the same ApiController

**Convention Constraints:**
- Supports **up to 2 path segments** (`/path1/path2`)
- Path names must be **alphanumeric and underscores** only (starting with letter or `_`)
- 3rd level and beyond return 404
- Non-existent controllers/methods return 404

#### Which Should You Use?

| Case | Recommended Approach |
|------|---------------------|
| Validation or middleware needed | **Explicit Route Definition** |
| URLs with 3+ levels (e.g., `/user/123/profile`) | **Explicit Route Definition** (Required) |
| Simple API endpoints | Convention-based |

**Two Patterns for Explicit Route Definition:**

1. **With Controller**: `Route::path('/foo', [FooController::class, 'index'])`
   - Explicitly specify the controller and method

2. **Without Controller**: `Route::path('/foo')`
   - Resolve controller by convention
   - Validation and middleware can still be applied
   - Combines convenience with safety

**Real-world Example:** [oc-review-dev/app/Config/routing.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Config/routing.php)

## Core Features

### 1. Routing and Validation

**Configuration File:** [`app/Config/routing.php`](app/Config/routing.php)
**Core Implementation:** [`shadow/Kernel/Route.php`](shadow/Kernel/Route.php), [`shadow/Kernel/Validator.php`](shadow/Kernel/Validator.php)

#### Basic Route Definition

```php
use Shadow\Kernel\Route;
use App\Middleware\VerifyCsrfToken;

// Image upload route definition
Route::path('image/store@post')
    ->matchFile('file', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], emptyAble: false)
    ->matchStr('imageType', regex: '/(jpeg|png|webp)/')
    ->matchNum('imageSize', min: 0, max: 1000)
    ->fails(redirect('image'));

// Execute middleware
Route::run(VerifyCsrfToken::class);
```

#### Path Parameters

```php
// Define path parameters
Route::path('user/{userId}/profile', [UserController::class, 'show'])
    ->matchNum('userId', min: 1)
    ->matchStr('tab', regex: ['posts', 'followers', 'following'], emptyAble: true);

// Custom validation
Route::path('search', [SearchController::class, 'index'])
    ->matchStr('q', maxLen: 100)
    ->match(function(string $q) {
        // Custom validation logic
        return strlen(trim($q)) > 0;
    });
```

#### Multiple HTTP Methods

```php
// Different controllers for GET and POST
Route::path(
    'comment/{commentId}@get@post',
    [CommentViewController::class, 'show', 'get'],
    [CommentPostController::class, 'store', 'post']
)
    ->matchNum('commentId', min: 1)
    ->matchStr('text', 'post', maxLen: 1000) // POST only
    ->matchNum('limit', 'get', min: 1, max: 100, default: 20, emptyAble: true) // GET only
    ->middleware([VerifyCsrfToken::class], 'post'); // POST only
```

**Validation Methods:**
- `matchStr(string $name, ?string $requestMethod = null, ?int $maxLen = null, string|array|null $regex = null, bool $emptyAble = false, mixed $default = '')`
  - String validation (regex, max length, allow empty, default value)
- `matchNum(string $name, ?string $requestMethod = null, ?int $min = null, ?int $max = null, bool $emptyAble = false, ?int $default = 0)`
  - Numeric validation (min, max, allow empty, default value)
- `matchFile(string $name, array $allowedMimeTypes, ?int $maxFileSize = null, bool $emptyAble = false)`
  - File validation (MIME types, size limit)
- `match(\Closure $callback, ?string $requestMethod = null)`
  - Custom validation logic
- `fails(Response|\Closure $response)`
  - Response on validation failure
- `middleware(array $middlewareClasses, ?string $requestMethod = null)`
  - Apply middleware

**Real-world Example:** [oc-review-dev/app/Config/routing.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Config/routing.php)

#### Form Implementation Example

Example implementation that retains input values and displays error messages on validation failure:

**Routing Configuration:**
```php
// app/Config/routing.php
Route::path('contact@get@post', [ContactController::class, 'show', 'get'], [ContactController::class, 'store', 'post'])
    ->matchStr('name', 'post', maxLen: 50)
    ->matchStr('email', 'post', maxLen: 100, regex: '/^[^\s@]+@[^\s@]+\.[^\s@]+$/')
    ->matchStr('message', 'post', maxLen: 1000)
    ->fails(function() {
        // On validation failure, retain input values and redirect
        flash(['error' => 'There are errors in your input.']);
        return redirect('contact');
    })
    ->middleware([VerifyCsrfToken::class], 'post');
```

**Controller:**
```php
// app/Controllers/Pages/ContactController.php
class ContactController
{
    public function show()
    {
        return view('contact');
    }

    public function store(Reception $reception)
    {
        // Get validated data
        $data = [
            'name' => $reception->input('name'),
            'email' => $reception->input('email'),
            'message' => $reception->input('message'),
        ];

        // Send email, etc...

        flash(['success' => 'Your inquiry has been received.']);
        return redirect('contact');
    }
}
```

**View Template:**
```php
<!-- app/Views/contact.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Contact Form</title>
    <style>
        .error { color: red; margin: 10px 0; }
        .success { color: green; margin: 10px 0; }
        .form-error { border-color: red; }
    </style>
</head>
<body>
    <h1>Contact Form</h1>

    <?php if ($error = flash('error')): ?>
        <div class="error"><?php echo $error ?></div>
    <?php endif; ?>

    <?php if ($success = flash('success')): ?>
        <div class="success"><?php echo $success ?></div>
    <?php endif; ?>

    <form method="POST" action="/contact">
        <?php echo csrf() ?>

        <div>
            <label>Name (max 50 characters):</label>
            <input type="text" name="name" value="<?php echo old('name') ?>" required>
        </div>

        <div>
            <label>Email Address:</label>
            <input type="email" name="email" value="<?php echo old('email') ?>" required>
        </div>

        <div>
            <label>Message (max 1000 characters):</label>
            <textarea name="message" rows="5" required><?php echo old('message') ?></textarea>
        </div>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
```

**Flow:**
1. User fills out and submits the form
2. Validation is executed
3. **On validation success**: The controller's `store()` method is executed
4. **On validation failure**:
   - Input values are automatically saved to the session
   - The redirect defined in `fails()` is executed
   - When the form is redisplayed, input values can be restored using the `old()` function

### 2. Helper Functions

**Core Implementation:** [`shared/MimimalCMS_HelperFunctions.php`](shared/MimimalCMS_HelperFunctions.php)

Convenient global functions for cleaner code:

```php
// View rendering
return view('home', ['title' => 'Welcome']);

// View composition
return view('header')->make('content', ['data' => $data])->make('footer');

// JSON response
return response(['status' => 'success', 'data' => $data], 200);

// Redirect
return redirect('dashboard');
return redirect('https://example.com', 302);

// Session operations
session(['user_id' => 123, 'username' => 'john']);
$userId = session('user_id');
$username = session('username', 'guest'); // Default value

// Flash session (persists until next request)
flash(['success' => 'Saved successfully']);
$message = flash('success');

// Request data
$email = input('email');
$data = input(); // All data

// Old input values (for re-populating forms after validation errors)
$email = old('email');
$name = old('name', 'default value');

// DI container
$service = app(UserService::class);
```

### 3. Dependency Injection Container

**Core Implementation:** [`shadow/Kernel/Application.php`](shadow/Kernel/Application.php), [`shadow/Kernel/Dispatcher/ConstructorInjection.php`](shadow/Kernel/Dispatcher/ConstructorInjection.php)

#### Interface Binding

**Configuration File:** [`shared/MimimalCmsConfig.php`](shared/MimimalCmsConfig.php)

```php
class MimimalCmsConfig
{
    public static array $constructorInjectionMap = [
        UserRepositoryInterface::class => UserRepository::class,
        CacheInterface::class => RedisCache::class,
    ];
}
```

#### Automatic Constructor Injection

```php
// Repository interface
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function save(User $user): bool;
}

// Implementation class
class UserRepository implements UserRepositoryInterface
{
    public function __construct(private DBInterface $db) {}

    public function findById(int $id): ?User
    {
        $stmt = $this->db->execute("SELECT * FROM users WHERE id = :id", [':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

// Service class (auto-injection)
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CacheInterface $cache
    ) {}

    public function getUser(int $id): ?User
    {
        return $this->cache->remember("user:{$id}", function() use ($id) {
            return $this->userRepository->findById($id);
        });
    }
}

// Controller (auto-injection)
class UserController
{
    public function __construct(private UserService $userService) {}

    public function show(int $id)
    {
        $user = $this->userService->getUser($id);
        return view('user.profile', ['user' => $user]);
    }
}
```

#### Manual DI Usage

```php
use Shadow\Kernel\Application;

$app = new Application();

// Bind interface to implementation
$app->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);

// Register singleton
$app->singleton(CacheManager::class);

// Closure binding
$app->bind(Logger::class, function($app) {
    return new FileLogger('/path/to/log');
});

// Create instance (auto-resolve dependencies)
$service = $app->make(PaymentService::class);
```

**Real-world Example:** [oc-review-dev/shared/MimimalCmsConfig.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/shared/MimimalCmsConfig.php)

### 3.5 ServiceProvider Pattern

**Core Implementation:** [`shadow/Kernel/Application.php`](shadow/Kernel/Application.php)

Use ServiceProviders to dynamically bind services at application startup or for specific routes.

#### ServiceProvider Interface

```php
interface ServiceProviderInterface
{
    public function register(): void;
}
```

#### Basic ServiceProvider Implementation

```php
// app/ServiceProvider/CacheServiceProvider.php
namespace App\ServiceProvider;

use App\Services\Cache\CacheInterface;
use App\Services\Cache\RedisCache;
use App\Services\Cache\FileCache;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(): void
    {
        // Bind different implementations based on environment
        if (getenv('CACHE_DRIVER') === 'redis') {
            app()->singleton(CacheInterface::class, fn() => new RedisCache(
                host: getenv('REDIS_HOST'),
                port: getenv('REDIS_PORT')
            ));
        } else {
            app()->singleton(CacheInterface::class, fn() => new FileCache(
                cacheDir: __DIR__ . '/../../storage/cache'
            ));
        }
    }
}
```

#### Dynamic ServiceProvider Registration in Routing

Switch to different implementations for specific endpoints only:

```php
// app/Config/routing.php

// Switch to API-specific database implementation
Route::path('api/v1/{resource}', [ApiController::class, 'index'])
    ->match(function () {
        // Register API ServiceProvider
        app(ApiServiceProvider::class)->register();
        return true;
    });

// Switch to admin-specific implementation
Route::path('admin/dashboard', [AdminController::class, 'index'])
    ->match(function (AdminAuthService $auth) {
        if ($auth->authenticate()) {
            app(AdminServiceProvider::class)->register();
            return true;
        }
        return false;
    });
```

#### Practical Example: Test vs Production Environment

```php
// app/ServiceProvider/RepositoryServiceProvider.php
class RepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(): void
    {
        if (MimimalCmsConfig::$debugMode) {
            // Test environment: Use mock repositories
            app()->bind(UserRepositoryInterface::class, MockUserRepository::class);
            app()->bind(PaymentGatewayInterface::class, MockPaymentGateway::class);
        } else {
            // Production environment: Use actual implementations
            app()->bind(UserRepositoryInterface::class, UserRepository::class);
            app()->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);
        }
    }
}

// Register at startup in shared/MimimalCMS_Settings.php
app(RepositoryServiceProvider::class)->register();
```

#### Multiple Database Switching

```php
// app/ServiceProvider/DatabaseServiceProvider.php
class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function __construct(private string $database) {}

    public function register(): void
    {
        match ($this->database) {
            'mysql' => app()->bind(DBInterface::class, fn() => new MySQLDatabase()),
            'sqlite' => app()->bind(DBInterface::class, fn() => new SQLiteDatabase()),
            'postgres' => app()->bind(DBInterface::class, fn() => new PostgresDatabase()),
        };
    }
}

// Dynamically switch in routing
Route::path('analytics/{report}', [AnalyticsController::class, 'show'])
    ->match(function () {
        // Switch to SQLite database for analytics
        app(new DatabaseServiceProvider('sqlite'))->register();
        return true;
    });
```

**Real-world Example:** [Open-Chat-Graph/app/ServiceProvider/ApiDbOpenChatControllerServiceProvider.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/ServiceProvider/ApiDbOpenChatControllerServiceProvider.php)

### 4. Database (PDO Wrapper)

**Core Implementation:** [`shadow/DB.php`](shadow/DB.php)

PDO wrapper with type-safe parameter binding:

```php
use Shadow\DB;

// Execute query
$stmt = DB::execute(
    "SELECT * FROM users WHERE age > :age AND status = :status",
    [':age' => 20, ':status' => 'active']
);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// INSERT and get ID
$lastId = DB::executeAndGetLastInsertId(
    "INSERT INTO users (name, email) VALUES (:name, :email)",
    [':name' => 'John', ':email' => 'john@example.com']
);

// Transaction
DB::transaction(function() {
    DB::execute("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    DB::execute("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
});

// Get raw PDO instance
$pdo = DB::connect();
```

**Database Configuration:** [`shared/MimimalCmsConfig.php`](shared/MimimalCmsConfig.php)

```php
class MimimalCmsConfig
{
    public static string $dbHost = 'localhost';
    public static string $dbName = 'your_database';
    public static string $dbUserName = 'your_username';
    public static string $dbPassword = 'your_password';
    public static bool $dbAttrPersistent = false;
    public static string $dbCharset = 'utf8mb4';
}
```

### 5. Views and Templates

**Core Implementation:** [`shadow/Kernel/View.php`](shadow/Kernel/View.php)
**Configuration:** `MimimalCmsConfig::$viewsDir` (Default: `app/Views`)

#### Template Placement Conventions

- Templates are placed in the `app/Views` directory
- Use `.php` or `.html` extensions
- Subdirectory structure is supported
- Extensions can be omitted (automatic search priority: `.php` > `.html`)

```
app/Views/
├── layouts/
│   ├── header.php
│   └── footer.php
├── components/          # Reusable components
│   ├── navbar.php
│   └── breadcrumb.php
├── user/
│   ├── profile.php
│   └── settings.php
├── errors/              # Error pages (specific pages priority)
│   ├── 404.php         # 404-specific
│   ├── 500.php         # 500-specific
│   └── error.php       # Generic error page (fallback)
├── home.php
└── about.php
```

#### View Usage

```php
// Controller
return view('home', ['title' => 'Welcome']);

// Subdirectory template
return view('user/profile', ['user' => $user]);

// Extension omission
view('home');        // → app/Views/home.php or home.html
view('user/profile'); // → app/Views/user/profile.php

// Check view existence
if (view()->exists('custom_theme/header')) {
    return view('custom_theme/header');
} else {
    return view('default/header');
}

// View composition
return view('layouts/header')
    ->make('user/profile', ['user' => $user])
    ->make('layouts/footer');
```

#### Automatic XSS Escaping

**All variables are automatically escaped:**

```php
// Controller
return view('profile', [
    'name' => '<script>alert("XSS")</script>',
    '_rawHtml' => '<strong>Trusted HTML</strong>',  // Keys starting with _ are not escaped
    'count' => 123,                                   // Numbers as-is
    'active' => true,                                 // Booleans as-is
    'nested' => [
        'html' => '<div>test</div>',                  // Nested arrays also escaped
        '_safeHtml' => '<span>safe</span>'           // _ prefix works in nested arrays
    ]
]);
```

```php
<!-- app/Views/profile.php -->
<h1><?php echo $name ?></h1>
<!-- Output: <h1>&lt;script&gt;alert("XSS")&lt;/script&gt;</h1> -->

<div><?php echo $_rawHtml ?></div>
<!-- Output: <div><strong>Trusted HTML</strong></div> -->

<p>Count: <?php echo $count ?></p>
<!-- Output: <p>Count: 123</p> -->

<p><?php echo $nested['html'] ?></p>
<!-- Output: <p>&lt;div&gt;test&lt;/div&gt;</p> -->
```

**Cases NOT escaped:**
- **Underscore prefix**: Variables with keys starting with `_`
- **Non-strings**: Numbers, booleans, null
- **Enum values**: `\UnitEnum` instances
- **View instances**: `ViewInterface` implementations

### 6. Security Features

#### CSRF Token Protection

**Core Implementation:** [`shadow/Kernel/Cookie.php`](shadow/Kernel/Cookie.php), [`shadow/Kernel/Session.php`](shadow/Kernel/Session.php)
**Implementation Example:** [`app/Middleware/VerifyCsrfToken.php`](app/Middleware/VerifyCsrfToken.php)

```php
// Automatic verification in middleware
use Shadow\Kernel\Reception;

class VerifyCsrfToken
{
    public function handle(Reception $reception)
    {
        if ($reception->isMethod('POST')) {
            $token = $reception->input('_token');

            if (!hash_equals(session('_token'), $token)) {
                throw new UnauthorizedException('Invalid CSRF token');
            }
        }
    }
}
```

```php
<!-- Embed token in form -->
<form method="POST" action="/submit">
    <?php echo csrf() ?>
    <input type="text" name="data">
    <button type="submit">Submit</button>
</form>
```

#### String Encryption

**Core Implementation:** [`shadow/StringCryptor.php`](shadow/StringCryptor.php)

Secure encryption with HKDF + AES-256-GCM:

```php
use Shadow\StringCryptor;

$cryptor = new StringCryptor();

// Encrypt
$encrypted = $cryptor->encrypt('sensitive data');

// Decrypt
$decrypted = $cryptor->decrypt($encrypted);
```

**Configuration:** [`shared/MimimalCmsConfig.php`](shared/MimimalCmsConfig.php)

```php
class MimimalCmsConfig
{
    public static string $stringCryptorHkdfKey = 'YOUR_SECRET_KEY';
    public static string $stringCryptorOpensslKey = 'YOUR_OPENSSL_KEY';
}
```

### 7. Image Processing

**Core Implementation:** [`shadow/File/FileValidator.php`](shadow/File/FileValidator.php)

Safe image validation and resizing:

```php
use Shadow\File\FileValidator;

$validator = new FileValidator();

// Validate and resize image
$result = $validator->validateImageFileAndResize(
    $_FILES['image']['tmp_name'],
    $_FILES['image']['name'],
    maxWidth: 1200,
    maxHeight: 800,
    quality: 85
);

// Save
move_uploaded_file($result['tmp_name'], "uploads/{$result['hashed_name']}");
```

**Configuration:**

```php
class MimimalCmsConfig
{
    // Default maximum file size (KB)
    public static int $defaultMaxFileSize = 20480; // 20MB
}
```

### 8. Session Management

**Core Implementation:** [`shadow/Kernel/Session.php`](shadow/Kernel/Session.php)

```php
// Store in session
session(['user_id' => 123, 'username' => 'john']);

// Retrieve
$userId = session('user_id');
$username = session('username', 'guest'); // Default value

// Dot notation for nested access
session(['user' => ['name' => 'John', 'email' => 'john@example.com']]);
$name = session('user.name'); // 'John'

// Flash messages (persist until next request)
flash(['success' => 'Saved successfully']);
$message = flash('success');

// Delete from session
session()->forget('user_id');
session()->flush(); // Delete all

// Check existence
if (session()->has('user_id')) {
    // ...
}
```

### 9. Cookie Management

**Core Implementation:** [`shadow/Kernel/Cookie.php`](shadow/Kernel/Cookie.php)

```php
use Shadow\Kernel\Cookie;

// Store in cookie
Cookie::push('user_id', 123);
Cookie::push('username', 'john', time() + 3600); // Valid for 1 hour

// Set multiple cookies at once
Cookie::push(['user_id' => 123, 'username' => 'john'], time() + 3600);

// Retrieve
$userId = Cookie::get('user_id');
$username = Cookie::get('username', 'guest'); // Default value

// Delete cookie
Cookie::remove('user_id');
Cookie::flush(); // Delete all

// Check existence
if (Cookie::has('user_id')) {
    // ...
}

// Set cookie with options
Cookie::push(
    'token',
    'abc123',
    expires: time() + 86400,      // Expiration
    path: '/',                     // Path
    samesite: 'Strict',            // SameSite attribute
    secure: true,                  // Secure attribute
    httpOnly: true,                // HttpOnly attribute
    domain: 'example.com'          // Domain
);
```

### 10. Request Handling

**Core Implementation:** [`shadow/Kernel/Reception.php`](shadow/Kernel/Reception.php)

```php
use Shadow\Kernel\Reception;

class MyController
{
    public function handle(Reception $reception)
    {
        // Check request method
        if ($reception->isMethod('POST')) {
            // ...
        }

        // Get input data
        $email = $reception->input('email');
        $user = $reception->input('user.name'); // Dot notation

        // All input data
        $allData = $reception->input();

        // Check if input exists
        if ($reception->has('email')) {
            // ...
        }

        // Get as object
        $userObj = $reception->getObject('user');
        // Access via $userObj->name, $userObj->email

        // Check if JSON request
        if ($reception->isJson()) {
            // ...
        }
    }
}
```

### 11. Middleware

**Core Implementation:** [`shadow/Kernel/Dispatcher/MiddlewareInvoker.php`](shadow/Kernel/Dispatcher/MiddlewareInvoker.php)

Middleware executes before/after request processing:

```php
namespace App\Middleware;

use Shadow\Kernel\Reception;

class AuthMiddleware
{
    public function handle(Reception $reception)
    {
        $userId = session('user_id');

        if (!$userId) {
            throw new UnauthorizedException('Authentication required');
        }

        // Fetch user info and add to request
        $reception->overWrite(array_merge(
            $reception->input(),
            ['authenticated_user_id' => $userId]
        ));
    }
}
```

#### Middleware Return Value Behavior

The `handle()` method's behavior varies based on its return value:

| Return Value | Behavior |
|--------------|----------|
| `null` or `void` | Continue processing (to next middleware or controller) |
| `false` | Error response (returns response set in `fails()`) |
| `array` | Merge into `Reception::$inputData` and continue |
| `ViewInterface` | Display view and stop processing |
| `ResponseInterface` | Send response and stop processing |
| `\Closure` | Execute closure and stop processing |

```php
// Pattern 1: Continue processing
class LoggingMiddleware
{
    public function handle(Reception $reception)
    {
        error_log('Request: ' . $reception->method());
        return; // or return null;
    }
}

// Pattern 2: Merge data and continue
class UserDataMiddleware
{
    public function handle(Reception $reception)
    {
        $userId = session('user_id');

        // Add user info to request data
        return ['user_id' => $userId, 'is_admin' => $this->checkAdmin($userId)];
    }
}

// Pattern 3: Conditional error response
class RateLimitMiddleware
{
    public function handle(Reception $reception)
    {
        if ($this->isRateLimited()) {
            return false; // Return fails() response
        }
        return null; // Continue processing
    }
}

// Pattern 4: Redirect
class GuestMiddleware
{
    public function handle(Reception $reception)
    {
        if (session('user_id')) {
            return redirect('dashboard'); // ResponseInterface
        }
    }
}

// Pattern 5: Display error view
class MaintenanceMiddleware
{
    public function handle(Reception $reception)
    {
        if ($this->isMaintenanceMode()) {
            return view('errors/maintenance'); // ViewInterface
        }
    }
}
```

## Request Lifecycle

**Core Implementation:** [`shadow/Kernel/Kernel.php`](shadow/Kernel/Kernel.php)

```
1. Receive Request
   ↓
2. Parse URI (RequestParser)
   ↓
3. Routing (Routing::resolveController)
   ↓
4. Parameter Validation (Validator)
   ↓
5. Execute Middleware (MiddlewareInvoker)
   ↓
6. Execute Controller (ControllerInvoker + ConstructorInjection)
   ↓
7. Handle Response (ResponseHandler)
   ↓
8. Send Response
```

## Directory Structure

```
/
├── app/                          # Application code
│   ├── Config/                  # Configuration files
│   │   ├── routing.php         # Route definitions
│   │   └── AppConfig.php       # App configuration
│   ├── Controllers/            # Controllers
│   │   ├── Pages/             # Page controllers (GET)
│   │   │   └── IndexPageController.php
│   │   └── Api/               # API controllers (POST/PUT/DELETE)
│   │       └── IndexApiController.php
│   ├── Models/                # Models & repositories
│   ├── Services/              # Business logic
│   ├── Views/                 # View templates (.php/.html)
│   │   ├── layouts/
│   │   └── home.php
│   ├── Middleware/            # Middleware
│   │   └── VerifyCsrfToken.php
│   ├── Exceptions/            # Exception handlers
│   │   └── Handlers/
│   │       └── ApplicationExceptionHandler.php
│   └── Helpers/               # Helper functions
│       └── functions.php
│
├── shadow/                      # Framework core
│   ├── Kernel/                # Kernel, routing, DI container
│   │   ├── Application.php    # DI container
│   │   ├── Kernel.php         # Request handler
│   │   ├── Route.php          # Route definition
│   │   ├── View.php           # View engine
│   │   ├── Session.php        # Session management
│   │   ├── Cookie.php         # Cookie management
│   │   ├── Reception.php      # Request processing
│   │   ├── Validator.php      # Validator
│   │   └── Dispatcher/        # Dispatchers
│   │       ├── ConstructorInjection.php  # DI implementation
│   │       ├── Routing.php               # Routing resolver
│   │       └── ControllerInvoker.php     # Controller invoker
│   ├── File/                  # File & image processing
│   │   └── FileValidator.php
│   ├── DB.php                 # Database wrapper
│   └── StringCryptor.php      # Encryption
│
├── shared/                      # Shared configuration
│   ├── MimimalCMS_HelperFunctions.php  # Global helpers
│   ├── MimimalCmsConfig.php            # Framework config
│   ├── MimimalCMS_ExceptionHandler.php # Exception handler
│   └── Exceptions/                      # Common exceptions
│       ├── NotFoundException.php
│       ├── ValidationException.php
│       └── UnauthorizedException.php
│
├── public/                      # Public directory
│   ├── index.php               # Entry point
│   └── assets/                 # Static files (CSS/JS/images)
│
├── tests/                       # Test code
├── .htaccess                    # Apache configuration
├── composer.json                # Composer configuration
└── README.md
```

## Installation

### Requirements

- PHP 8.1+
- Composer
- MySQL/MariaDB (optional)
- Apache or Nginx

### Setup

```bash
# Clone repository
git clone https://github.com/mimimiku778/MimimalCMS.git
cd MimimalCMS/www/html

# Install dependencies
composer install

# Edit configuration
# Configure database connection in shared/MimimalCmsConfig.php
```

### Web Server Configuration

#### Apache (.htaccess)

The project already includes [`.htaccess`](.htaccess).

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

#### Nginx

```nginx
server {
    listen 80;
    server_name example.com;
    root /path/to/MimimalCMS/www/html/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Practical Example: User Management

### 1. Create Repository

```php
// app/Models/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
}

// app/Models/UserRepository.php
use Shadow\DB;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = DB::execute(
            "SELECT * FROM users WHERE id = :id",
            [':id' => $id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = DB::execute(
            "SELECT * FROM users WHERE email = :email",
            [':email' => $email]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        return DB::executeAndGetLastInsertId(
            "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password)",
            [
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_BCRYPT)
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $stmt = DB::execute(
            "UPDATE users SET name = :name, email = :email WHERE id = :id",
            [':id' => $id, ':name' => $data['name'], ':email' => $data['email']]
        );
        return $stmt->rowCount() > 0;
    }
}
```

### 2. Create Service

```php
// app/Services/UserService.php
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function registerUser(string $name, string $email, string $password): int
    {
        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            throw new InvalidInputException('All fields are required');
        }

        // Check email duplication
        if ($this->userRepository->findByEmail($email)) {
            throw new InvalidInputException('This email is already registered');
        }

        // Create user
        return $this->userRepository->create([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
    }

    public function getUser(int $id): array
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new NotFoundException('User not found');
        }

        return $user;
    }
}
```

### 3. Create Page Controller

```php
// app/Controllers/Pages/UserPageController.php
namespace App\Controllers\Pages;

class UserPageController
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index()
    {
        // Display registration form
        return view('user/register');
    }

    public function profile(int $userId)
    {
        try {
            $user = $this->userService->getUser($userId);
            return view('user/profile', ['user' => $user]);
        } catch (NotFoundException $e) {
            return view('errors/404');
        }
    }
}
```

### 4. Create API Controller

```php
// app/Controllers/Api/UserApiController.php
namespace App\Controllers\Api;

class UserApiController
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index()
    {
        $name = input('name');
        $email = input('email');
        $password = input('password');

        try {
            $userId = $this->userService->registerUser($name, $email, $password);
            return response([
                'id' => $userId,
                'message' => 'User created successfully'
            ], 201);
        } catch (InvalidInputException $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }
}
```

### 5. Configure Routing

```php
// app/Config/routing.php
use Shadow\Kernel\Route;
use App\Middleware\VerifyCsrfToken;

// User registration (GET and POST)
Route::path(
    'user@get@post',
    [UserPageController::class, 'index', 'get'],
    [UserApiController::class, 'index', 'post']
)
    ->matchStr('name', 'post', maxLen: 100)
    ->matchStr('email', 'post', maxLen: 255, regex: '/^[^\s@]+@[^\s@]+\.[^\s@]+$/')
    ->matchStr('password', 'post', maxLen: 255, regex: '/^.{8,}$/') // Minimum 8 characters
    ->middleware([VerifyCsrfToken::class], 'post')
    ->fails(response(['error' => 'Invalid input'], 400));

// User profile
Route::path('user/{userId}/profile', [UserPageController::class, 'profile'])
    ->matchNum('userId', min: 1);

Route::run();
```

### 6. Create View Templates

```php
<!-- app/Views/user/register.php -->
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
    <h1>User Registration</h1>

    <?php if ($message = flash('success')): ?>
        <div class="success"><?php echo $message ?></div>
    <?php endif; ?>

    <?php if ($error = flash('error')): ?>
        <div class="error"><?php echo $error ?></div>
    <?php endif; ?>

    <form method="POST" action="/user">
        <?php echo csrf() ?>

        <div>
            <label>Name:</label>
            <input type="text" name="name" required>
        </div>

        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>Password:</label>
            <input type="password" name="password" required minlength="8">
        </div>

        <button type="submit">Register</button>
    </form>
</body>
</html>
```

```php
<!-- app/Views/user/profile.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $user['name'] ?>'s Profile</title>
</head>
<body>
    <h1><?php echo $user['name'] ?></h1>
    <p>Email: <?php echo $user['email'] ?></p>
    <p>Registered: <?php echo $user['created_at'] ?></p>
</body>
</html>
```

### 7. Configure DI Container

```php
// shared/MimimalCmsConfig.php
class MimimalCmsConfig
{
    public static array $constructorInjectionMap = [
        // Repository bindings
        UserRepositoryInterface::class => UserRepository::class,

        // Framework standard bindings
        \Shadow\Kernel\ViewInterface::class => \Shadow\Kernel\View::class,
        \Shadow\DBInterface::class => \Shadow\DB::class,
    ];

    // Other settings...
}
```

## Exception Handling

**Core Implementation:** [`shared/MimimalCMS_ExceptionHandler.php`](shared/MimimalCMS_ExceptionHandler.php)

Unified error handling with custom exception handler:

```php
// app/Exceptions/Handlers/ApplicationExceptionHandler.php
namespace App\Exceptions\Handlers;

class ApplicationExceptionHandler implements ApplicationExceptionHandlerInterface
{
    public function handle(\Throwable $e)
    {
        // Log error
        error_log($e->getMessage());

        // Show details in development
        if (MimimalCmsConfig::$debugMode) {
            throw $e;
        }

        // Generic error page in production
        return view('errors/error', [
            'message' => 'An error occurred',
            'code' => $e->getCode()
        ]);
    }
}
```

**HTTP Error Mapping:** [`shared/MimimalCmsConfig.php`](shared/MimimalCmsConfig.php)

```php
class MimimalCmsConfig
{
    public static array $httpErrors = [
        NotFoundException::class =>         ['httpCode' => 404, 'log' => false, 'httpStatusMessage' => 'Not Found'],
        ValidationException::class =>       ['httpCode' => 400, 'log' => true,  'httpStatusMessage' => 'Bad Request'],
        UnauthorizedException::class =>     ['httpCode' => 401, 'log' => true,  'httpStatusMessage' => 'Unauthorized'],
        ThrottleRequestsException::class => ['httpCode' => 429, 'log' => true,  'httpStatusMessage' => 'Too Many Requests'],
    ];

    // Error log configuration
    public static string $exceptionLogDirectory = __DIR__ . '/../storage/exception.log';
    public static bool $exceptionHandlerDisplayErrorTraceDetails = true;  // Production: false
}
```

#### Error Page Cascade Search

When an error occurs, error pages are searched and displayed in the following priority order:

```
1. app/Views/errors/{httpCode}.php  (e.g., 404.php, 500.php)
   ↓ If not found
2. app/Views/errors/error.php       (Generic error page)
   ↓ If not found
3. Default error message
```

**Error Page Implementation Examples:**

```php
<!-- app/Views/errors/404.php -->
<!DOCTYPE html>
<html>
<head>
    <title>404 Not Found</title>
</head>
<body>
    <h1>Page Not Found</h1>
    <p>The page you are looking for does not exist or has been moved.</p>
    <a href="/">Back to Home</a>
</body>
</html>
```

```php
<!-- app/Views/errors/error.php (Generic) -->
<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
</head>
<body>
    <h1>An Error Occurred</h1>
    <p><?php echo $httpStatusMessage ?? 'Internal Server Error' ?></p>
    <?php if (isset($detailsMessage)): ?>
        <p><?php echo $detailsMessage ?></p>
    <?php endif; ?>
</body>
</html>
```

## Documentation

Detailed API documentation:
- [Application (DI Container)](https://mimimiku778.github.io/MimimalCMS-document/packages/Application.html)
- [Helper Functions](https://mimimiku778.github.io/MimimalCMS-document/files/shared-mimimalcms-helperfunctions.html)

## License

MIT License - See [LICENSE.md](LICENSE.md) for details.

## Author

mimimiku778 <0203.sub@gmail.com>

## Links

- [OpenChat Graph (Implementation Example)](https://github.com/pika-0203/Open-Chat-Graph)
- [GitHub Repository](https://github.com/mimimiku778/MimimalCMS)
- [GitHub Issues](https://github.com/mimimiku778/MimimalCMS/issues)
