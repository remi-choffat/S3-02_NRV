<?php

namespace iutnc\nrv\festival;

class Artiste
{

    private int $id;
    private string $nomArtiste;

    public function __construct(int $id, string $nomArtiste)
    {
        $this->id = $id;
        $this->nomArtiste = $nomArtiste;
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