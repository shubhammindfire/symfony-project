<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        // BE CAREFUL! using fixtures removes all the current data in the database

        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        $user = $this->getReference('user1');

        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText($this->faker->numberBetween(15, 30)));
            $blogPost->setPublished($this->faker->dateTimeThisYear());
            $blogPost->setContent($this->faker->realText());
            $blogPost->setAuthor($user);
            $blogPost->setSlug($this->faker->slug());
            $manager->persist($blogPost);

            $this->addReference("blog_post_$i", $blogPost);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        
        for($i=0;$i<100;$i++){
            for($j=0;$j<rand(1,10);$j++){
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear());
                $comment->setAuthor($this->getReference('user1'));
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();

        $user->setUsername('user1');
        $user->setName('User 1');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password1'));
        $user->setEmail('user1@email.com');

        $this->addReference('user1', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
