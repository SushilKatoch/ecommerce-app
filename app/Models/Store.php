<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Store extends Model
{
    use HasFactory;
    protected $table = "store";
    protected $fillable = [
        'storeName',
        'fullName',
        'storeAddress',
        'storeImage',
        'email',
        'mobileNumber',
        'gst',
        'otp',
        'otpExpiresIn',
        'mobileVerified',
        'country',
        'warehouseAddress',
        'storeCategoryId',
        'loginThrough',
        'roleId',
        'subscriptionId',
        'storeActiveId',
        'ifscCode',
        'account_number'
    ];
    protected $casts = [
        'warehouseAddress' => 'array',
    ];
}
