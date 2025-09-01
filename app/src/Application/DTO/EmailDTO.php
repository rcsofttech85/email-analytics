<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmailDTO
{
    public function __construct(
        #[Assert\NotBlank, Assert\Email] public string $to,
        #[Assert\NotBlank] public string $subject,
        #[Assert\NotBlank] public string $htmlBody,
        public ?string $campaignId = null,
        public ?string $redirectUrl = null,
    ) {
    }
}
