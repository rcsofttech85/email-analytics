<?php

namespace App\Tests\Unit\Message\Handler;

use App\Application\DTO\EmailDTO;
use App\Application\Message\Command\SendEmailCommand;
use App\Application\Message\Handler\SendEmailHandler;
use App\Domain\Entity\Email;
use App\Domain\Repository\EmailRepositoryInterface;
use App\Domain\Service\TrackingService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

class SendEmailHandlerTest extends TestCase
{
    public function testEmailIsSent(): void
    {
        $dto = new EmailDTO('test@example.com', 'Subject', '<p>Body {{tracked_link}}</p>', 'cmp-001', 'https://example.com');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send')->with($this->isInstanceOf(MimeEmail::class));

        $emailsRepo = $this->createMock(EmailRepositoryInterface::class);
        $emailsRepo->expects($this->once())->method('add')->with($this->isInstanceOf(Email::class));

        $tracking = $this->createMock(TrackingService::class);
        $tracking->method('generateTrackingId')->willReturn('tid-123');
        $tracking->method('pixelUrl')->willReturn('pixel-url');
        $tracking->method('redirectUrl')->willReturn('redirect-url');
        $tracking->method('injectPixel')->willReturn('<p>Body</p><img src="pixel-url">');
        $tracking->method('injectTrackedLink')->willReturn('<p>Body</p><a href="redirect-url">Click</a>');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $handler = new SendEmailHandler($mailer, $emailsRepo, $tracking, $logger, "no-reply@domain.com");
        $handler(new SendEmailCommand($dto));
    }
}
