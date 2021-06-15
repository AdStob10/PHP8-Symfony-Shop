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
        $category = new Category();
        $category->setName("Electronic");
        $category->setDescription("Electronical stuff: phones, laptops etc.");

        $product = new Product();
        $product->setName("Smartfon");
        $product->setDescription("Ordinary smartfon for general user. High perfomance, large battery capacity");
        $product->setImageFileName("smartfon1.jpg");
        $product->setPrice("299");
        
        $manager->persist($product);

        $category->addProduct($product);

        for($i = 1; $i < 13; $i++)
        {
            $product = new Product();
            $product->setName("TEST");
            $product->setDescription("TEST");
            $product->setPrice("1");
            $manager->persist($product);
            $category->addProduct($product);
        }

        $manager->persist($category);
        
        $manager->flush();
    }
}
