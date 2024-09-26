<?php

namespace App\Entity;

use App\Repository\PocilloRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PocilloRepository::class)]
class Pocillo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $fila = null;

    #[ORM\Column]
    private ?int $columna = null;

    #[ORM\Column]
    private ?float $intensidad = null;

    #[ORM\ManyToOne(inversedBy: 'pocillos')]
    private ?Test $test = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFila(): ?int
    {
        return $this->fila;
    }

    public function setFila(int $fila): static
    {
        $this->fila = $fila;

        return $this;
    }

    public function getColumna(): ?int
    {
        return $this->columna;
    }

    public function setColumna(int $columna): static
    {
        $this->columna = $columna;

        return $this;
    }

    public function getIntensidad(): ?float
    {
        return $this->intensidad;
    }

    public function setIntensidad(float $intensidad): static
    {
        $this->intensidad = $intensidad;

        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): static
    {
        $this->test = $test;

        return $this;
    }
}
