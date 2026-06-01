<?php

namespace App\Data\Mail;

use Spatie\LaravelData\Data;

class EmailVerificationLinkMailData extends Data {
    public function __construct(
        public string $email,
        public string $url,
        public int $expiresAfter,
    ) {}
}
