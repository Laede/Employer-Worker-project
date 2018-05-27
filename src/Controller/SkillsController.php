<?php

namespace App\Controller;

use App\Entity\Skills;
use App\Service\SerializeService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/skills")
 */
class SkillsController extends Controller
{

    /**
     * @Route("/all", name="json_skills", methods="GET|POST")
     */
    public function getAll(SerializeService $service)
    {
        return new Response($service->serialize(Skills::class));
    }
}
