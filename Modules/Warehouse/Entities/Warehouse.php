<?php

namespace Modules\Warehouse\Entities;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Warehouse extends BaseModel
{
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'warehouse';

    protected $dates = [
        'deleted_at',
    ];
    protected $casts = [
        'regionDelivery' => 'array',
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
   
   
    
    
     public function product(): HasOne
    {
        return $this->hasOne('Modules\Product\Entities\Product','uuid','categoryImageId')
          ->select('uuid','name','path','imageDescription','alt');
    }



    
}
