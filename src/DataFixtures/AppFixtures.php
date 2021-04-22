<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // BE CAREFUL! using fixtures removes all the current data in the database

        $blogPost = new BlogPost();
        $blogPost->setTitle("fix title1");
        $blogPost->setPublished(new DateTime('2020-03-12 13:03:33'));
        $blogPost->setContent("fix content1");
        $blogPost->setAuthor("fix author1");
        $blogPost->setSlug("fix_slug_1");
        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle("fix title2");
        $blogPost->setPublished(new DateTime('2020-03-12 13:03:33'));
        $blogPost->setContent("fix content2");
        $blogPost->setAuthor("fix author2");
        $blogPost->setSlug("fix_slug_2");
        $manager->persist($blogPost);

        $manager->flush();
    }
}
