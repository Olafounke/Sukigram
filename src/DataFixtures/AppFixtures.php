<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $users = [];

        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("user$i@test.com")
                 ->setUsername("user$i")
                 ->setBio("Ma super bio d'animal n°$i");
            

            $password = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($password);

            $manager->persist($user);
            $users[] = $user;


            $allPosts = [];
            foreach ($users as $index => $user) {
                for ($j = 1; $j <= 3; $j++) {
                    $post = new Post();
                    $post->setContext("Wouf ! Voici mon post n°$j (par " . $user->getUsername() . ")")
                        ->setAuthor($user);
                    
                    $manager->persist($post);
                    $allPosts[] = $post;
                }
            }

       
            foreach ($users as $user) {

                $nextUser = $users[($i + 1) % 5];
                if ($user !== $nextUser) {
                    $user->addFollowing($nextUser);
                }

                for ($k = 0; $k < 2; $k++) {
                    $randomPost = $allPosts[array_rand($allPosts)];
                    $randomPost->addLikedBy($user);
                }
            }

            $manager->flush();
        }
    }
}