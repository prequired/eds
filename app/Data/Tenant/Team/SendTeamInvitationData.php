<?php

declare(strict_types=1);

namespace App\Data\Tenant\Team;

use App\Enums\UserRole;
use Spatie\LaravelData\Data;

class SendTeamInvitationData extends Data
{
    public function __construct(
        public string $email,
        public UserRole $role,
        public ?array $permissions = null,
    ) {}

    public static function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'string', 'in:owner,admin,member'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ];
    }
}
