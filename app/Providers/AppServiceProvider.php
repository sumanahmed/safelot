<?php

namespace App\Providers;

use App\Services\FormValidation\{ 
    DealershipFormService,
    IFormValidation,
    LoginFormService,
    RegistrationFormService,
    UserFormService,
    VehicleFormService
};
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
                case 'UserController':
                    return new UserFormService();
                case 'DealershipController':
                    return new DealershipFormService();
                case 'VehicleController':
                    return new VehicleFormService();
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
