<?php

namespace App\Application\Message\Command;

use App\Application\DTO\EmailDTO;

class SendEmailCommand
{
    public function __construct(public EmailDTO $dto)
    {
    }
}
