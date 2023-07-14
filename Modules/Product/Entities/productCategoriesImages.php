<?php

namespace Modules\Product\Entities;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class productCategoriesImages extends BaseModel
{
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'product_categories_images';


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->table);
    }
    
}
