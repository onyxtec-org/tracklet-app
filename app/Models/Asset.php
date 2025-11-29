<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'asset_code',
        'name',
        'category',
        'purchase_date',
        'purchase_price',
        'vendor',
        'warranty_expiry',
        'status',
        'status_changed_at',
        'status_change_reason',
        'assigned_to_user_id',
        'assigned_to_location',
        'description',
        'serial_number',
        'model_number',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'warranty_expiry' => 'date',
        'status_changed_at' => 'date',
    ];

    /**
     * Get the organization that owns the asset
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user assigned to this asset
     */
    public function assignedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get all movements for this asset
     */
    public function movements(): HasMany
    {
        return $this->hasMany(AssetMovement::class);
    }

    /**
     * Get all maintenance records for this asset
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    /**
     * Calculate asset age in days
     */
    public function getAgeInDays(): int
    {
        return $this->purchase_date->diffInDays(now());
    }

    /**
     * Calculate asset age in years
     */
    public function getAgeInYears(): float
    {
        return round($this->purchase_date->diffInDays(now()) / 365, 2);
    }

    /**
     * Check if warranty is expired
     */
    public function isWarrantyExpired(): bool
    {
        if (!$this->warranty_expiry) {
            return false;
        }
        return $this->warranty_expiry->isPast();
    }

    /**
     * Get days until warranty expires (negative if expired)
     */
    public function getWarrantyDaysRemaining(): ?int
    {
        if (!$this->warranty_expiry) {
            return null;
        }
        return now()->diffInDays($this->warranty_expiry, false);
    }

    /**
     * Generate unique asset code
     * Retries if code already exists (handles race conditions)
     */
    public static function generateAssetCode(Organization $organization, int $maxAttempts = 10): string
    {
        $prefix = strtoupper(substr($organization->slug, 0, 3));
        $year = date('Y');
        
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            // Get the last asset code for this organization and year (including soft-deleted)
            $lastAsset = self::withTrashed()
                ->where('organization_id', $organization->id)
                ->where('asset_code', 'like', "{$prefix}-{$year}-%")
                ->orderBy('asset_code', 'desc')
                ->first();

            if ($lastAsset) {
                $lastNumber = (int) substr($lastAsset->asset_code, -4);
                $newNumber = str_pad($lastNumber + 1 + $attempt, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = str_pad(1 + $attempt, 4, '0', STR_PAD_LEFT);
            }

            $assetCode = "{$prefix}-{$year}-{$newNumber}";
            
            // Check if this code already exists (including soft-deleted)
            $exists = self::withTrashed()
                ->where('organization_id', $organization->id)
                ->where('asset_code', $assetCode)
                ->exists();
            
            if (!$exists) {
                return $assetCode;
            }
        }
        
        // If we've exhausted attempts, throw an exception
        throw new \Exception("Unable to generate unique asset code after {$maxAttempts} attempts. Please try again.");
    }
}
