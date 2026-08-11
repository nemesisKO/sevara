<?php

namespace Database\Seeders;

use App\Enums\PrivateLabelSection;
use App\Models\PackagingFormat;
use App\Models\PrivateLabelItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrivateLabelSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedItems(PrivateLabelSection::Highlight, [
            ['Product Development', 'Custom flavors and recipes developed for your market.'],
            ['Packaging Customization', 'Tailored packaging that reflects your brand and positioning.'],
            ['Export Support', 'End-to-end logistics assistance for global delivery.'],
        ]);

        $this->seedItems(PrivateLabelSection::Step, [
            ['Brief', 'Share your concept, target market and requirements.'],
            ['Sample', 'We develop samples and finalize flavors and packaging.'],
            ['Packaging', 'Custom packaging designed to represent your brand.'],
            ['Production & Export', 'We produce, pack and export to your market with care.'],
        ]);

        $this->seedItems(PrivateLabelSection::Feature, [
            ['Selected Dates & Almonds', 'Carefully sourced premium ingredients for rich taste and natural goodness.'],
            ['Retail-Ready Packaging', 'Elegant, durable packaging built for retail shelves and brand impact.'],
            ['Export-Focused Support', 'Global logistics and documentation support for smooth and on-time delivery.'],
        ]);

        $this->seedFormats(true, [
            ['Single Flow Pack', 'Individual portion pack for sampling and on-the-go indulgence.'],
            ['250g Retail Pouch', 'Stand-up pouch for retail shelves and everyday sales.'],
            ['Premium Gift Box', 'Elegant gift box for premium gifting and special occasions.'],
            ['Metal Tin / Luxury Can', 'Durable and luxurious tin for long shelf life and premium appeal.'],
            ['Glass Jar / PET Jar', 'Transparent jar for visibility, trust and impulse purchase.'],
        ]);

        $this->seedFormats(false, [
            ['Window Box', 'Printed box with a clear window that shows the product.'],
            ['Sleeve Box', 'Slide-out sleeve box with a clean, minimal finish.'],
            ['Mini Sachet Pack', 'Small multi-pack sachets for sampling and promotions.'],
            ['Distributor Display Case', 'Counter-top display case built for point of sale.'],
            ['Corporate Gift Set', 'Presentation set assembled for corporate gifting.'],
        ]);
    }

    /**
     * @param  list<array{0: string, 1: string}>  $items
     */
    private function seedItems(PrivateLabelSection $section, array $items): void
    {
        foreach ($items as $order => [$title, $description]) {
            PrivateLabelItem::firstOrCreate(
                ['section' => $section, 'title' => $title],
                ['description' => $description, 'order' => $order],
            );
        }
    }

    /**
     * @param  list<array{0: string, 1: string}>  $formats
     */
    private function seedFormats(bool $isFeatured, array $formats): void
    {
        $offset = $isFeatured ? 0 : 100;

        foreach ($formats as $order => [$name, $description]) {
            PackagingFormat::firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'is_featured' => $isFeatured, 'order' => $offset + $order],
            );
        }
    }
}
