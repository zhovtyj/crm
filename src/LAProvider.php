<?php
/**
 * Code generated using Crm
 * Help: http://Crm.com
 * Crm is open-sourced software licensed under the MIT license.
 * Developed by: Zhovtyj IT Solutions
 * Developer Website: http://Zhovtyjitsolutions.com
 */

namespace Zhovtyj\Crm;

use Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

use Zhovtyj\Crm\Helpers\LAHelper;

/**
 * Class LAProvider
 * @package Zhovtyj\Crm
 *
 * This is Crm Service Provider which looks after managing aliases, other required providers, blade directives
 * and Commands.
 */
class LAProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // @mkdir(base_path('resources/Crm'));
        // @mkdir(base_path('database/migrations/Crm'));
        /*
        $this->publishes([
            __DIR__.'/Templates' => base_path('resources/Crm'),
            __DIR__.'/config.php' => base_path('config/Crm.php'),
            __DIR__.'/Migrations' => base_path('database/migrations/Crm')
        ]);
        */
        //echo "Crm Migrations started...";
        // Artisan::call('migrate', ['--path' => "vendor/Zhovtyj/Crm/src/Migrations/"]);
        //echo "Migrations completed !!!.";
        // Execute by php artisan vendor:publish --provider="Zhovtyj\Crm\LAProvider"
        
        /*
        |--------------------------------------------------------------------------
        | Blade Directives for Entrust not working in Laravel 5.3
        |--------------------------------------------------------------------------
        */
        if(LAHelper::laravel_ver() >= 5.3 || LAHelper::laravel_ver() == 5.4) {
            
            // Call to Entrust::hasRole
            Blade::directive('role', function ($expression) {
                return "<?php if (\\Entrust::hasRole({$expression})) : ?>";
            });
            
            // Call to Entrust::can
            Blade::directive('permission', function ($expression) {
                return "<?php if (\\Entrust::can({$expression})) : ?>";
            });
            
            // Call to Entrust::ability
            Blade::directive('ability', function ($expression) {
                return "<?php if (\\Entrust::ability({$expression})) : ?>";
            });
        }
    }
    
    /**
     * Register the application services including routes, Required Providers, Alias, Controllers, Blade Directives
     * and Commands.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/routes.php';
        
        // For LAEditor
        if(file_exists(__DIR__ . '/../../laeditor')) {
            include __DIR__ . '/../../laeditor/src/routes.php';
        }
        
        /*
        |--------------------------------------------------------------------------
        | Providers
        |--------------------------------------------------------------------------
        */
        
        // Collective HTML & Form Helper
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);
        // For Datatables
        $this->app->register(\Yajra\Datatables\DatatablesServiceProvider::class);
        // For Gravatar
        $this->app->register(\Creativeorange\Gravatar\GravatarServiceProvider::class);
        // For Entrust
        $this->app->register(\Zizaco\Entrust\EntrustServiceProvider::class);
        // For Spatie Backup
        $this->app->register(\Spatie\Backup\BackupServiceProvider::class);
        
        /*
        |--------------------------------------------------------------------------
        | Register the Alias
        |--------------------------------------------------------------------------
        */
        
        $loader = AliasLoader::getInstance();
        
        // Collective HTML & Form Helper
        $loader->alias('Form', \Collective\Html\FormFacade::class);
        $loader->alias('HTML', \Collective\Html\HtmlFacade::class);
        
        // For Gravatar User Profile Pics
        $loader->alias('Gravatar', \Creativeorange\Gravatar\Facades\Gravatar::class);
        
        // For Crm Code Generation
        $loader->alias('CodeGenerator', \Zhovtyj\Crm\CodeGenerator::class);
        
        // For Crm Form Helper
        $loader->alias('LAFormMaker', \Zhovtyj\Crm\LAFormMaker::class);
        
        // For Crm Helper
        $loader->alias('LAHelper', \Zhovtyj\Crm\Helpers\LAHelper::class);
        
        // Crm Module Model 
        $loader->alias('Module', \Zhovtyj\Crm\Models\Module::class);
        
        // For Crm Configuration Model
        $loader->alias('LAConfigs', \Zhovtyj\Crm\Models\LAConfigs::class);
        
        // For Entrust
        $loader->alias('Entrust', \Zizaco\Entrust\EntrustFacade::class);
        $loader->alias('role', \Zizaco\Entrust\Middleware\EntrustRole::class);
        $loader->alias('permission', \Zizaco\Entrust\Middleware\EntrustPermission::class);
        $loader->alias('ability', \Zizaco\Entrust\Middleware\EntrustAbility::class);
        
        /*
        |--------------------------------------------------------------------------
        | Register the Controllers
        |--------------------------------------------------------------------------
        */
        
        $this->app->make('Zhovtyj\Crm\Controllers\ModuleController');
        $this->app->make('Zhovtyj\Crm\Controllers\FieldController');
        $this->app->make('Zhovtyj\Crm\Controllers\MenuController');
        
        // For LAEditor
        if(file_exists(__DIR__ . '/../../laeditor')) {
            $this->app->make('Zhovtyj\Laeditor\Controllers\CodeEditorController');
        }
        
        /*
        |--------------------------------------------------------------------------
        | Blade Directives
        |--------------------------------------------------------------------------
        */
        
        // LAForm Input Maker
        Blade::directive('la_input', function ($expression) {
            if(LAHelper::laravel_ver() >= 5.3 || LAHelper::laravel_ver() == 5.4) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LAFormMaker::input$expression; ?>";
        });
        
        // LAForm Form Maker
        Blade::directive('la_form', function ($expression) {
            if(LAHelper::laravel_ver() >= 5.3 || LAHelper::laravel_ver() == 5.4) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LAFormMaker::form$expression; ?>";
        });
        
        // LAForm Maker - Display Values
        Blade::directive('la_display', function ($expression) {
            if(LAHelper::laravel_ver() >= 5.3 || LAHelper::laravel_ver() == 5.4) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LAFormMaker::display$expression; ?>";
        });
        
        // LAForm Maker - Check Whether User has Module Access
        Blade::directive('la_access', function ($expression) {
            if(LAHelper::laravel_ver() >= 5.3 || LAHelper::laravel_ver() == 5.4) {
                $expression = "(" . $expression . ")";
            }
            return "<?php if(LAFormMaker::la_access$expression) { ?>";
        });
        Blade::directive('endla_access', function ($expression) {
            return "<?php } ?>";
        });
        
        // LAForm Maker - Check Whether User has Module Field Access
        Blade::directive('la_field_access', function ($expression) {
            if(LAHelper::laravel_ver() >= 5.3 || LAHelper::laravel_ver() == 5.4) {
                $expression = "(" . $expression . ")";
            }
            return "<?php if(LAFormMaker::la_field_access$expression) { ?>";
        });
        Blade::directive('endla_field_access', function ($expression) {
            return "<?php } ?>";
        });
        
        /*
        |--------------------------------------------------------------------------
        | Register the Commands
        |--------------------------------------------------------------------------
        */
        
        $commands = [
            \Zhovtyj\Crm\Commands\Migration::class,
            \Zhovtyj\Crm\Commands\Crud::class,
            \Zhovtyj\Crm\Commands\Packaging::class,
            \Zhovtyj\Crm\Commands\LAInstall::class
        ];
        
        // For LAEditor
        if(file_exists(__DIR__ . '/../../laeditor')) {
            $commands[] = \Zhovtyj\Laeditor\Commands\LAEditor::class;
        }
        
        $this->commands($commands);
    }
}
