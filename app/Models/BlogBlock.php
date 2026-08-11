<?php

namespace App\Models;

use Database\Factories\BlogBlockFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $blog_id
 * @property string $type
 * @property int $order
 * @property string|null $title
 * @property string|null $body
 * @property string|null $image_path
 * @property string|null $link_text
 * @property string|null $link_url
 * @property array<int, array<int, array{text: string, link_url: string}>>|null $table_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $image_url
 */
#[Fillable([
    'blog_id',
    'type',
    'order',
    'title',
    'body',
    'image_path',
    'link_text',
    'link_url',
    'table_data',
])]
class BlogBlock extends Model
{
    /** @use HasFactory<BlogBlockFactory> */
    use HasFactory;

    protected $appends = ['image_url'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'table_data' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Blog, $this>
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->image_path ? '/storage/'.$this->image_path : null,
        );
    }
}
