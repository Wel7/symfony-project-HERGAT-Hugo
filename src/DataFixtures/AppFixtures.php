<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Adresse;
use App\Entity\Categorie;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\OrderItem;
use App\Entity\Order;
use App\Entity\User;

use App\Enum\OrderStatus;
use App\Enum\ProductStatus;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /*
        * Fixtures généré par IA (pour le moement, y'aura un truc propre plus tard :D)
        */
        $adresse1 = new Adresse();
        $adresse1->setStreet('123 Main St');
        $adresse1->setPostalCode('12345');
        $adresse1->setCity('Metz');
        $adresse1->setCountry('France');
        $manager->persist($adresse1);

        // Création de la deuxième adresse
        $adresse2 = new Adresse();
        $adresse2->setStreet('456 Elm St');
        $adresse2->setPostalCode('67890');
        $adresse2->setCity('Paris');
        $adresse2->setCountry('France');
        $manager->persist($adresse2);

        // Création de la troisième adresse
        $adresse3 = new Adresse();
        $adresse3->setStreet('789 Oak St');
        $adresse3->setPostalCode('11223');
        $adresse3->setCity('Lyon');
        $adresse3->setCountry('France');
        $manager->persist($adresse3);

        // Création de la catégorie "Prints"
        $categoriePrints = new Categorie();
        $categoriePrints->setNom('Prints');
        $manager->persist($categoriePrints);
        
        // Création de la catégorie "Dakimakura"
        $categorieDakimakura = new Categorie();
        $categorieDakimakura->setNom('Dakimakura');
        $manager->persist($categorieDakimakura);

        // Création de la première image
        $image1 = new Image();
        $image1->setUrl('https://example.com/image1.jpg');
        $manager->persist($image1);

        // Création de la deuxième image
        $image2 = new Image();
        $image2->setUrl('https://example.com/image2.jpg');
        $manager->persist($image2);

        // Création de la troisième image
        $image3 = new Image();
        $image3->setUrl('https://example.com/image3.jpg');
        $manager->persist($image3);
        
        // Création des produits
        $product1 = new Product();
        $product1->setName('Product 1');
        $product1->setDescription('Description for product 1');
        $product1->setPrice(19.99);
        $product1->setCategorie($categoriePrints);
        $product1->addImage($image1);
        $product1->setStock(12);
        $product1->setStatus(ProductStatus::DISPONIBLE);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Product 2');
        $product2->setDescription('Description for product 2');
        $product2->setPrice(29.99);
        $product2->setCategorie($categorieDakimakura);
        $product2->addImage($image2);
        $product2->setStock(12);
        $product2->setStatus(ProductStatus::DISPONIBLE);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Product 3');
        $product3->setDescription('Description for product 3');
        $product3->setPrice(39.99);
        $product3->setCategorie($categoriePrints);
        $product3->addImage($image3);
        $product3->setStock(12);
        $product3->setStatus(ProductStatus::DISPONIBLE);
        $manager->persist($product3);

        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setFirstName('John');
        $user1->setLastName('Doe');
        $user1->setPassword(password_hash('password1', PASSWORD_BCRYPT));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setFirstName('Chat');
        $user2->setLastName('Gpt');
        $user2->setRoles(['ROLE_ADMIN']);
        $user2->setPassword(password_hash('password2', PASSWORD_BCRYPT));
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('user3@example.com');
        $user3->setFirstName('Alice');
        $user3->setLastName('Johnson');
        $user3->setPassword(password_hash('password3', PASSWORD_BCRYPT));
        $manager->persist($user3);

        $orderItem1 = new OrderItem();
        $orderItem1->setQuantity(2);
        $orderItem1->setProductPrice(10.0);
        $orderItem1->setProduct($product1);
        $manager->persist($orderItem1);

        $orderItem2 = new OrderItem();
        $orderItem2->setQuantity(1);
        $orderItem2->setProductPrice(20.0);
        $orderItem2->setProduct($product2);
        $manager->persist($orderItem2);

        // Create Orders with fixed data and associate OrderItems
        $order1 = new Order();
        $order1->setStatus(OrderStatus::PREPARATION);
        $order1->setUser($user1);
        $order1->setReference("a");
        $order1->setCreatedAt(new DateTimeImmutable());
        $manager->persist($order1);

        $order2 = new Order();
        $order2->setStatus(OrderStatus::LIVRE);
        $order2->setUser($user2);
        $order2->setReference("2");
        $order2->setCreatedAt(new DateTimeImmutable());
        $manager->persist($order2);

        $manager->flush();
    }
}
