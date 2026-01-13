<?php


namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/reservations', name: 'app_reservation_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $reservations = $reservationRepository->findBy([], ['reservation_date' => 'DESC']);
            return $this->render('reservation/admin_index.html.twig', [
                'reservations' => $reservations,
            ]);
        }

        if ($this->isGranted('ROLE_SERVER')) {
            $reservations = $reservationRepository->findBy([], ['reservation_date' => 'DESC']);
            return $this->render('reservation/server_index.html.twig', [
                'reservations' => $reservations,
            ]);
        }
        
      
        $user = $this->getUser();
        $reservations = $reservationRepository->findBy(['customer' => $user], ['reservation_date' => 'DESC']);
        return $this->render('reservation/customer_index.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    
    #[Route('/reservation/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager, TableRepository $tableRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
          
            $uneTable = $tableRepository->findOneBy([]);
            if (!$uneTable) {
                $this->addFlash('danger', 'Le restaurant ne contient aucune table pour le moment. Impossible de réserver.');
                return $this->redirectToRoute('app_home');
            }

            // On complète les informations
            $reservation->setCustomer($this->getUser());
            $reservation->setStatus('Confirmée');
            $reservation->setTableRes($uneTable);
            
            $entityManager->persist($reservation);
            $entityManager->flush();
            
           
            return $this->redirectToRoute('app_reservation_index', [
                'id' => $reservation->getId(),
            ]);
        }

        return $this->render('reservation/new.html.twig', [
            'reservationForm' => $form->createView(),
        ]);
    }
 

    #[Route('/reservation/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
       
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $reservation->getCustomer()) {
            throw $this->createAccessDeniedException("Action non autorisée.");
        }

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



#[Route('/reservation/{id}', name: 'app_reservation_delete', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
{
    
    if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $reservation->getCustomer()) {
        throw $this->createAccessDeniedException("Action non autorisée.");
    }

   
    if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
        $entityManager->remove($reservation);
        $entityManager->flush();
        
        $this->addFlash('success', 'La réservation a été annulée avec succès.');
    }

    return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
}

}
