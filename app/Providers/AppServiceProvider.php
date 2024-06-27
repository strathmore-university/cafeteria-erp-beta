<?php

namespace App\Providers;

//use App\Models\Core\Category;
//use App\Models\Core\Team;
//use App\Models\Core\UnitMeasurement;
//use App\Models\Core\User;
//use App\Models\Inventory\Article;
//use App\Models\Inventory\Store;
use App\Models\Core\Category;
use App\Models\Core\Team;
use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

//use Modules\Inventory\Models\Article;
//use Modules\Inventory\Models\Store;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //        Model::shouldBeStrict();
        Model::unguard();

        Blueprint::macro('team', function (): void {
            $this->foreignIdFor(Team::class)->index()->constrained('teams');
        });

        Blueprint::macro('nullableTeam', function (): void {
            $this->foreignIdFor(Team::class)->nullable()->index();
        });

        Blueprint::macro('uniqueNameInTeam', function (): void {
            $this->unique(['name', 'team_id']);
        });

        Blueprint::macro('hashString', function (): void {
            $this->string('hash_string')->unique();
        });

        Blueprint::macro('unit', function (): void {
            $this->foreignIdFor(Unit::class)->index()->constrained();
        });

        Blueprint::macro('article', function (): void {
            $this->foreignIdFor(Article::class)->index()->constrained();
        });

        Blueprint::macro('store', function (): void {
            $this->foreignIdFor(Store::class)->index()->constrained();
        });

        Blueprint::macro('name', function (): void {
            $this->string('name', 255)->index();
        });

        Blueprint::macro('status', function (): void {
            $this->string('status', 50);
        });

        Blueprint::macro('creator', function (): void {
            $this->foreignIdFor(User::class, 'created_by')->index()->constrained('users');
        });

        Blueprint::macro('code', function (): void {
            $this->string('code', 20)->nullable()->index();
        });

        Blueprint::macro('description', function (): void {
            $this->string('description', 255)->nullable();
        });

        Blueprint::macro('active', function (): void {
            $this->boolean('is_active')->default(true)->index();
        });

        Blueprint::macro('hidden', function (): void {
            $this->boolean('is_hidden')->default(false)->index();
        });

        Blueprint::macro('default', function (): void {
            $this->boolean('is_default')->default(false)->index();
        });

        Blueprint::macro('common', function (): void {
            $this->name();
            $this->code();
            $this->description();
            $this->active();
        });

        Blueprint::macro('category', function (): void {
            $this->foreignIdFor(Category::class)->nullable()->index()->constrained();
        });

        Blueprint::macro('nest', function (): void {
            $this->nestedSet();
            $this->boolean('is_reference')->default(false);

            $this->index('_lft');
            $this->index('_rgt');
            $this->index('parent_id');
        });
    }
}
