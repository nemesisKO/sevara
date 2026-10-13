<?php

namespace App\Models;

use Database\Factories\PrivateLabelProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $private_label_inquiry_id
 * @property string $full_name
 * @property string $company_name
 * @property string $email
 * @property array<string, mixed> $details
 * @property array<string, mixed>|null $files
 * @property Carbon $consented_at
 * @property Carbon|null $created_at
 * @property-read string $reference
 */
#[Fillable(['private_label_inquiry_id', 'full_name', 'company_name', 'email', 'details', 'files', 'consented_at'])]
class PrivateLabelProject extends Model
{
    /** @use HasFactory<PrivateLabelProjectFactory> */
    use HasFactory;

    protected $appends = ['reference'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'details' => 'array',
            'files' => 'array',
            'consented_at' => 'datetime',
        ];
    }

    protected function reference(): Attribute
    {
        return Attribute::make(
            get: fn (): string => sprintf('PL-%s-%05d', $this->created_at?->format('Y') ?? now()->format('Y'), $this->id),
        );
    }
}
