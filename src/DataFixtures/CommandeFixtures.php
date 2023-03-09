<?php
namespace App\DataFixtures;

use App\Entity\Commande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommandeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $commande = new Commande();
        $commande->setFirstname('John');
        $commande->setLastname('Doe');
        $commande->setEmail('johndoe@example.com');
        $commande->setTel('555-555-5555');
        $commande->setCity('New York');
        $commande->setAdresse('123 Main St');
        $manager->persist($commande);
        $manager->flush();
    }
}