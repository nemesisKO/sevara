<?php

namespace App\Enums;

enum PrivateLabelSection: string
{
    case Highlight = 'highlight';
    case Step = 'step';
    case Feature = 'feature';

    public function label(): string
    {
        return match ($this) {
            self::Highlight => 'What we offer',
            self::Step => 'From idea to market',
            self::Feature => 'Built for reliable B2B supply',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Highlight => 'The three cards shown directly under the hero.',
            self::Step => 'The numbered timeline explaining how a project runs.',
            self::Feature => 'The supply promises shown near the bottom of the page.',
        };
    }
}
