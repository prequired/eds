<?php

declare(strict_types=1);

namespace App\Enums;

enum ExpenseCategory: string
{
    case TRAVEL = 'travel';
    case ACCOMMODATION = 'accommodation';
    case MEALS = 'meals';
    case SOFTWARE = 'software';
    case HARDWARE = 'hardware';
    case OFFICE_SUPPLIES = 'office_supplies';
    case MARKETING = 'marketing';
    case PROFESSIONAL_SERVICES = 'professional_services';
    case UTILITIES = 'utilities';
    case SUBSCRIPTIONS = 'subscriptions';
    case TRAINING = 'training';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TRAVEL => 'Travel',
            self::ACCOMMODATION => 'Accommodation',
            self::MEALS => 'Meals & Entertainment',
            self::SOFTWARE => 'Software',
            self::HARDWARE => 'Hardware',
            self::OFFICE_SUPPLIES => 'Office Supplies',
            self::MARKETING => 'Marketing',
            self::PROFESSIONAL_SERVICES => 'Professional Services',
            self::UTILITIES => 'Utilities',
            self::SUBSCRIPTIONS => 'Subscriptions',
            self::TRAINING => 'Training & Development',
            self::OTHER => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TRAVEL => '✈️',
            self::ACCOMMODATION => '🏨',
            self::MEALS => '🍽️',
            self::SOFTWARE => '💻',
            self::HARDWARE => '🖥️',
            self::OFFICE_SUPPLIES => '📎',
            self::MARKETING => '📢',
            self::PROFESSIONAL_SERVICES => '👔',
            self::UTILITIES => '⚡',
            self::SUBSCRIPTIONS => '📅',
            self::TRAINING => '📚',
            self::OTHER => '📝',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TRAVEL => 'blue',
            self::ACCOMMODATION => 'purple',
            self::MEALS => 'orange',
            self::SOFTWARE => 'indigo',
            self::HARDWARE => 'gray',
            self::OFFICE_SUPPLIES => 'yellow',
            self::MARKETING => 'pink',
            self::PROFESSIONAL_SERVICES => 'teal',
            self::UTILITIES => 'green',
            self::SUBSCRIPTIONS => 'cyan',
            self::TRAINING => 'violet',
            self::OTHER => 'slate',
        };
    }
}
