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
 * @property string $link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $image_url
 */
#[Fillable(['image_path', 'link'])]
class Slide extends Model
{
    /** @use HasFactory<SlideFactory> */
    use HasFactory;

    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/storage/'.$this->image_path,
        );
    }
}
