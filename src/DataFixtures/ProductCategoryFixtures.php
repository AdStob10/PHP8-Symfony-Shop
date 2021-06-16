<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        // Data for testing

        // Electronics
        $electronics = new Category();
        $electronics->setName("Electronics");
        $electronics->setDescription("Electronical stuff: phones, laptops etc.");

        $product = new Product();
        $product->setName("Smartfon");
        $product->setDescription("Ordinary smartfon for general user. High perfomance, large battery capacity");
        $product->setImageFileName("smartfon1.jpg");
        $product->setPrice("299");

        $manager->persist($product);
        $electronics->addProduct($product);

        $product = new Product();
        $product->setName("Smartwatch X1");
        $product->setDescription("Basic smartwatch for daily use");
        $product->setImageFileName("testsmart.jpg");
        $product->setPrice(69.99);
        
        $manager->persist($product);
        $electronics->addProduct($product);

        $product = new Product();
        $product->setName("Smartwatch Premium Edition");
        $product->setDescription("Premium smartwatch for advanced users");
        $product->setImageFileName("testsmart2.jpg");
        $product->setPrice(109.99);
        
        $manager->persist($product);
        $electronics->addProduct($product);

        $product = new Product();
        $product->setName("Smartfon XS");
        $product->setDescription("Low price phone for everyone");
        $product->setPrice(59.99);

        $manager->persist($product);
        $electronics->addProduct($product);

        // Sporting goods
        $sporting = new Category();
        $sporting->setName("Sporting goods");
        $sporting->setDescription("Everything you need to make physical activity more efficient");

        $product = new Product();
        $product->setName("Weight plate 5KG");
        $product->setDescription("New weight plates");
        $product->setImageFileName("plate.jpg");
        $product->setPrice(20.99);

        $manager->persist($product);
        $sporting->addProduct($product);

        $product = new Product();
        $product->setName("Sport Bike XL");
        $product->setDescription("Professional bike for cyclists");
        $product->setImageFileName("sportbike.png");
        $product->setPrice(320.99);

        $manager->persist($product);
        $sporting->addProduct($product);

        $product = new Product();
        $product->setName("Tennis Ball");
        $product->setDescription("Standard tennis ball");
        $product->setImageFileName("tennisball.jpeg");
        $product->setPrice(5);

        $manager->persist($product);
        $sporting->addProduct($product);


        $manager->persist($electronics);
        $manager->persist($sporting);
        
        $manager->flush();
    }
}
