<?php

namespace App\Data\Tenant\Clients;

use App\Enums\ClientStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\In;

class UpdateClientData extends Data
{
    public function __construct(
        #[Required, Min(2), Max(255)]
        public string $name,

        #[Max(255)]
        public ?string $company,

        #[Email, Max(255)]
        public ?string $email,

        #[Max(20)]
        public ?string $phone,

        #[Max(255)]
        public ?string $website,

        // Address
        #[Max(255)]
        public ?string $addressLine1,

        #[Max(255)]
        public ?string $addressLine2,

        #[Max(100)]
        public ?string $city,

        #[Max(100)]
        public ?string $state,

        #[Max(20)]
        public ?string $postalCode,

        #[Min(2), Max(2)]
        public string $country,

        // Billing
        #[Email, Max(255)]
        public ?string $billingEmail,

        public int $monthlyRetainerCents,

        #[In(['usd', 'eur', 'gbp'])]
        public string $currency,

        public int $paymentTerms,

        public ClientStatus $status,

        public ?string $notes,

        public array $tags,
    ) {}
}
