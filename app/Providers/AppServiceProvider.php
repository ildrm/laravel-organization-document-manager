<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\Form;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Policies\DocumentPolicy;
use App\Policies\FormPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Organization::class => OrganizationPolicy::class,
        Document::class => DocumentPolicy::class,
        Form::class => FormPolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        \App\Models\Document::observe(\App\Observers\DocumentObserver::class);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'fa']);
        });
    }
}
