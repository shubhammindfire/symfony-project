<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    private const POSTS = [
        [
            'id' => 1,
            'slug' => 'hello-world',
            'title' => 'Hello World!',
        ],
        [
            'id' => 2,
            'slug' => 'another-post',
            'title' => 'This is another post',
        ],
        [
            'id' => 3,
            'slug' => 'last-example',
            'title' => 'This is the last example',
        ],
    ];

    /**
     * @Route("/{page}", name="blog_list", defaults={"page" : 3}, requirements={"page"="\d+"})
     */
    // the defaults value for page given above with the Route overrides
    // the default value for $page provided in the function definition below
    public function list($page = 2, Request $request)
    {
        // the Request class provides utilities to retrieve the request parameters from the HTTP requests
        // it has all sorts of methods like get() headers() and so on
        // try http://localhost:8000/blog/2?limit=22 in the browser
        $limit = $request->get('limit', 10);
        $param = $request->get('param', 20);

        // every controller in Symfony must return a new Resonse() object
        // in the this function list(), we return an instance of JsonResponse class
        // but in next method urlListById() we use $this->json() method which also returns a JsonResponse object (you can check in the definition of $this->json() method)
        return new JsonResponse(
            [
                'page' => $page,
                'limit' => $limit,
                'param' => $param,
                'data' => self::POSTS,
            ]
        );
    }

    /**
     * @Route("/url_list_by_id/{page}", name="url_list_by_id", defaults={"page" : 3}, requirements={"page"="\d+"})
     */
    // the defaults value for page given above with the Route overrides
    // the default value for $page provided in the function definition below
    public function urlListById($page = 2)
    {
        // every controller in Symfony must return a new Resonse() object
        // in the above function list(), it returns an instance of JsonResponse class
        // but here in this method we use $this->json() method which also returns a JsonResponse object (you can check in the definition of $this->json() method)
        
        // return $page; // this won't work because even if you want to return a simple value like $page you must return a Response() object (check the next line)
        // return new Response($page);

        return $this->json(
            [
                'page' => $page,
                'data' => array_map(function ($item) {
                    return $this->generateUrl('blog_by_id', ['id' => $item['id']]);
                }, self::POSTS)
            ]
        );
    }

    /**
     * @Route("/url_list_by_slug/{page}", name="url_list_by_slug", defaults={"page" : 3}, requirements={"page"="\d+"})
     */
    // the defaults value for page given above with the Route overrides
    // the default value for $page provided in the function definition below
    public function urlListBySlug($page = 2)
    {
        return new JsonResponse(
            [
                'page' => $page,
                'data' => array_map(function ($item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item['slug']]);
                }, self::POSTS)
            ]
        );
    }

    /**
     * @Route("/id/{id}", name="blog_by_id", requirements={"id"="\d+"})
     */
    public function post($id = 1)
    {
        return new JsonResponse(
            self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
        );
    }

    /**
     * @Route("/slug/{slug}", name="blog_by_slug")
     */
    public function postBySlug($slug = self::POSTS[0]['slug'])
    {
        return new JsonResponse(
            self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
        );
    }
}
