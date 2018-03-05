<!DOCTYPE html>
<html>
<head>
    <title>Create Laravel Package</title>
</head>
<body>
    <div class="container">

        <h1>Create Laravel Package</h1>

        <div>
            <h3>Package folder and name</h3>
            <p>
                create a package folder: /packages/[creator]/[package_name]. Then inside of it we need to create another folder called /src.
            </p>
            <pre>
--app
--packages
----[CREATOR/VENDOR]
------[PACKAGE_NAME]
--------src
            </pre>
        </div>

        <div>
            <h3>Composer.json file for the package</h3>
            <pre>composer init</pre>
        </div>

        <div>
            <h3>Loading package via main composer.json and PSR-4</h3>
            <pre>
"autoload": {
    "classmap": [
        "database"
    ],
    "psr-4": {
        "App\\": "app/",
        "Laraveldaily\\Timezones\\": "packages/laraveldaily/timezones/src"
    }
},
...

composer dump-autoload
            </pre>
        </div>

        <div>
            <h3>Creating a Service Provider</h3>
            <pre>php artisan make:provider TimezonesServiceProvider</pre>
            <p>It will generate a file called TimezonesServiceProvider.php in folder app/Providers – then we should move that file to our
            folder /packages/laraveldaily/timezones/src. After that don’t forget to change the namespace of the Provider class – it should
            be the same as we specified in main composer.json file – in our case, Laraveldaily\Timezones:</p>
            <p>
                config/app.php:
                <pre>
'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Foundation\Providers\ArtisanServiceProvider::class,
        // ... other providers
        Illuminate\View\ViewServiceProvider::class,
        Laraveldaily\Timezones\TimezonesServiceProvider::class,
                </pre>
            </p>
        </div>

        <div>
            <h3>Create a Controller</h3>
            <div>
                packages\laraveldaily\timezones\src\TimezonesController.php
                <pre>
namespace Laraveldaily\Timezones;

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
                </pre>
            </div>

        </div>

        <div>
            <h3>Create our Routes.php file</h3>
            <p>packages\laraveldaily\timezones\src\routes.php:</p>
            <pre>
Route::get('timezones/{timezone?}',
    'laraveldaily\timezones\TimezonesController@index');
            </pre>
            <p>
                Now, how does Laravel know about this routes.php file and our Controller? This is where our Service Provider comes in: we
                add these lines to its method register():
            </p>
            <pre>
class TimezonesServiceProvider extends ServiceProvider
{
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('Laraveldaily\Timezones\TimezonesController');
    }
}

            </pre>
        </div>

        <div>
            <h3>That’s it – load URL in the browser!</h3>
        </div>

        <div>
            <h3>What about the Views?</h3>
            <p>
                src/views/time.blade.php:
            </p>
            <pre>

    &lt;div class="container"&gt;
        &lt;div class="content"&gt;
            &lt;div class="title"&gt;@{{ $current_time }}&lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;

            </pre>

            <p>
                Now, let’s return to our Service Provider and this time we will use boot() method by adding a command, where to load our
                views from. The second parameter is our Namespace which we will use in the next step.
            </p>
            <pre>
class TimezonesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'timezones');
    }
            </pre>

            <p>
                Next thing – we need to change our Controller to load this view with a parameter. Now, notice that we are loading the view
                with the specific namespace of our package that we just specified in Service Provider.
            </p>
            <pre>
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
            </pre>
        </div>

        <div>
            <h3>Publishing the Views</h3>
            <p>
                And the last step – what if we want user of our package to customize that view himself? That’s a pretty common scenario – we provide a basic template, but then user wants it to look as his app, not ours.
            </p>
            <p>
                But they wouldn’t go to our package folder and edit views directly – that would ruin all future updates process. That means we should copy our views into Laravel folder resources/views. To do that, we add this line to Service Provider’s boot() method:
            </p>
            <pre>
$this->publishes([
    __DIR__.'/views' => base_path('resources/views/laraveldaily/timezones'),
]);
            </pre>
            <p>
                Then:
                <pre>php artisan vendor:publish</pre>
            </p>
        </div>
    </div>
</body>
</html>
