<?php

namespace iutnc\nrv\festival;

/**
 * Représente un artiste
 */
class Artiste
{

    private ?int $id;
    private string $nomArtiste;

    public function __construct(?int $id, string $nomArtiste)
    {
        $this->id = $id ?? -1;
        $this->nomArtiste = $nomArtiste;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNomArtiste(): string
    {
        return $this->nomArtiste;
    }

    public function __toString(): string
    {
        return $this->nomArtiste;
    }

}