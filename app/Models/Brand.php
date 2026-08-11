<?php

namespace App\Models;

use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $image_path
 * @property string $link
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $image_url
 */
#[Fillable(['name', 'image_path', 'link', 'order'])]
class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use HasFactory;

    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/storage/'.$this->image_path,
        );
    }
}
