<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\BlogPost;

/**
* @Route("/blog")
**/

class BlogController extends AbstractController {

	private const DATA = [

[
	'id' => 1,
	'slug' => 'foo',
], [

	'id' => 2,
	'slug' => 'bar'
]
	];

/**
* @Route("/", name="blog_list", methods={"GET"})
**/

public function list()
{
	$repository = $this->getDoctrine()->getRepository(BlogPost::class);
	$items = $repository->findAll();
	return new JsonResponse([
		array_map(function(BlogPost $e){
		return $this->generateUrl('blog_by_slug', ['slug' => $e->getSlug()]);
	}, $items)]);
}

/**
* @Route("/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
**/

public function post($id)
{
	$repository = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);

		return new JsonResponse([$repository]);

}

/**
* @Route("/{slug}", name="blog_by_slug", requirements={"slug"="\w+"}, methods={"GET"})
**/

public function postBySlug($slug)
{
return new JsonResponse([
			$this->getDoctrine()->getRepository(BlogPost::class)->findBy(['slug' => $slug]);
			]);
}


/**
* @Route("/add", name="add_post", methods={"POST"})
 **/
public function add(Request $request)
{
	/**
	@var Serializer $serializer
	*/
	$serializer = $this->get('serializer');
	$blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
	$em = $this->getDoctrine()->getManager();
	$em->persist($blogPost);
	$em->flush();

	return $this->json($blogPost);
}

}