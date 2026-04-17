<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'author')]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following')]
    #[ORM\JoinTable(name: 'follows')]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followers')]
    private Collection $following;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'likedBy')]
    private Collection $likedPosts;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'receptor')]
    private Collection $notificationsReceptor;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'sender')]
    private Collection $notificationSender;



    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->likedPosts = new ArrayCollection();
        $this->notificationsReceptor = new ArrayCollection();
        $this->notificationSender = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function getPosts(): Collection
    {
        return $this->posts;
    }
    
    public function getNotifications(): Collection
{
    return $this->notificationsReceptor;
}

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {

            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }
    public function eraseCredentials(): void
    {
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotificationsReceptor(): Collection
    {
        return $this->notificationsReceptor;
    }

    public function addNotificationsReceptor(Notification $notificationsReceptor): static
    {
        if (!$this->notificationsReceptor->contains($notificationsReceptor)) {
            $this->notificationsReceptor->add($notificationsReceptor);
            $notificationsReceptor->setReceptor($this);
        }

        return $this;
    }

    public function removeNotificationsReceptor(Notification $notificationsReceptor): static
    {
        if ($this->notificationsReceptor->removeElement($notificationsReceptor)) {
            // set the owning side to null (unless already changed)
            if ($notificationsReceptor->getReceptor() === $this) {
                $notificationsReceptor->setReceptor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotificationSender(): Collection
    {
        return $this->notificationSender;
    }

    public function addNotificationSender(Notification $notificationSender): static
    {
        if (!$this->notificationSender->contains($notificationSender)) {
            $this->notificationSender->add($notificationSender);
            $notificationSender->setSender($this);
        }

        return $this;
    }

    public function removeNotificationSender(Notification $notificationSender): static
    {
        if ($this->notificationSender->removeElement($notificationSender)) {
            // set the owning side to null (unless already changed)
            if ($notificationSender->getSender() === $this) {
                $notificationSender->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }
    
    /**
     * @return Collection<int, Post>
     */
    public function getLikedPosts(): Collection
    {
        return $this->likedPosts;
    }

    public function addFollower(self $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }
        return $this;
    }

    public function removeFollower(self $follower): static
    {
        $this->followers->removeElement($follower);
        return $this;
    }

    public function addFollowing(self $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
        }
        return $this;
    }

    public function removeFollowing(self $following): static
    {
        $this->following->removeElement($following);
        return $this;
    }

    public function addLikedPost(Post $post): static
    {
        if (!$this->likedPosts->contains($post)) {
            $this->likedPosts->add($post);
        }
        return $this;
    }

    public function removeLikedPost(Post $post): static
    {
        $this->likedPosts->removeElement($post);
        return $this;
    }

}