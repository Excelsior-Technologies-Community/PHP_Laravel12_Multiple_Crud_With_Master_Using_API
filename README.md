# PHP_Laravel12_Multiple_Crud_With_Master_Using_API
---

## What is “Multiple CRUD with Master” (Easy Words)

We will create **Master–Child (Parent–Child) CRUD** using **API only**.

### Example Used


Category  (MASTER)
   ↓
Products  (MULTIPLE CRUD)




### Meaning

- One **Category** can have **many Products**
- Relationship: **One-to-Many**
- No Blade, No Views
- API-based project only

### CRUD Operations

- Create  
- Read  
- Update  
- Delete  

---

## 🛠 Technologies Used

- PHP 8+
- Laravel 12
- MySQL
- REST API

---

## STEP 1: Create New Laravel 12 Project

### Command

```
composer create-project laravel/laravel:^12.0 PHP_Laravel12_Multiple_Crud_With_Master_Using_API
```

### Go inside project:
```

cd PHP_Laravel12_Multiple_Crud_With_Master_Using_API
```


### Run server:
```

php artisan serve

```


## STEP 2: Database Configuration

### Open .env
```

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multiple_crud_api
DB_USERNAME=root
DB_PASSWORD=
```


### Create database in phpMyAdmin:
```
multiple_crud_api
```

## STEP 3: Create Models + Migrations

### Create Category
```
php artisan make:model Category -m
```

### Create Product
```
php artisan make:model Product -m

```

## STEP 4: Migrations Code

 ### categories table

database/migrations/xxxx_create_categories_table.php
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```


### products table

database/migrations/xxxx_create_products_table.php
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->decimal('price', 8, 2);
        $table->integer('quantity');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

```

### Run Migration:
```
php artisan migrate
```



## STEP 5: Define Relationships (VERY IMPORTANT)


 ### Category Model

app/Models/Category.php
```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    // One category has many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

### Product Model

app/Models/Product.php
```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'quantity'
    ];

    // Product belongs to category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

```


## STEP 6: Create API Controllers
```

php artisan make:controller Api/CategoryController
php artisan make:controller Api/ProductController

```
### STEP 7: Category CRUD API

 ## CategoryController

app/Http/Controllers/Api/CategoryController.php

```
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET: All Categories
    public function index()
    {
        return response()->json(Category::all());
    }

    // POST: Create Category
    public function store(Request $request)
    {
        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json($category, 201);
    }

    // GET: Single Category
    public function show($id)
    {
        return response()->json(Category::findOrFail($id));
    }

    // PUT: Update Category
// POST: Update Category (instead of PUT)
public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);
    $category->update($request->all());

    return response()->json($category);
}

