<?php

declare(strict_types=1);

namespace App\Data\Tenant\Team;

use Spatie\LaravelData\Data;

class AcceptTeamInvitationData extends Data
{
    public function __construct(
        public string $name,
        public string $password,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
