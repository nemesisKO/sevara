<?php

namespace App\Models;

use Database\Factories\PrivateLabelInquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $full_name
 * @property string $email
 * @property string|null $phone
 * @property string $country
 * @property string $product_format
 * @property string $estimated_quantity
 * @property string|null $message
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['full_name', 'email', 'phone', 'country', 'product_format', 'estimated_quantity', 'message', 'read_at'])]
class PrivateLabelInquiry extends Model
{
    /** @use HasFactory<PrivateLabelInquiryFactory> */
    use HasFactory;

    /**
     * The markets a private label inquiry may be submitted for.
     *
     * @var list<string>
     */
    public const MARKETS = [
        'United Arab Emirates',
        'Saudi Arabia',
        'Qatar',
        'Kuwait',
        'Oman',
        'Bahrain',
        'Turkey',
        'United Kingdom',
        'European Union',
        'United States',
        'Canada',
        'China',
        'Japan',
        'South Korea',
        'Malaysia',
        'Indonesia',
        'India',
        'Australia',
        'Africa',
        'Other / Multiple markets',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }
}
