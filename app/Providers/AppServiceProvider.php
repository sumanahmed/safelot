<?php

namespace App\Providers;

use App\Services\FormValidation\IFormValidation;
use App\Services\FormValidation\LoginFormService;
use App\Services\FormValidation\RegistrationFormService;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IFormValidation::class, function ($app) {
            $request = $app->make(Request::class);
            
            /*
             * Get controller name from Request
             */
                $routeArray = $request->route()->getAction();
                $controllerAction = class_basename($routeArray['controller']);
                list($controller, $action) = explode('@', $controllerAction);
            /*
             * End of get controller name from Request
             */
            
            switch ($controller) {
                case 'AuthController':
                    if ($request->input('first_name')) {
                        return new RegistrationFormService();
                    } else {
                        return new LoginFormService();
                    }
                default:
                   return '';
            }
            
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }
}
