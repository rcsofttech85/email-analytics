<?php

namespace App\Domain\Entity;

use App\Domain\Repository\EmailEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailEventRepository::class)]
class EmailEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 36)]
    private ?string $trackingId = null;

    #[ORM\Column(length: 10)]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $occuredAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackingId(): ?string
    {
        return $this->trackingId;
    }

    public function setTrackingId(string $trackingId): static
    {
        $this->trackingId = $trackingId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOccuredAt(): ?\DateTimeImmutable
    {
        return $this->occuredAt;
    }

    public function setOccuredAt(\DateTimeImmutable $occuredAt): static
    {
        $this->occuredAt = $occuredAt;

        return $this;
    }

    public function getMeta(): ?string
    {
        return $this->meta;
    }

    public function setMeta(?string $meta): static
    {
        $this->meta = $meta;

        return $this;
    }
}
