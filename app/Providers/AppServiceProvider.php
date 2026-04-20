<?php

namespace App\Providers;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Encomenda;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\Review;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
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
        Livewire::component('admin.profile.update-profile-information-form', \App\Livewire\Profile\UpdateProfileInformationForm::class);

        $this->registerAuditHooks();
    }

    private function registerAuditHooks(): void
    {
        /** @var class-string<Model>[] $models */
        $models = [
            Livro::class,
            Requisicao::class,
            Review::class,
            Encomenda::class,
            Autor::class,
            Editora::class,
            User::class,
        ];

        foreach ($models as $modelClass) {
            $modelClass::created(fn (Model $model) => app(AuditLogger::class)->logModelEvent($model, 'criado'));
            $modelClass::updated(fn (Model $model) => app(AuditLogger::class)->logModelEvent($model, 'atualizado'));
            $modelClass::deleted(fn (Model $model) => app(AuditLogger::class)->logModelEvent($model, 'apagado'));
        }
    }
}
