<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $catalogue_path
 * @property string|null $logo_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $catalogue_url
 * @property-read string|null $logo_url
 */
#[Fillable(['catalogue_path', 'logo_path'])]
class Setting extends Model
{
    protected $appends = ['catalogue_url', 'logo_url'];

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

    protected function catalogueUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->catalogue_path ? '/storage/'.$this->catalogue_path : null,
        );
    }
}
