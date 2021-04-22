<?php

namespace App\Controller;

use App\Entity\BlogPost;
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
    /**
     * @Route("/{page}", name="blog_list", defaults={"page" : 3}, requirements={"page"="\d+"}, methods={"GET"})
     */
    // the defaults value for page given above with the Route overrides
    // the default value for $page provided in the function definition below
    public function list($page = 2, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();
        // the Request class provides utilities to retrieve the request parameters from the HTTP requests
        // it has all sorts of methods like get() headers() and so on
        // try http://localhost:8000/blog/2?limit=22 in the browser
        $limit = $request->get('limit', 10);
        $param = $request->get('param', 20);

        // every controller in Symfony must return a new Resonse() object
        // in the this function list(), we return an instance of JsonResponse class
        // but in next method urlListById() we use $this->json() method which also returns a JsonResponse object (you can check in the definition of $this->json() method)
        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'param' => $param,
                'data' => $items,
            ]
        );
    }

    /**
     * @Route("/url_list_by_id/{page}", name="url_list_by_id", defaults={"page" : 3}, requirements={"page"="\d+"}, methods={"GET"})
     */
    // the defaults value for page given above with the Route overrides
    // the default value for $page provided in the function definition below
    public function urlListById($page = 2)
    {
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        // every controller in Symfony must return a new Resonse() object
        // in the above function list(), it returns an instance of JsonResponse class
        // but here in this method we use $this->json() method which also returns a JsonResponse object (you can check in the definition of $this->json() method)

        // return $page; // this won't work because even if you want to return a simple value like $page you must return a Response() object (check the next line)
        // return new Response($page);

        return $this->json(
            [
                'page' => $page,
                'data' => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_by_id', ['id' => $item->getId()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/url_list_by_slug/{page}", name="url_list_by_slug", defaults={"page" : 3}, requirements={"page"="\d+"}, methods={"GET"})
     */
    // the defaults value for page given above with the Route overrides
    // the default value for $page provided in the function definition below
    public function urlListBySlug($page = 2)
    {
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        return $this->json(
            [
                'page' => $page,
                'data' => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function post($id = 1)
    {
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
        );
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     */
    public function postBySlug($slug)
    {
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug' => $slug])
        );
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     */
    public function add(Request $request)
    {
        /** @var Serializer seriallizer */
        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        // $em is the Doctrine Entity Manager
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        // json() returns a JsonResponse that uses the serializer component if enabled, or json_encode
        return $this->json($blogPost);
    }

    /**
     * @Route("/post/{id}", name="blog_delete", methods={"DELETE"})
     */
    public function delete(BlogPost $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        // return new Response(null,Response::HTTP_NO_CONTENT); // this also works fine
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
