<?php

namespace App\Models;

use Database\Factories\SampleRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $product_id
 * @property string $product_name
 * @property string $full_name
 * @property string $email
 * @property string|null $company_name
 * @property string|null $phone
 * @property string $sample_volume
 * @property string $shipping_term
 * @property string|null $description
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 */
#[Fillable(['product_id', 'product_name', 'full_name', 'email', 'company_name', 'phone', 'sample_volume', 'shipping_term', 'description', 'read_at'])]
class SampleRequest extends Model
{
    /** @use HasFactory<SampleRequestFactory> */
    use HasFactory;

    /** @var list<string> */
    public const VOLUMES = ['5–10 kg', '10–25 kg', '25–50 kg', '50+ kg'];

    /** @var list<string> */
    public const SHIPPING_TERMS = ['FOB', 'CIF'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }
}
