<?php

namespace App\Models;

use Database\Factories\PackagingFormatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image_path
 * @property bool $is_featured
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $image_url
 */
#[Fillable(['name', 'description', 'image_path', 'is_featured', 'order'])]
class PackagingFormat extends Model
{
    /** @use HasFactory<PackagingFormatFactory> */
    use HasFactory;

    protected $appends = ['image_url'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->image_path ? '/storage/'.$this->image_path : null,
        );
    }
}
