<?php

namespace Modules\Product\Entities;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Variants extends BaseModel
{
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'variants';

    protected $dates = [
        'deleted_at',
    ];
    protected $casts = [
        'attributes' => 'array',
    ]; 
    
    protected $hidden = [
        'authId',
        'categoryImageId',
        'bannerImageId',
        'inStock',
        'parentId',
        'parentName'
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
   

    
}
