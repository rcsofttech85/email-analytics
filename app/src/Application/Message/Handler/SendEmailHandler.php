<?php

namespace App\Application\Message\Handler;

use App\Application\Message\Command\SendEmailCommand;
use App\Domain\Entity\Email;
use App\Domain\Repository\EmailRepositoryInterface;
use App\Domain\Service\TrackingService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email as MimeEmail;

#[AsMessageHandler]
class SendEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private EmailRepositoryInterface $emails,
        private TrackingService $tracking,
        private LoggerInterface $logger,
        private String $defaultFrom
    ) {
    }

    public function __invoke(SendEmailCommand $cmd): void
    {
        $dto = $cmd->dto;
        $tid = $this->tracking->generateTrackingId();
        $pixel = $this->tracking->pixelUrl($tid, $dto->campaignId);
        $body = $this->tracking->injectPixel($dto->htmlBody, $pixel);
        if ($dto->redirectUrl) {
            $tracked = $this->tracking->redirectUrl($tid, $dto->redirectUrl, $dto->campaignId);
            $body = $this->tracking->injectTrackedLink($body, $tracked);
        }

        $mime = (new MimeEmail())->from($this->defaultFrom)->to($dto->to)->subject($dto->subject)->html($body);
        $this->mailer->send($mime);

        $email = new Email();
        $email->setRecipient($dto->to);
        $email->setSubject($dto->subject);
        $email->setBody($body);
        $email->setTrackingId($tid);
        $email->setStatus('sent');
        $email->setCampaignId($dto->campaignId);
        $email->setCreatedAt(new \DateTimeImmutable());
        $email->setSentAt(new \DateTimeImmutable());

        $this->emails->add($email);
        $this->logger->info('Email sent', ['to' => $dto->to,'trackingId' => $tid]);
    }
}
