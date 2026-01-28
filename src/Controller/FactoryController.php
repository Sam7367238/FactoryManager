<?php

namespace App\Controller;

use App\Entity\Factory;
use App\Entity\Machine;
use App\Form\FactoryType;
use App\Repository\FactoryRepository;
use App\Repository\MachineRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/factory', name: 'factory_')]
final class FactoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FactoryRepository $factoryRepository,
        private MachineRepository $machineRepository,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $factories = $this->factoryRepository->findAll();

        return $this->render('factory/index.html.twig', compact('factories'));
    }

    #[Route("/{id<\d+>}", name: 'show')]
    public function show(Factory $factory): Response
    {
        $factoryMachines = $factory->getMachines();

        return $this->render(
            'factory/show.html.twig',
            compact('factory', 'factoryMachines')
        );
    }

    #[Route('/new', 'new')]
    public function new(Request $request, FileUploaderService $fileUploader): Response
    {
        $factory = new Factory();

        $form = $this->createForm(FactoryType::class, $factory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $factory->setImage($imageFileName);
            }

            $this->entityManager->persist($factory);
            $this->entityManager->flush();

            $this->addFlash('status', 'Factory Created Successfully');

            return $this->redirectToRoute('factory_show', ['id' => $factory->getId()]);
        }

        return $this->render('factory/new.html.twig', compact('form'));
    }

    #[Route("/{id<\d+>}/edit", name: 'edit')]
    public function edit(Request $request, FileUploaderService $fileUploader, Factory $factory): Response
    {
        $form = $this->createForm(FactoryType::class, $factory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $factory->setImage($imageFileName);
            }

            $this->entityManager->flush();

            $this->addFlash('status', 'Factory Updated Successfully');

            return $this->redirectToRoute('factory_show', ['id' => $factory->getId()]);
        }

        return $this->render('factory/edit.html.twig', compact('form'));
    }

    #[Route("/{id<\d+>}/delete", name: 'delete')]
    public function delete(Request $request, Factory $factory): Response
    {
        if ($request->isMethod('POST')) {
            $this->entityManager->remove($factory);

            $this->addFlash('status', 'Factory Deleted Successfully');

            return $this->redirectToRoute('factory_index');
        }

        return $this->render('factory/delete.html.twig', ['factoryID' => $factory->getId()]);
    }

    #[Route("/{factoryId<\d+>}/machine/{machineId<\d+>}/delete", name: 'machine_delete', methods: ['POST'])]
    public function machineDelete(
        #[MapEntity(id: 'factoryId')] Factory $factory,
        #[MapEntity(id: 'machineId')] Machine $machine,
    ): Response {
        $factory->removeMachine($machine);
        $this->entityManager->flush();

        $this->addFlash('status', 'Machine Has Been Removed From Factory Successfully');

        return $this->redirectToRoute('factory_show', ['id' => $factory->getId()]);
    }

    #[Route("/{id<\d+>}/machine/add", name: 'machine_add', methods: 'GET')]
    public function machineAdd(Request $request, Factory $factory): Response
    {
        $machines = $this->machineRepository->findOrphanedMachinesForFactory($factory);

        return $this->render('factory/add_machine.html.twig', compact('machines'));
    }

    #[Route("/{id<\d+>}/machine/add", name: 'machine_new', methods: 'POST')]
    public function machineNew(Request $request, Factory $factory): Response
    {
        $machineId = $request->request->get('machineId');

        if (!$this->machineRepository->find($machineId)) {
            return throw $this->createNotFoundException();
        }

        $machine = $this->machineRepository->find($machineId);

        $machines = $factory->getMachines();

        $contains = $machines->contains($machine);

        if ($contains) {
            $this->addFlash('status', 'Machine Is Already Part Of The Factory');

            return $this->redirectToRoute('factory_machine_add', ['id' => $factory->getId()]);
        }

        $machine = $this->machineRepository->find($machineId);

        $factory->addMachine($machine);

        $this->entityManager->flush();

        $this->addFlash('status', 'Machine Added To Factory Successfully');

        return $this->redirectToRoute('factory_machine_add', ['id' => $factory->getId()]);
    }
}
