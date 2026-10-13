<?php

namespace App\Models;

use Database\Factories\PrivateLabelInquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $full_name
 * @property string|null $company_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $intent
 * @property string|null $source_page
 * @property string|null $country
 * @property string|null $product_format
 * @property string|null $product_name
 * @property string|null $estimated_quantity
 * @property string|null $message
 * @property Carbon|null $read_at
 * @property string|null $project_access_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['full_name', 'company_name', 'email', 'phone', 'intent', 'source_page', 'country', 'product_format', 'product_name', 'estimated_quantity', 'message', 'read_at', 'project_access_token'])]
class PrivateLabelInquiry extends Model
{
    /** @use HasFactory<PrivateLabelInquiryFactory> */
    use HasFactory;

    protected $hidden = ['project_access_token'];

    /**
     * @return HasOne<PrivateLabelProject, $this>
     */
    public function project(): HasOne
    {
        return $this->hasOne(PrivateLabelProject::class);
    }

    /**
     * The purposes visitors can select before completing an inquiry.
     *
     * @var list<string>
     */
    public const INTENTS = [
        'private_label',
        'sample_request',
        'distribution_partnership',
        'general_export_enquiry',
    ];

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

        'Iran',
        'Iraq',
        'Turkey',
        'Pakistan',
        'Afghanistan',
        'Armenia',
        'Azerbaijan',
        'Georgia',
        'Kazakhstan',
        'Kyrgyzstan',
        'Tajikistan',
        'Turkmenistan',
        'Uzbekistan',
        'Jordan',
        'Lebanon',
        'Syria',
        'Yemen',
        'Egypt',
        'India',
        'China',
        'United Kingdom',
        'United States',
        'Germany',
        'France',

        'Albania',
        'Algeria',
        'Andorra',
        'Angola',
        'Antigua and Barbuda',
        'Argentina',
        'Australia',
        'Austria',
        'Bahamas',
        'Bangladesh',
        'Barbados',
        'Belarus',
        'Belgium',
        'Belize',
        'Benin',
        'Bhutan',
        'Bolivia',
        'Bosnia and Herzegovina',
        'Botswana',
        'Brazil',
        'Brunei',
        'Bulgaria',
        'Burkina Faso',
        'Burundi',
        'Cambodia',
        'Cameroon',
        'Canada',
        'Cape Verde',
        'Central African Republic',
        'Chad',
        'Chile',
        'Colombia',
        'Comoros',
        'Costa Rica',
        'Croatia',
        'Cuba',
        'Cyprus',
        'Czechia',
        'Democratic Republic of the Congo',
        'Denmark',
        'Djibouti',
        'Dominica',
        'Dominican Republic',
        'Ecuador',
        'El Salvador',
        'Equatorial Guinea',
        'Eritrea',
        'Estonia',
        'Eswatini',
        'Ethiopia',
        'Fiji',
        'Finland',
        'Gabon',
        'Gambia',
        'Ghana',
        'Greece',
        'Grenada',
        'Guatemala',
        'Guinea',
        'Guinea-Bissau',
        'Guyana',
        'Haiti',
        'Honduras',
        'Hungary',
        'Iceland',
        'Indonesia',
        'Ireland',
        'Israel',
        'Italy',
        'Ivory Coast',
        'Jamaica',
        'Japan',
        'Kenya',
        'Kiribati',
        'Kosovo',
        'Laos',
        'Latvia',
        'Liberia',
        'Libya',
        'Liechtenstein',
        'Lithuania',
        'Luxembourg',
        'Madagascar',
        'Malawi',
        'Malaysia',
        'Maldives',
        'Mali',
        'Malta',
        'Marshall Islands',
        'Mauritania',
        'Mauritius',
        'Mexico',
        'Micronesia',
        'Moldova',
        'Monaco',
        'Mongolia',
        'Montenegro',
        'Morocco',
        'Mozambique',
        'Myanmar',
        'Namibia',
        'Nauru',
        'Nepal',
        'Netherlands',
        'New Zealand',
        'Nicaragua',
        'Niger',
        'Nigeria',
        'North Korea',
        'North Macedonia',
        'Norway',
        'Palau',
        'Palestine',
        'Panama',
        'Papua New Guinea',
        'Paraguay',
        'Peru',
        'Philippines',
        'Poland',
        'Portugal',
        'Republic of the Congo',
        'Romania',
        'Russia',
        'Rwanda',
        'Saint Kitts and Nevis',
        'Saint Lucia',
        'Saint Vincent and the Grenadines',
        'Samoa',
        'San Marino',
        'Sao Tome and Principe',
        'Senegal',
        'Serbia',
        'Seychelles',
        'Sierra Leone',
        'Singapore',
        'Slovakia',
        'Slovenia',
        'Solomon Islands',
        'Somalia',
        'South Africa',
        'South Korea',
        'South Sudan',
        'Spain',
        'Sri Lanka',
        'Sudan',
        'Suriname',
        'Sweden',
        'Switzerland',
        'Taiwan',
        'Tanzania',
        'Thailand',
        'Timor-Leste',
        'Togo',
        'Tonga',
        'Trinidad and Tobago',
        'Tunisia',
        'Tuvalu',
        'Uganda',
        'Ukraine',
        'Uruguay',
        'Vanuatu',
        'Vatican City',
        'Venezuela',
        'Vietnam',
        'Zambia',
        'Zimbabwe',
        'European Union',
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
