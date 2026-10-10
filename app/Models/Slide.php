<?php

namespace App\Models;

use Database\Factories\SlideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $image_path
 * @property string|null $mobile_image_path
 * @property string $link
 * @property string|null $title
 * @property string|null $description
 * @property array<string, mixed>|null $title_style
 * @property array<string, mixed>|null $description_style
 * @property string|null $primary_cta_text
 * @property string|null $primary_cta_link
 * @property string|null $secondary_cta_text
 * @property string|null $secondary_cta_link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $image_url
 * @property-read string $mobile_image_url
 */
#[Fillable([
    'image_path',
    'mobile_image_path',
    'link',
    'title',
    'description',
    'title_style',
    'description_style',
    'primary_cta_text',
    'primary_cta_link',
    'secondary_cta_text',
    'secondary_cta_link',
])]
class Slide extends Model
{
    /** @use HasFactory<SlideFactory> */
    use HasFactory;

    protected $appends = ['image_url', 'mobile_image_url'];

    protected function casts(): array
    {
        return [
            'title_style' => 'array',
            'description_style' => 'array',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/storage/'.$this->image_path,
        );
    }

    protected function mobileImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/storage/'.($this->mobile_image_path ?: $this->image_path),
        );
    }
}
