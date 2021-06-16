<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixture extends Fixture
{
    private $encoder;
     
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    // Create users for app
    public function load(ObjectManager $manager)
    {

        $clientRoles = array("ROLE_CLIENT");
        $workerRoles = array("ROLE_EMPLOYEE");

        $user = new User();
        $user->setEmail("manager@shop.com");
        $user->setPassword($this->encoder->encodePassword($user, "1234"));
        $user->setRoles($workerRoles);
        $manager->persist($user);

        $user = new User();
        $user->setEmail("client@shop.com");
        $user->setPassword($this->encoder->encodePassword($user, "1234"));
        $user->setRoles($clientRoles);
        $manager->persist($user);

        
        $user = new User();
        $user->setEmail("client2@shop.com");
        $user->setPassword($this->encoder->encodePassword($user, "1234"));
        $user->setRoles($clientRoles);
        $manager->persist($user);

        $manager->flush();
    }
}
