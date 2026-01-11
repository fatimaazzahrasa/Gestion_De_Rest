<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $reservation_date = null;

    #[ORM\Column]
    private ?float $nbr_personne = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Table $table_res = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservationDate(): ?\DateTime
    {
        return $this->reservation_date;
    }

    public function setReservationDate(\DateTime $reservation_date): static
    {
        $this->reservation_date = $reservation_date;

        return $this;
    }

    public function getNbrPersonne(): ?float
    {
        return $this->nbr_personne;
    }

    public function setNbrPersonne(float $nbr_personne): static
    {
        $this->nbr_personne = $nbr_personne;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTableRes(): ?Table
    {
        return $this->table_res;
    }

    public function setTableRes(?Table $table_res): static
    {
        $this->table_res = $table_res;

        return $this;
    }
}
