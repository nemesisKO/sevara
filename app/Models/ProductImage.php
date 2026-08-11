<?php

namespace App\Models;

use Database\Factories\ProductImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property string $image_path
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $image_url
 */
#[Fillable(['product_id', 'image_path', 'order'])]
class ProductImage extends Model
{
    /** @use HasFactory<ProductImageFactory> */
    use HasFactory;

    protected $appends = ['image_url'];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/storage/'.$this->image_path,
        );
    }
}
