# Example Create Laravel 5 Package

### Package folder and name
create a package folder: /packages/[creator]/[package_name]. Then inside of it we need to create another folder called /src.
```
--app
--packages
----[CREATOR/VENDOR] (ricogoh)
------[PACKAGE_NAME] (timezones)
--------src
```         
Create composer.json file for the package
```
composer init
```
Loading package via main composer.json and PSR-4
```json
"autoload": {
    "classmap": [
        "database"
    ],
    "psr-4": {
        "App\\": "app/",
        "RicoGoh\\Timezones\\": "packages/ricogoh/timezones/src"
    }
},
```
```php
composer dump-autoload
```
### Creating a Service Provider
```php
php artisan make:provider TimezonesServiceProvider
```
It will generate a file called TimezonesServiceProvider.php in folder app/Providers – then we should move that file to our folder /packages/ricogoh/timezones/src. After that don’t forget to change the namespace of the Provider class – it should be the same as we specified in main composer.json file – in our case, RicoGoh\Timezones:

config/app.php:

```php
'providers' => [
        RicoGoh\Timezones\TimezonesServiceProvider::class,
```
### Create a Controller
packages\ricogoh\timezones\src\TimezonesController.php

```php
namespace RicoGoh\Timezones;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TimezonesController extends Controller
{
    public function index($timezone = NULL)
    {
        $current_time = ($timezone)
            ? Carbon::now(str_replace('-', '/', $timezone))
            : Carbon::now();
        return view('ricogoh/timezones::time', compact('current_time'));
    }
}
```
### Create our Routes.php file
packages\ricogoh\timezones\src\routes.php:

```php
Route::get('timezones/{timezone?}',
    'RicoGoh\Timezones\TimezonesController@index');
```

Now, how does Laravel know about this routes.php file and our Controller? This is where our Service Provider comes in: 
we add these lines to its method register():
```php
class TimezonesServiceProvider extends ServiceProvider
{
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('RicoGoh\Timezones\TimezonesController');
    }
}
```
### That’s it – load URL in the browser!

### What about the Views?

src/views/time.blade.php:
```html
    <div class="container">
        <div class="content">
            <div class="title">{{ $current_time }}</div>
        </div>
    </div>
```

Now, let’s return to our Service Provider and this time we will use boot() method by adding a command, where to load our views from. The second parameter is our Namespace which we will use in the next step.
```php
class TimezonesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'ricogoh/timezones');
    }
```
            
Next thing – we need to change our Controller to load this view with a parameter. Now, notice that we are loading the view with the specific namespace of our package that we just specified in Service Provider.
```php
class TimezonesController extends Controller
{

    public function index($timezone = NULL)
    {
        $current_time = ($timezone)
            ? Carbon::now(str_replace('-', '/', $timezone))
            : Carbon::now();
        return view('ricogoh/timezones::time', compact('current_time'));
    }

}
```
### Publishing the Views
To prepare publish views folders into Laravel folder, add this line to Service Provider’s boot() method:

```php
$this->publishes([
    __DIR__.'/views' => resource_path('views/vendor/ricogoh/timezones'),
]);
```       
To publish and edit views in Laravel, run below command. It will copy views to resources/views/vendor/ricogoh/timezones.
```php
php artisan vendor:publish
```
