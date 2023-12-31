<?php

namespace Modules\Product\Entities;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends BaseModel
{
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'products';

    protected $dates = [
        'deleted_at',
    ];
    protected $casts = [
        'attributes' => 'array',
        'isActive' => 'boolean',
        'inStock' => 'boolean',
        'isTaxable' => 'boolean',
    ]; 
    
    protected $hidden = [
        'seoData'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->table);
    }

   

   
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = slug_format(trim($value));

        if (empty($value)) {
            $this->attributes['slug'] = slug_format(trim($this->attributes['name']));
        }
    }
   
       public function category():HasMany
    {
        return $this->hasMany('Modules\Product\Entities\Variant','uuid','productId')
                    ->select('masterAttribute','attributeName','uuid');
    }

    public function inventory():HasMany
    {
        return $this->hasMany('Modules\Product\Entities\Variant','uuid','productId')
                    ->select('masterAttribute','attributeName','uuid');
    }
    
}
