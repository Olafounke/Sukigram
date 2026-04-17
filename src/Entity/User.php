<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Notification;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post:read'])]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'author')]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following')]
    #[ORM\JoinTable(name: 'follows')]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followers')]
    private Collection $following;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'likedBy')]
    private Collection $likedPosts;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'receptor')]
    private Collection $notificationsReceptor;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'sender')]
    private Collection $notificationSender;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author')]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'participants')]
    private Collection $conversations;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->likedPosts = new ArrayCollection();
        $this->notificationsReceptor = new ArrayCollection();
        $this->notificationSender = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(string $username): static { $this->username = $username; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getBio(): ?string { return $this->bio; }
    public function setBio(?string $bio): static { $this->bio = $bio; return $this; }

    public function getAvatarUrl(): ?string { return $this->avatarUrl; }
    public function setAvatarUrl(?string $avatarUrl): static { $this->avatarUrl = $avatarUrl; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getUserIdentifier(): string { return (string) $this->email; }
    public function getRoles(): array { return ['ROLE_USER']; }
    public function eraseCredentials(): void {}


    public function getPosts(): Collection { return $this->posts; }
    public function getFollowers(): Collection { return $this->followers; }
    public function getFollowing(): Collection { return $this->following; }
    public function getLikedPosts(): Collection { return $this->likedPosts; }
    public function getComments(): Collection { return $this->comments; }
    public function getConversations(): Collection { return $this->conversations; }
    public function getMessages(): Collection { return $this->messages; }
    public function getNotifications(): Collection { return $this->notificationsReceptor; }

  
    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
        }
        return $this;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSender($this);
        }
        return $this;
    }
    
  
    public function addNotificationsReceptor(Notification $notification): static
    {
        if (!$this->notificationsReceptor->contains($notification)) {
            $this->notificationsReceptor->add($notification);
        
            if ($notification->getReceptor() !== $this) {
            $notification->setReceptor($this);
            }
        }

        return $this;
    }


    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        
     
            if ($comment->getAuthor() !== $this) {
            $comment->setAuthor($this);
            }
        }

        return $this;
    }

    public function addFollowing(self $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
        
       
            $following->addFollower($this);
        }
        return $this;
    }

    public function addFollower(self $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }
        return $this;
    }
}

