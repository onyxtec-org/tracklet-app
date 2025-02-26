<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'device_id',
        'device_version_id',
        'primary_color_id',
        'secondary_color_id',
        'shipping_address_id',
        'shipping_attention',
        'caller_id_requested'
    ];

    /**
     * Relationship with Device
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Relationship with Device Version
     */
    public function deviceVersion()
    {
        return $this->belongsTo(DeviceVersion::class);
    }

    /**
     * Relationship with Primary Color
     */
    public function primaryColor()
    {
        return $this->belongsTo(Color::class, 'primary_color_id');
    }

    /**
     * Relationship with Secondary Color
     */
    public function secondaryColor()
    {
        return $this->belongsTo(Color::class, 'secondary_color_id');
    }

    /**
     * Relationship with Shipping Address
     */
    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }
}
