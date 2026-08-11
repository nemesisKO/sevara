<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $slug
 * @property string $title_part1
 * @property string $title_part1_color
 * @property string $title_part2
 * @property string $title_part2_color
 * @property string|null $subtitle
 * @property string $subtitle_color
 * @property string|null $description
 * @property string|null $spec_image_path
 * @property string|null $experience_title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $seo_title
 * @property-read string|null $spec_image_url
 */
#[Fillable([
    'slug',
    'title_part1',
    'title_part1_color',
    'title_part2',
    'title_part2_color',
    'subtitle',
    'subtitle_color',
    'description',
    'spec_image_path',
    'experience_title',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $appends = ['seo_title', 'spec_image_url'];

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    /**
     * @return HasMany<ProductSpecification, $this>
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('order');
    }

    /**
     * @return HasMany<ProductExperienceItem, $this>
     */
    public function experienceItems(): HasMany
    {
        return $this->hasMany(ProductExperienceItem::class)->orderBy('order');
    }

    protected function seoTitle(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim("{$this->title_part1} {$this->title_part2}"),
        );
    }

    protected function specImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->spec_image_path ? '/storage/'.$this->spec_image_path : null,
        );
    }
}
