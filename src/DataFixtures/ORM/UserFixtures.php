<?php

/*
 * This file is part of episki core.
 *
 * (c) Justin Leapline <justin@episki.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Defines the sample users to load in the database before running the unit and
 * functional tests. Execute this command to load the data.
 *
 *   $ php bin/console doctrine:fixtures:load
 *
 * See https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author Justin Leapline <justin@episki.org>
 */
class UserFixtures extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $passwordEncoder = $this->container->get('security.password_encoder');

        $tomAdmin = new User();
        $tomAdmin->setFullName('Tom Admin');
        $tomAdmin->setEmail('tom@admin.com');
        $tomAdmin->setRoles(['ROLE_ADMIN']);
        $encodedPassword = $passwordEncoder->encodePassword($tomAdmin, 'password');
        $tomAdmin->setPassword($encodedPassword);
        $manager->persist($tomAdmin);
        $this->addReference('tom-admin', $tomAdmin);

        $johnUser = new User();
        $johnUser->setFullName('John User');
        $johnUser->setEmail('john@user.com');
        $encodedPassword = $passwordEncoder->encodePassword($johnUser, 'password');
        $johnUser->setPassword($encodedPassword);
        $manager->persist($johnUser);
        $this->addReference('john-user', $johnUser);

        $manager->flush();
    }
}
