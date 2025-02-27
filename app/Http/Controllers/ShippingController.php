<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShippingRequest;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function create()
    {
        return view('shipping.create');
    }

    public function store(StoreShippingRequest $request)
    {
        $validatedData = $request->validated();

        foreach ($validatedData['addresses'] as $address) {
            ShippingAddress::create([
                'address' => trim($address),
            ]);
        }

        return redirect()->back()->with('success', 'Shipping addresses added successfully!');
    }

    public function list()
    {
        if (request()->ajax()) {
            $shippingAddresses = ShippingAddress::all();
            return ['data' => $shippingAddresses];
        }
        return view('shipping.list');
    }
}
