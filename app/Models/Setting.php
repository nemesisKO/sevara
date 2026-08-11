<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $logo_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $logo_url
 */
#[Fillable(['logo_path'])]
class Setting extends Model
{
    protected $appends = ['logo_url'];

    /**
     * Get the single settings row, creating it if it doesn't exist yet.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->logo_path ? '/storage/'.$this->logo_path : null,
        );
    }
}
