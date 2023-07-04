<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function gstKyc(Request $request)
    {
        // validation
        $request->validate([ 
            'gstin' => 'required',
        ]);

        $user = User::find(Auth::user()->id);
    
        $user->update($request->all());
    
        return response()->json(["status" => 200, "message" => "User Gstin Registered Successfully"]);
    }

    public function pickupAddressKyc(Request $request)
    {
        // validation
        $request->validate([ 
            'warehouseAddress' => 'required',
        ]);

        $user = User::find(Auth::user()->id);
        $user->update($request->all());
    
        return response()->json(["status" => 200, "message" => "User Warehouse Address Details Registered Successfully"]);
    }

    public function bankDetailsKyc(Request $request)
    {
        // validation
        $request->validate([ 
            'accountNumber' => 'required',
            'ifscCode' => 'required'
        ]);

        $user = User::find(Auth::user()->id);
        $user->update($request->all());
    
        return response()->json(["status" => 200, "message" => "User Store Bank Details Registered Successfully"]);
    }

    public function supplierDetailsKyc(Request $request)
    {
         // validation
         $request->validate([ 
            'storeName' => 'required',
            'fullName' => 'required'
        ]);

        $user = User::find(Auth::user()->id);
        $user->update($request->all());
    
        return response()->json(["status" => 200, "message" => "User Store Supplier Details Registered Successfully"]);
    }
}
