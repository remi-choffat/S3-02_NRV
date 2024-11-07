<?php

namespace iutnc\nrv\repository;

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

        self::$config = parse_ini_file($file);
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


    // Méthodes d'accès à la base de données...

    private array $spectacles = [
        ['id' => 1, 'titre' => 'Spectacle 1', 'date' => '2021-10-01', 'horaire' => '20h00', 'lieu' => 'Chez la mère à Mathis', 'artiste' => 'Artiste 1', 'nb_places' => 100, 'description' => 'Description du spectacle 1'],
        ['id' => 2, 'titre' => 'Spectacle 2', 'date' => '2021-10-02', 'horaire' => '20h00', 'lieu' => 'Chez la mère à Mathis', 'artiste' => 'Artiste 2', 'nb_places' => 100, 'description' => 'Description du spectacle 2'],
        ['id' => 3, 'titre' => 'Spectacle 3', 'date' => '2021-10-03', 'horaire' => '20h00', 'lieu' => 'Chez la mère à Mathis', 'artiste' => 'Artiste 3', 'nb_places' => 100, 'description' => 'Description du spectacle 3'],
    ];

    public function getSpectacles(): array
    {
        return $this->spectacles;
    }

    public function getSpectacle(int $idSpectacle): array
    {
        return $this->spectacles[$idSpectacle - 1];
    }

}