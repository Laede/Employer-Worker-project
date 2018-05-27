<?php

namespace App\Controller;


use App\Entity\Skills;
use App\Service\SerializeService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends Controller
{
    private $serializeService;

    public function __construct(SerializeService $serializeService)
    {

        $this->serializeService = $serializeService;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $data = $this->serializeService->serialize(Skills::class);
        return new Response($data);
    }
}