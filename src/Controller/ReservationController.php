<?php
// Fichier : src/Controller/ReservationController.php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\TableRepository; // Ajout important
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/reservations', name: 'app_reservation_index')]
    #[IsGranted('ROLE_USER')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $reservations = $reservationRepository->findAll();
            return $this->render('reservation/admin_index.html.twig', [
                'reservations' => $reservations,
            ]);
        }

        if ($this->isGranted('ROLE_SERVER')) {
            $reservations = $reservationRepository->findAll();
            return $this->render('reservation/server_index.html.twig', [
                'reservations' => $reservations,
            ]);
        }
        
        $user = $this->getUser();
        $reservations = $reservationRepository->findBy(['customer' => $user]);
        return $this->render('reservation/customer_index.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    
    #[Route('/reservation/new', name: 'app_reservation_new')]
    #[IsGranted('ROLE_USER')]
    // On demande à Symfony de nous "injecter" le TableRepository ici
    public function new(Request $request, EntityManagerInterface $entityManager, TableRepository $tableRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            // ===============================================
            // === LOGIQUE FACTICE POUR TROUVER UNE TABLE ===
            // On cherche la première table disponible dans la base de données
            $uneTable = $tableRepository->findOneBy([]);

            // Si aucune table n'existe dans le restaurant, on lance une erreur claire.
            if (!$uneTable) {
                $this->addFlash('danger', 'Le restaurant ne contient aucune table pour le moment. Impossible de réserver.');
                return $this->redirectToRoute('app_home');
            }
            // ===============================================

            // On complète les informations de la réservation
            $reservation->setCustomer($this->getUser());
            $reservation->setStatus('Confirmée');
            
            // ON ASSIGNE LA TABLE TROUVÉE À LA RÉSERVATION
            $reservation->setTableRes($uneTable);
            
            $entityManager->persist($reservation);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre réservation a bien été enregistrée !');
            return $this->redirectToRoute('app_reservation_index'); // On redirige vers la liste des résas
        }

        return $this->render('reservation/new.html.twig', [
            'reservationForm' => $form->createView(),
        ]);
    }

    #[Route('/reservation/{id}/edit', name: 'app_reservation_edit')]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // Sécurité : Seul un ADMIN ou le propriétaire peut modifier.
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $reservation->getCustomer()) {
            throw $this->createAccessDeniedException("Action non autorisée.");
        }

        // Note : Pour l'instant, l'édition ne ré-assigne pas de table.
        // Elle ne modifie que la date et le nombre de personnes.
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La réservation a été modifiée avec succès.');
            return $this->redirectToRoute('app_reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'reservationForm' => $form->createView(),
        ]);
    }
}
