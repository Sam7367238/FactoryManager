<?php

namespace App\Controller;

use App\Entity\Machine;
use App\Form\MachineType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/machine', name: 'machine_')]
final class MachineController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request): Response
    {
        $machine = new Machine();

        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $machine->setStatus('ON');

            $this->entityManager->persist($machine);
            $this->entityManager->flush();

            $this->addFlash('status', 'Machine Created Successfully');

            return $this->redirectToRoute('factory_index');
        }

        return $this->render('machine/new.html.twig', compact('form'));
    }
}
