<?php

namespace App\DataFixtures;
use App\Entity\Table;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {   $faker = Factory::create('fr_FR');
        for ($i = 1; $i <= 20; $i++){
            // $product = new Product();
            $table= new Table();
            $nomTable = "Table " . $i;
            $capa = $faker->numberBetween(2, 8);
            $etat = $faker->randomElement(['Libre', 'Occupée', 'Réservée']);
            $table->setNom($nomTable)
                  ->setCapacite($capa)
                  ->setStatut($etat);
            // $manager->persist($product);
            $manager->persist($table);
        }
        $manager->flush();
    }
}
