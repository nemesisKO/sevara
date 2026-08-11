<?php

namespace App\Models;

use App\Enums\PrivateLabelSection;
use Database\Factories\PrivateLabelItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property PrivateLabelSection $section
 * @property string|null $icon_path
 * @property string $title
 * @property string $description
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $icon_url
 */
#[Fillable(['section', 'icon_path', 'title', 'description', 'order'])]
class PrivateLabelItem extends Model
{
    /** @use HasFactory<PrivateLabelItemFactory> */
    use HasFactory;

    protected $appends = ['icon_url'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'section' => PrivateLabelSection::class,
        ];
    }

    protected function iconUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->icon_path ? '/storage/'.$this->icon_path : null,
        );
    }
}
