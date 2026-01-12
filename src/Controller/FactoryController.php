<?php

namespace App\Controller;

use App\Entity\Factory;
use App\Entity\Machine;
use App\Form\FactoryType;
use App\Service\FactoryService;
use App\Service\FileUploader;
use App\Service\MachineService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/factory", name: "factory_")]
final class FactoryController extends AbstractController
{
    public function __construct(private FactoryService $factoryService, private MachineService $machineService) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $factories = $this -> factoryService -> findAll();

        return $this->render('factory/index.html.twig', compact("factories"));
    }

    #[Route("/{id<\d+>}", name: "show")]
    public function show(Factory $factory): Response {
        $factoryMachines = $this -> factoryService -> getMachines($factory);

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
                $this -> factoryService -> setFactoryImage($factory, $imageFileName);
            }

            $this -> factoryService -> saveFactory($factory);

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
                $this -> factoryService -> setFactoryImage($factory, $imageFileName);
            }

            $this -> factoryService -> saveFactory($factory, persist: false);

            $this -> addFlash("status", "Factory Updated Successfully");

            return $this -> redirectToRoute("factory_show", ["id" => $factory -> getId()]);
        }

        return $this -> render("factory/edit.html.twig", compact("form"));
    }

    #[Route("/{id<\d+>}/delete", name: "delete")]
    public function delete(Request $request, Factory $factory): Response {
        if ($request -> isMethod("POST")) {
            $this -> factoryService -> deleteFactory($factory);

            $this -> addFlash("status", "Factory Deleted Successfully");

            return $this -> redirectToRoute("factory_index");
        }

        return $this -> render("factory/delete.html.twig", ["factoryID" => $factory -> getId()]);
    }

    #[Route("/{factoryId<\d+>}/machine/{machineId<\d+>}/delete", name: "machine_delete", methods: ["POST"])]
    public function machineDelete(
        #[MapEntity(id: "factoryId")] Factory $factory, 
        #[MapEntity(id:"machineId")] Machine $machine
    ): Response {
        $this -> factoryService -> deleteFactoryMachine($factory, $machine);

        $this -> addFlash("status", "Machine Has Been Removed From Factory Successfully");

        return $this -> redirectToRoute("factory_show", ["id" => $factory -> getId()]);
    }

    #[Route("/{id<\d+>}/machine/new", name: "machine_new")]
    public function machineNew(Request $request, Factory $factory): Response
    {
        $machines = $this -> machineService -> getOrphanedMachines($factory);

        if ($request -> isMethod("POST")) {
            $machineId = $request -> request -> get("machineId");
            $machine = $this -> machineService -> findOneBy(["id" => $machineId]);
            
            $this -> factoryService -> addMachine($machine, $factory);

            $this -> addFlash("status", "Machine Added To Factory Successfully");

            return $this -> redirectToRoute("factory_machine_new", ["id" => $factory -> getId()]);
        }

        return $this -> render("factory/add_machine.html.twig", compact("machines"));
    }
}
