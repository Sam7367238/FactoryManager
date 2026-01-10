<?php

namespace App\Controller;

use App\Entity\Factory;
use App\Form\FactoryType;
use App\Service\FactoryService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
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
        $factoryMachines = $this -> service -> getMachines($factory);

        return $this -> render(
            "factory/show.html.twig", 
            compact("factory", "factoryMachines")
        );
    }

    #[Route("/new", "new")]
    public function new(Request $request, FileUploader $fileUploader): Response {
        $factory = new Factory();

        $form = $this -> createForm(FactoryType::class, $factory);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $imageFile = $form -> get("image") -> getData();

            if ($imageFile) {
                $imageFileName = $fileUploader -> upload($imageFile);
                $this -> service -> setFactoryImage($factory, $imageFileName);
            }

            $this -> service -> saveFactory($factory);

            $this -> addFlash("status", "Factory Created Successfully");

            return $this -> redirectToRoute("factory_show", ["id" => $factory -> getId()]);
        }

        return $this -> render("factory/new.html.twig", compact("form"));
    }

    #[Route("/{id<\d+>}/edit", name: "edit")]
    public function edit(Request $request, FileUploader $fileUploader, Factory $factory): Response {
        $form = $this -> createForm(FactoryType::class, $factory);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $imageFile = $form -> get("image") -> getData();

            if ($imageFile) {
                $imageFileName = $fileUploader -> upload($imageFile);
                $this -> service -> setFactoryImage($factory, $imageFileName);
            }

            $this -> service -> saveFactory($factory, persist: false);

            $this -> addFlash("status", "Factory Updated Successfully");

            return $this -> redirectToRoute("factory_show", ["id" => $factory -> getId()]);
        }

        return $this -> render("factory/edit.html.twig", compact("form"));
    }

    #[Route("/{id<\d+>}/delete", name: "delete")]
    public function delete(Request $request, Factory $factory) {
        if ($request -> isMethod("POST")) {
            $this -> service -> deleteFactory($factory);

            $this -> addFlash("status", "Factory Deleted Successfully");

            return $this -> redirectToRoute("factory_index");
        }

        return $this -> render("factory/delete.html.twig", ["factoryID" => $factory -> getId()]);
    }
}
