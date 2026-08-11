<?php

namespace App\Models;

use Database\Factories\BlogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $blog_category_id
 * @property string $title
 * @property string $slug
 * @property string|null $author
 * @property string|null $source_link
 * @property string|null $custom_date
 * @property string|null $read_duration
 * @property string|null $mini_review
 * @property string|null $meta_description
 * @property string|null $image_path
 * @property array<int, string>|null $references
 * @property array<int, array{question: string, answer: string}>|null $faqs
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $image_url
 * @property-read BlogCategory $category
 */
#[Fillable([
    'blog_category_id',
    'title',
    'slug',
    'author',
    'source_link',
    'custom_date',
    'read_duration',
    'mini_review',
    'meta_description',
    'image_path',
    'references',
    'faqs',
])]
class Blog extends Model
{
    /** @use HasFactory<BlogFactory> */
    use HasFactory;

    protected $appends = ['image_url'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'references' => 'array',
            'faqs' => 'array',
        ];
    }

    /**
     * @return BelongsTo<BlogCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * @return HasMany<BlogBlock, $this>
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(BlogBlock::class)->orderBy('order');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->image_path ? '/storage/'.$this->image_path : null,
        );
    }
}
