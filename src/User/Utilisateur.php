<?php

namespace iutnc\nrv\User;

class Utilisateur
{
private int $rank;
private string $name;

public function __construct(int $r,string $n)
{
    $this->name = $n;
    $this->rank = $r;
}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }
}