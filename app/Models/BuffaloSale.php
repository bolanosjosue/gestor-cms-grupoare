<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuffaloSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'breed_id',
        'father_breed_id',
        'mother_breed_id',
        'sex',
        'age_years',
        'weight_kg',
        'reproductive_status',
        'gestation_months',
        'births_count',
        'milk_production',
        'vaccines_up_to_date',
        'feeding_type',
        'animal_condition',
        'status',
        'price_crc',
        'phone',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'breed_id' => 'integer',
        'father_breed_id' => 'integer',
        'mother_breed_id' => 'integer',
        'age_years' => 'integer',
        'weight_kg' => 'decimal:2',
        'price_crc' => 'integer',
        'gestation_months' => 'integer',
        'births_count' => 'integer',
        'milk_production' => 'decimal:2',
        'vaccines_up_to_date' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function breed(): BelongsTo
    {
        return $this->belongsTo(BuffaloBreed::class, 'breed_id');
    }

    public function fatherBreed(): BelongsTo
    {
        return $this->belongsTo(BuffaloBreed::class, 'father_breed_id');
    }

    public function motherBreed(): BelongsTo
    {
        return $this->belongsTo(BuffaloBreed::class, 'mother_breed_id');
    }

    public function getPhotoUrl(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }

        return asset('storage/' . $this->photo_path);
    }
}
