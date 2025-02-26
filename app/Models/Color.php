<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
    protected $fillable = ['device_id', 'device_version_id', 'color_name'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function deviceVersion()
    {
        return $this->belongsTo(DeviceVersion::class);
    }
}

