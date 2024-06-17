<?php

namespace App\Models\Core;

//use App\Concerns\HasHiddenRecords;
//use App\Concerns\HasKitchens;
//use App\Concerns\HasStock;
//use App\Concerns\HasStockLevels;
//use App\Concerns\HasStores;
//use App\Models\Core\User;
//use App\Models\Inventory\Article;
//use App\Models\Production\Restaurant;
use App\Concerns\HasStores;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

//use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasStores;
    use SoftDeletes;
    //    use HasHiddenRecords, HasKitchens, HasStockLevels;
    //    use HasStock;

    protected $guarded = [];

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    //    public function restaurants(): HasMany
    //    {
    //        return $this->hasMany(Restaurant::class);
    //    }
}
