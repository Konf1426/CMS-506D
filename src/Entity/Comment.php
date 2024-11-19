<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Entity\Traits\UuidIdentifiable;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'security' => "is_granted('ROLE_USER')"
        ]
    ],
    itemOperations: [
        'get',
        'put' => [
            'security' => "is_granted('ROLE_ADMIN') or object.getAuthor() == user"
        ],
        'delete' => [
            'security' => "is_granted('ROLE_ADMIN') or object.getAuthor() == user"
        ]
    ]
)]
class Comment
{
    use UuidIdentifiable; // UUID pour l'identifiant unique
    use Timestampable;    // Timestamps pour les dates de création et modification

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Content::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Content $contentRelated = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getContentRelated(): ?Content
    {
        return $this->contentRelated;
    }

    public function setContentRelated(?Content $contentRelated): static
    {
        $this->contentRelated = $contentRelated;

        return $this;
    }
}
