# timezones
Example Create Laravel 5 Package

# Create Laravel Package

### Package folder and name
create a package folder: /packages/[creator]/[package_name]. Then inside of it we need to create another folder called /src.

```
--app
--packages
----[CREATOR/VENDOR] (ricogoh)
------[PACKAGE_NAME] (timezones)
--------src
            
Composer.json file for the package
composer init
Loading package via main composer.json and PSR-4
"autoload": {
    "classmap": [
        "database"
    ],
    "psr-4": {
        "App\\": "app/",
        "Ricogoh\\Timezones\\": "packages/ricogoh/timezones/src"
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
It will generate a file called TimezonesServiceProvider.php in folder app/Providers – then we should move that file to our folder /packages/ricogoh/timezones/src. After that don’t forget to change the namespace of the Provider class – it should be the same as we specified in main composer.json file – in our case, Ricogoh\Timezones:

config/app.php:

```php
'providers' => [
        Ricogoh\Timezones\TimezonesServiceProvider::class,
```

### Create a Controller

packages\ricogoh\timezones\src\TimezonesController.php

```php
namespace Ricogoh\Timezones;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TimezonesController extends Controller
{
    public function index($timezone = NULL)
    {
        $current_time = ($timezone)
            ? Carbon::now(str_replace('-', '/', $timezone))
            : Carbon::now();
        return view('timezones::time', compact('current_time'));
    }
}
```
                
### Create our Routes.php file
packages\ricogoh\timezones\src\routes.php:

```php
Route::get('timezones/{timezone?}',
    'ricogoh\timezones\TimezonesController@index');
```

Now, how does Laravel know about this routes.php file and our Controller? This is where our Service Provider comes in: we add these lines to its method register():

```php
class TimezonesServiceProvider extends ServiceProvider
{
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('Ricogoh\Timezones\TimezonesController');
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
And the last step – what if we want user of our package to customize that view himself? That’s a pretty common scenario – we provide a basic template, but then user wants it to look as his app, not ours.

But they wouldn’t go to our package folder and edit views directly – that would ruin all future updates process. That means we should copy our views into Laravel folder resources/views. To do that, we add this line to Service Provider’s boot() method:

```php
$this->publishes([
    __DIR__.'/views' => base_path('resources/views/ricogoh/timezones'),
]);
```
            
Then:
```php
php artisan vendor:publish
```
