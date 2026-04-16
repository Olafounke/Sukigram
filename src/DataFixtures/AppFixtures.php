<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
{
    for ($i = 1; $i <= 5; $i++) {
        $user = new User();
        $user->setEmail("user$i@test.com")
             ->setUsername("user$i")
             ->setPassword("password") // On ne gère pas le hashage encore (Jour 2)
             ->setBio("Ma super bio $i");
        $manager->persist($user);

        for ($j = 1; $j <= 3; $j++) {
            $post = new Post();
            $post->setContext("Ceci est le post $j de l'utilisateur $i")
                 ->setAuthor($user);
            $manager->persist($post);
        }
    }
    $manager->flush();
}
}