<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $hora = null;

    #[ORM\Column(length: 255)]
    private ?string $pocillos_hab = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $informe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre_alt = null;

    #[ORM\Column(length: 5000, nullable: true)]
    private ?string $descripción = null;

    #[ORM\ManyToOne(inversedBy: 'tests')]
    private ?Usuario $usuario = null;

    /**
     * @var Collection<int, pocillo>
     */
    #[ORM\OneToMany(targetEntity: pocillo::class, mappedBy: 'test')]
    private Collection $pocillos;

    public function __construct()
    {
        $this->pocillos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(\DateTimeInterface $hora): static
    {
        $this->hora = $hora;

        return $this;
    }

    public function getPocillosHab(): ?string
    {
        return $this->pocillos_hab;
    }

    public function setPocillosHab(string $pocillos_hab): static
    {
        $this->pocillos_hab = $pocillos_hab;

        return $this;
    }

    public function getInforme()
    {
        return $this->informe;
    }

    public function setInforme($informe): static
    {
        $this->informe = $informe;

        return $this;
    }

    public function getNombreAlt(): ?string
    {
        return $this->nombre_alt;
    }

    public function setNombreAlt(?string $nombre_alt): static
    {
        $this->nombre_alt = $nombre_alt;

        return $this;
    }

    public function getDescripción(): ?string
    {
        return $this->descripción;
    }

    public function setDescripción(?string $descripción): static
    {
        $this->descripción = $descripción;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection<int, pocillo>
     */
    public function getPocillos(): Collection
    {
        return $this->pocillos;
    }

    public function addPocillo(pocillo $pocillo): static
    {
        if (!$this->pocillos->contains($pocillo)) {
            $this->pocillos->add($pocillo);
            $pocillo->setTest($this);
        }

        return $this;
    }

    public function removePocillo(pocillo $pocillo): static
    {
        if ($this->pocillos->removeElement($pocillo)) {
            // set the owning side to null (unless already changed)
            if ($pocillo->getTest() === $this) {
                $pocillo->setTest(null);
            }
        }

        return $this;
    }
}
