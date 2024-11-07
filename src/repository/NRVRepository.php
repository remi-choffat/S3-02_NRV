<?php

namespace iutnc\nrv\repository;

use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Soiree;
use iutnc\nrv\festival\Spectacle;
use PDO;
use PDOException;

class NRVRepository
{
    private static array $config;
    private static ?NRVRepository $instance = null;
    private PDO $pdo;

    private function __construct()
    {
//        $dsn = sprintf(
//            '%s:host=%s;dbname=%s;charset=utf8mb4',
//            self::$config['driver'],
//            self::$config['host'],
//            self::$config['dbname']
//        );
//
//        try {
//            $this->pdo = new PDO($dsn, self::$config['username'], self::$config['password']);
//            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        } catch (PDOException $e) {
//            throw new \RuntimeException('Database connection error : ' . $e->getMessage());
//        }
    }

    public static function setConfig(string $file): void
    {
        if (!file_exists($file)) {
            // throw new \InvalidArgumentException("Configuration file not found : $file");
            self::$config = [];
        }

//        self::$config = parse_ini_file($file);
//        if (self::$config === false) {
//            throw new \RuntimeException("Error parsing configuration file : $file");
//        }
    }

    public static function getInstance(): NRVRepository
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    // Tableau de spectacles fictifs pour tester, en attendant la base de données
    private array $spectacles = [
        ['id' => 1, 'titre' => 'Un super strip-tease', 'date' => '2024-11-07', 'horaire' => '20h00', 'duree' => 120, 'artistes' => ['Mathis', 'La mère à Mathis'], 'nb_places' => 69, 'description' => '🔞🐖'],
        ['id' => 2, 'titre' => 'Un autre spectacle', 'date' => '2024-11-08', 'horaire' => '19h00', 'duree' => 5, 'artistes' => [], 'nb_places' => 0, 'description' => '🤷‍♂️'],
    ];

    private array $soirees = [
        ['id' => 1, 'nom' => 'Un soirée interdite aux moins de 18 ans', 'theme' => '🤫', 'date' => '2024-11-07', 'lieu' => 'Un endroit secret', 'heureDebut' => '19h00'],
    ];

    public function getSoirees(): array
    {
        $listeSoirees = [];
        foreach ($this->soirees as $soiree) {

            $soireeAAjouter = new Soiree(
                $soiree['id'],
                $soiree['nom'],
                $soiree['theme'],
                $soiree['date'],
                new Lieu($soiree['lieu'], 'adresse fictive', 10, 10),
                $soiree['heureDebut']
            );

            $listeSoirees[] = $soireeAAjouter;

        }

        return $listeSoirees;
    }

    public function getSoiree(int $idSoiree): Soiree
    {
        $soiree = null;
        foreach ($this->soirees as $s) {
            if ($s['id'] === $idSoiree) {
                $soiree = $s;
                break;
            }
        }

        if ($soiree === null) {
            throw new \InvalidArgumentException("Soirée non trouvée");
        }

        $soireeAAjouter = new Soiree(
            $soiree['id'],
            $soiree['nom'],
            $soiree['theme'],
            $soiree['date'],
            new Lieu($soiree['lieu'], 'adresse fictive', 10, 10),
            $soiree['heureDebut']
        );

        $soireeAAjouter->ajouterSpectacle($this->getSpectacle(1));

        return $soireeAAjouter;
    }

    public function getSpectacles(): array
    {
        $listeSpectacles = [];
        foreach ($this->spectacles as $spectacle) {
            $listeSpectacles[] = new Spectacle(
                $spectacle['id'],
                $spectacle['titre'],
                new \DateTime($spectacle['date']),
                $spectacle['horaire'],
                $spectacle['duree'],
                $spectacle['artistes'],
                $spectacle['nb_places'],
                $spectacle['description']
            );
        }

        return $listeSpectacles;
    }

    public function getSpectacle(int $idSpectacle): Spectacle
    {
        $spectacle = null;
        foreach ($this->spectacles as $s) {
            if ($s['id'] === $idSpectacle) {
                $spectacle = $s;
                break;
            }
        }

        if ($spectacle === null) {
            throw new \InvalidArgumentException("Spectacle non trouvé");
        }

        return new Spectacle(
            $spectacle['id'],
            $spectacle['titre'],
            new \DateTime($spectacle['date']),
            $spectacle['horaire'],
            $spectacle['duree'],
            $spectacle['artistes'],
            $spectacle['nb_places'],
            $spectacle['description']
        );
    }

}