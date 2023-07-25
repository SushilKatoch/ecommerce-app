<?php

namespace Modules\Warehouse\Entities;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Inventory extends BaseModel
{
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'inventory';

    protected $dates = [
        'deleted_at',
    ];
    protected $casts = [
        'regionDelivery' => 'array',
        'isActive' => 'boolean',
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


    /**
     * Set the published at
     * If no value submitted use the 'Title'.
     *
     * @param [type]
     */
   
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = slug_format(trim($value));

        if (empty($value)) {
            $this->attributes['slug'] = slug_format(trim($this->attributes['name']));
        }
    }
   

    
}
