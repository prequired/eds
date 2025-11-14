<?php

namespace App\Enums;

enum PlanType: string
{
    case STARTER = 'starter';
    case PROFESSIONAL = 'professional';
    case AGENCY = 'agency';
    case ENTERPRISE = 'enterprise';

    public function label(): string
    {
        return match($this) {
            self::STARTER => 'Starter',
            self::PROFESSIONAL => 'Professional',
            self::AGENCY => 'Agency',
            self::ENTERPRISE => 'Enterprise',
        };
    }

    public function price(): int
    {
        return match($this) {
            self::STARTER => 99_00,
            self::PROFESSIONAL => 299_00,
            self::AGENCY => 599_00,
            self::ENTERPRISE => 0, // Custom
        };
    }

    public function clientLimit(): ?int
    {
        return match($this) {
            self::STARTER => 5,
            self::PROFESSIONAL => 20,
            self::AGENCY => null,
            self::ENTERPRISE => null,
        };
    }

    public function websiteLimit(): ?int
    {
        return match($this) {
            self::STARTER => 10,
            self::PROFESSIONAL => 50,
            self::AGENCY => null,
            self::ENTERPRISE => null,
        };
    }

    public function storageLimit(): int
    {
        return match($this) {
            self::STARTER => 10,    // GB
            self::PROFESSIONAL => 100,
            self::AGENCY => 500,
            self::ENTERPRISE => 1000,
        };
    }

    public function bandwidthLimit(): int
    {
        return match($this) {
            self::STARTER => 500,   // GB/month
            self::PROFESSIONAL => 2000,
            self::AGENCY => 10000,
            self::ENTERPRISE => 50000,
        };
    }

    public function features(): array
    {
        return match($this) {
            self::STARTER => [
                'client_portal' => true,
                'automated_reports' => false,
                'white_label_domain' => false,
                'api_access' => false,
                'team_members' => 2,
            ],
            self::PROFESSIONAL => [
                'client_portal' => true,
                'automated_reports' => true,
                'white_label_domain' => true,
                'api_access' => true,
                'team_members' => 5,
            ],
            self::AGENCY => [
                'client_portal' => true,
                'automated_reports' => true,
                'white_label_domain' => true,
                'api_access' => true,
                'team_members' => null, // Unlimited
                'white_label_reselling' => true,
            ],
            self::ENTERPRISE => [
                'client_portal' => true,
                'automated_reports' => true,
                'white_label_domain' => true,
                'api_access' => true,
                'team_members' => null,
                'white_label_reselling' => true,
                'custom_integrations' => true,
                'sla_guarantee' => true,
            ],
        };
    }
}
