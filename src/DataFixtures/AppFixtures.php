<?php

namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\Message;
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
        $names = ['Suki', 'Lucky', 'Nala', 'Simba', 'Baya'];

   
        foreach ($names as $name) {
            $user = new User();
            $user->setEmail(strtolower($name) . "@test.com")
                 ->setUsername($name)
                 ->setBio("Je suis un petit animal nommé $name 🐾")
                 ->setPassword($this->hasher->hashPassword($user, 'password'));
            
            $manager->persist($user);
            $users[] = $user;
        }

     
        foreach ($users as $user) {
            for ($i = 1; $i <= 2; $i++) {
                $post = new Post();
                $post->setContext("Coucou ! C'est le post n°$i de " . $user->getUsername())
                     ->setAuthor($user);
                $manager->persist($post);
            }
        }

    
        $conv = new Conversation();
        $conv->addParticipant($users[0]);
        $conv->addParticipant($users[1]);
        $manager->persist($conv);

   
        $texts = ["Salut !", "Coucou ça va ?", "Oui et toi ?", "Super, tu as vu mon dernier post ?"];
        foreach ($texts as $index => $text) {
            $msg = new Message();
            $msg->setContent($text)
                ->setSender($index % 2 == 0 ? $users[0] : $users[1])
                ->setConversation($conv)
                ->setCreatedAt(new \DateTimeImmutable("-" . (10 - $index) . " minutes"))
                ->setIsRead(true);
            
            $manager->persist($msg);
        }

        $manager->flush();
    }
}