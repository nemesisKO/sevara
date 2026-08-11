<?php

namespace App\Models;

use Database\Factories\ProductSpecificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property string $icon_path
 * @property string $question
 * @property string $answer
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $icon_url
 */
#[Fillable(['product_id', 'icon_path', 'question', 'answer', 'order'])]
class ProductSpecification extends Model
{
    /** @use HasFactory<ProductSpecificationFactory> */
    use HasFactory;

    protected $appends = ['icon_url'];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function iconUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/storage/'.$this->icon_path,
        );
    }
}