// POST: Delete Category (instead of DELETE)
public function destroy($id)
{
    Category::findOrFail($id)->delete();
    return response()->json(['message' => 'Category Deleted']);
}

    // GET: Category with Products (MASTER)
    public function categoryProducts($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return response()->json($category);
    }

    public function allProductsWithCategory()
{
    // Get all products with their category using Eloquent relationship
    $products = \App\Models\Product::with('category')->get();

    // Format response nicely
    $data = $products->map(function($product) {
        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'category_id' => $product->category->id ?? null,
            'category_name' => $product->category->name ?? null,
        ];
    });

    return response()->json([
        'total' => $data->count(),
        'products' => $data
    ]);
}

}
```


## STEP 8: Product CRUD API

 ### ProductController

app/Http/Controllers/Api/ProductController.php

```
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET: All Products
    public function index()
    {
        $products = Product::with('category')->get();

        return response()->json([
            'status' => true,
            'data' => $products->map(function ($product) {
                return [
                    'id'            => $product->id,
                    'name'          => $product->name,
                    'price'         => $product->price,
                    'quantity'      => $product->quantity,
                    'category_id'   => $product->category_id,
                    'category_name' => $product->category->name ?? null,
                ];
            })
        ]);
    }

    // POST: Create Product
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required',
            'price'       => 'required|numeric',
            'quantity'    => 'required|numeric',
        ]);

        $product = Product::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'price'       => $request->price,
            'quantity'    => $request->quantity,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    // GET: Single Product
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => [
                'id'            => $product->id,
                'name'          => $product->name,
                'price'         => $product->price,
                'quantity'      => $product->quantity,
                'category_id'   => $product->category_id,
                'category_name' => $product->category->name ?? null,
            ]
        ]);
    }

    // POST: Update Product
    public function update(Request $request, $id)
    {
        $request->validate([
            
            'name'        => 'required',
            'price'       => 'required|numeric',
            'quantity'    => 'required|numeric',
        ]);

        $product = Product::findOrFail($id);

        $product->update([
            
            'name'        => $request->name,
            'price'       => $request->price,
            'quantity'    => $request->quantity,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    // POST: Delete Product
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}


```

## STEP 9: API Routes

 ### routes/api.php

```
<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| CATEGORY APIs (GET + POST ONLY)
|--------------------------------------------------------------------------
*/

// CREATE
Route::post('/categories/create', [CategoryController::class, 'store']);

// READ
Route::get('/categories/categoriesList', [CategoryController::class, 'index']);
Route::get('/categories/view/{id}', [CategoryController::class, 'show']);

// UPDATE (POST instead of PUT)
Route::post('/categories/update/{id}', [CategoryController::class, 'update']);

// DELETE (POST instead of DELETE)
Route::post('/categories/delete/{id}', [CategoryController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| PRODUCT APIs (GET + POST ONLY)
|--------------------------------------------------------------------------
*/

// CREATE
Route::post('/products/create', [ProductController::class, 'store']);

// READ
Route::get('/products/productsList', [ProductController::class, 'index']);
Route::get('/products/view/{id}', [ProductController::class, 'show']);

// UPDATE
Route::post('/products/update/{id}', [ProductController::class, 'update']);

// DELETE
Route::post('/products/delete/{id}', [ProductController::class, 'destroy']);


/*
|--------------------------------------------------------------------------
| MASTER → CHILD API
|--------------------------------------------------------------------------
| Get all products under a specific category
| GET method only
|--------------------------------------------------------------------------
*/

// Category products by ID
Route::get('/categoriesWiseProducts/{id}', [CategoryController::class, 'categoryProducts'])
     ->whereNumber('id');


```

## Step 10 - Run Project

### Command:

```
php artisan serve

```

### Server should show:
```
http://127.0.0.1:8000
```




## How to Use CRUD in POSTMAN (Laravel 12 API)


### Project base URL (example):
```
http://127.0.0.1:8000/api
```

## CATEGORY CRUD IN POSTMAN



1. ### CREATE CATEGORY
 API
```
POST /categories/create
```
 Full URL
```
http://127.0.0.1:8000/api/categories/create
```

Postman Settings:

Method: POST

Body → raw

Type: JSON


 Body
```
{
  "name": "Electronics"
}
```

Expected Response:

<img width="1446" height="914" alt="Screenshot 2025-12-23 125441" src="https://github.com/user-attachments/assets/d52482f8-9b05-407f-941c-ea3eee8bf91e" />




2. ### CATEGORY LIST
 API
```
GET /categories/categoriesList
```
 URL
```
http://127.0.0.1:8000/api/categories/categoriesList
```
 Method

GET

No body

 Expected Response:


<img width="1445" height="917" alt="Screenshot 2025-12-23 131526" src="https://github.com/user-attachments/assets/9f0ebf75-e51d-47cb-9e3f-955ef4966d52" />




3. ### VIEW SINGLE CATEGORY
 API
```
GET /categories/view/{id}
```
 Example
```
http://127.0.0.1:8000/api/categories/view/1
```
 Response:


<img width="1442" height="911" alt="Screenshot 2025-12-23 125605" src="https://github.com/user-attachments/assets/d88c46de-7b5c-4b9a-b98f-868440eae0a6" />




4. ### UPDATE CATEGORY

 API
```
POST /categories/update/{id}
```
 URL
```
http://127.0.0.1:8000/api/categories/update/2
```
 Body (JSON)
```
{
  "name": "Best-Electronics"
}
```
 Response:


<img width="1442" height="912" alt="Screenshot 2025-12-23 125711" src="https://github.com/user-attachments/assets/27475c46-54c5-4ae2-b4c4-67bec527255d" />


5. ### DELETE CATEGORY
 API
```
POST /categories/delete/{id}
```
 URL
```
http://127.0.0.1:8000/api/categories/delete/1
```
 Response:



<img width="1431" height="912" alt="image" src="https://github.com/user-attachments/assets/2d2ce46d-a1bb-4ceb-a363-7a326c84ac00" />




## PRODUCT APIs (Step by Step)



6. ### CREATE PRODUCT
 API
```
POST /products/create
```
 URL
```
http://127.0.0.1:8000/api/products/create
```
Body (JSON)
```
{
  "category_id": 3,
  "name": "Laptop",
  "price": 55000,
  "quantity": 10
}
```

Response:

<img width="1438" height="911" alt="image" src="https://github.com/user-attachments/assets/82343f4d-714d-486b-8acc-44ef22cd3a12" />




7. ### PRODUCT LIST
API
```
GET /products/productsList
```
 URL
```
http://127.0.0.1:8000/api/products/productsList
```
 Response:


<img width="1443" height="910" alt="image" src="https://github.com/user-attachments/assets/e11e0c9c-88a3-467f-abb2-a693d2c0a2b6" />




8. ### VIEW SINGLE PRODUCT
 API
```
GET /products/view/{id}
```
 URL
```
http://127.0.0.1:8000/api/products/view/1
```
 Response:

<img width="1437" height="912" alt="image" src="https://github.com/user-attachments/assets/4516d2e3-99d8-43fb-a76c-0546221d2c59" />




9. ### UPDATE PRODUCT
 API
```
POST /products/update/{id}
```
 URL
```
http://127.0.0.1:8000/api/products/update/1
```
 Body
```
{
  "name": "MacBook Laptop",
  "price": 75000,
  "quantity": 8
}
```

Response:

<img width="1442" height="905" alt="image" src="https://github.com/user-attachments/assets/f1575e26-35bd-4f23-aa73-b18ce07d0536" />





10. ### DELETE PRODUCT
 API
```
POST /products/delete/{id}
```
 URL
```
http://127.0.0.1:8000/api/products/delete/1
```
 Response:


<img width="1442" height="916" alt="image" src="https://github.com/user-attachments/assets/423cc307-a27e-47fe-bf28-ef55d9c913c4" />



## MASTER → CHILD (IMPORTANT)


11. ### CATEGORY WISE PRODUCTS
 API
```
GET /categoriesWiseProducts/{id}
```
 URL
```
http://127.0.0.1:8000/api/categoriesWiseProducts/1
```
 Expected Response:


<img width="1445" height="915" alt="Screenshot 2025-12-23 134404" src="https://github.com/user-attachments/assets/8fbe7417-2786-461d-a9ea-2765a72d19f5" />





# Full Project Structure

```

PHP_Laravel12_Multiple_Crud_With_Master_Using_API
│
├── app
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── Api
│   │   │   │   ├── CategoryController.php
│   │   │   │   └── ProductController.php
│   │   │   │
│   │   │   └── Controller.php
│   │   │
│   │   └── Middleware
│   │
│   ├── Models
│   │   ├── Category.php
│   │   └── Product.php
│   │
│   └── Providers
│
├── bootstrap
│   └── app.php
│
├── config
│   ├── app.php
│   ├── database.php
│   └── cors.php
│
├── database
│   ├── factories
│   │
│   ├── migrations
│   │   ├── xxxx_xx_xx_create_categories_table.php
│   │   └── xxxx_xx_xx_create_products_table.php
│   │
│   └── seeders
│
├── public
│   └── index.php
│
├── routes
│   ├── api.php          ←  MAIN API ROUTES
│   ├── console.php
│   └── web.php          ← (Not used – API only)
│
├── storage
│   ├── app
│   ├── framework
│   └── logs
│
├── tests
│
├── vendor
│
├── .env                 ← Database config
├── .env.example
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
└── README.md            ←  Your README file


```



