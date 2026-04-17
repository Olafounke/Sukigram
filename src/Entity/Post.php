<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection; 
use Doctrine\Common\Collections\Collection;      
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    normalizationContext: ['groups' => ['post:read']],
    denormalizationContext: ['groups' => ['post:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'context' => 'partial',
    'author.username' => 'exact'
])]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu ne peut pas être vide")]
    #[Assert\Length(min: 10, minMessage: "Votre post doit faire au moins {{ limit }} caractères")]
    #[Groups(['post:read', 'post:write'])]
    private ?string $context = null;

    #[ORM\Column]
    #[Groups(['post:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:read'])]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'likedPosts')]
    #[ORM\JoinTable(name: 'post_likes')]
    #[Groups(['post:read'])]
    private Collection $likedBy;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['post:read', 'post:write'])]
    private ?string $imageUrl = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    #[Groups(['post:read'])]
    private Collection $comments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->likedBy = new ArrayCollection();
        $this->comments = new ArrayCollection(); 
    }

    public function getId(): ?int { return $this->id; }

    public function getContext(): ?string { return $this->context; }
    public function setContext(string $context): static { $this->context = $context; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getAuthor(): ?User { return $this->author; }
    public function setAuthor(?User $author): static { $this->author = $author; return $this; }

    public function getLikedBy(): Collection { return $this->likedBy; }

    public function addLikedBy(User $user): static
    {
        if (!$this->likedBy->contains($user)) {
            $this->likedBy->add($user);
        }
        return $this;
    }

    public function removeLikedBy(User $user): static
    {
        $this->likedBy->removeElement($user);
        return $this;
    }

    public function getImageUrl(): ?string { return $this->imageUrl; }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getComments(): Collection { return $this->comments; }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }
        return $this;
    }

    
}

