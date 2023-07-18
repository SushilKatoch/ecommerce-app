<?php

namespace Modules\Product\Entities;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class productCategories extends BaseModel
{
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'product_categories';

    protected $dates = [
        'deleted_at',
    ];
    protected $casts = [
        'images' => 'array',
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

   

    /**
     * Set the published at
     * If no value submitted use the 'Title'.
     *
     * @param [type]
     */
    public function setPublishedAtAttribute($value)
    {
        $this->attributes['published_at'] = $value;

        if (empty($value) && $this->attributes['status'] == 1) {
            $this->attributes['published_at'] = Carbon::now();
        }
    }
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = slug_format(trim($value));

        if (empty($value)) {
            $this->attributes['slug'] = slug_format(trim($this->attributes['name']));
        }
    }
   
    /**
     * Get the list of Published Articles.
     *
     * @param [type] $query [description]
     * @return [type] [description]
     */
    public function scopePublished($query)
    {
        return $query->where('status', '=', '1')
                        ->whereDate('published_at', '<=', Carbon::today()
                        ->toDateString());
    }

    /**
     * Get the list of Recently Published Articles.
     *
     * @param [type] $query [description]
     * @return [type] [description]
     */
    public function scopeRecentlyPublished($query)
    {
        return $query->where('status', '=', '1')
                        ->whereDate('published_at', '<=', Carbon::today()->toDateString())
                        ->orderBy('published_at', 'desc');
    }
    
     public function category(): HasOne
    {
        return $this->hasOne('Modules\Product\Entities\productCategoriesImages','uuid','categoryImageId')
          ->select('uuid','name','path','imageDescription','alt');
    }
    
//   public function category()
//     {
//         return $this->hasMany('Modules\Product\Entities\productCategoriesImages','uuid','categoryImageId')
//                     ->select('uuid','name','path','imageDescription','alt');
//     }

    public function banner(): HasOne
    {
        return $this->hasOne('Modules\Product\Entities\productCategoriesImages','uuid','bannerImageId')
          ->select('uuid','name','path','imageDescription','alt');
    }

    
}
