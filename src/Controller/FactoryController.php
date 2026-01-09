<?php

namespace App\Controller;

use App\Entity\Factory;
use App\Service\FactoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/factory", name: "factory_")]
final class FactoryController extends AbstractController
{

    public function __construct(private FactoryService $service) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $factories = $this -> service -> findAll();

        return $this->render('factory/index.html.twig', compact("factories"));
    }

    #[Route("/{id<\d+>}", name: "show")]
    public function show(Factory $factory): Response {
        return new Response($factory -> getName());
    }
}
