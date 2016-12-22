<?php
/**
 * Laravel IDE Helper Generator
 * @author    Barry vd. Heuvel <barryvdh@gmail.com>
 * @copyright 2014 Barry vd. Heuvel / Fruitcake Studio (http://www.fruitcakestudio.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/barryvdh/laravel-ide-helper
 */

namespace hurongsheng\LaravelRouteDoc;

use Illuminate\Support\ServiceProvider;
use App;

class RouteDocServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'RouteDoc');
        $this->publishes([__DIR__ . '/config.php' => config_path('route_doc.php')]);
        $this->publishes([__DIR__ . '/Migrations/' => database_path('/migrations')]);
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        App::bind('RouteDoc', function () {
            return new RouteDoc;
        });
    }

    public function provides()
    {
        return ['RouteDoc'];
    }

}
