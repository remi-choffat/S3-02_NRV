<?php

namespace iutnc\nrv\repository;

use DateMalformedStringException;
use InvalidArgumentException;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Spectacle;
use iutnc\nrv\festival\Soiree;
use PDO;
use PDOException;

class NRVRepository
{
    private static array $config;
    private static ?NRVRepository $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=utf8mb4',
            self::$config['driver'],
            self::$config['host'],
            self::$config['dbname']
        );

        try {
            $this->pdo = new PDO($dsn, self::$config['username'], self::$config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    public static function setConfig(string $file): void
    {
        if (!file_exists($file)) {
            self::$config = [];
            throw new InvalidArgumentException("Configuration file not found : $file");
        }

        self::$config = parse_ini_file($file);
        if (self::$config === false) {
            throw new \RuntimeException("Error parsing configuration file : $file");
        }
    }

    public static function getInstance(): NRVRepository
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getSpectacles(): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SPECTACLE');
        $stmt->execute();
        $spectaclesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$spectaclesData) {
            return null;
        }

        $listeSpectacles = array_map(fn($spectacle) => $this->mapToSpectacle($spectacle), $spectaclesData);

        return $listeSpectacles;
    }

    public function getSpectacle(int $idSpectacle): Spectacle
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SPECTACLE WHERE id = :id');
        $stmt->execute(['id' => $idSpectacle]);
        $spectacleData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$spectacleData) {
            throw new InvalidArgumentException("Spectacle non trouvé");
        }

        return $this->mapToSpectacle($spectacleData);
    }

    public function getSoirees(): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SOIREE');
        $stmt->execute();
        $soireesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$soireesData) {
            return null;
        }

        return array_map(fn($soiree) => $this->mapToSoiree($soiree), $soireesData);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function getSoiree(int $idSoiree): Soiree
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SOIREE WHERE id = :id');
        $stmt->execute(['id' => $idSoiree]);
        $soireeData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$soireeData) {
            throw new InvalidArgumentException("Soiree non trouvée");
        }

        return $this->mapToSoiree($soireeData);
    }

    private function fetchLieu(int $lieuId): Lieu
    {
        $stmt = $this->pdo->prepare('SELECT * FROM LIEU WHERE id = :id');
        $stmt->execute(['id' => $lieuId]);
        $lieuData = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Lieu($lieuData['id'], $lieuData['nom'], $lieuData['adresse'], $lieuData['nbpldeb'], $lieuData['nbplass']);
    }

    private function fetchArtistes(int $spectacleId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT nomArtiste
            FROM ARTISTE
            INNER JOIN JOUE ON ARTISTE.id = JOUE.ida
            WHERE JOUE.idsp = :idSpectacle
        ');
        $stmt->execute(['idSpectacle' => $spectacleId]);
        $artistesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($artiste) => $artiste['nomArtiste'], $artistesData);
    }

    private function mapToSpectacle(array $spectacleData): Spectacle
    {
        $lieu = $this->fetchLieu($spectacleData['lieu']);
        $artistes = $this->fetchArtistes($spectacleData['id']);

        return new Spectacle(
            $spectacleData['id'],
            $spectacleData['nom'],
            new \DateTime($spectacleData['date']),
            $spectacleData['duree'],
            $artistes,
            $lieu,
            $spectacleData['description'],
            $spectacleData['annule'] === 1,
            $spectacleData['soiree'] ?? null,
        );
    }

    private function fetchSpectaclesForSoiree(int $soireeId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SPECTACLE WHERE soiree = :soireeId');
        $stmt->execute(['soireeId' => $soireeId]);
        $spectaclesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($spectacle) => $this->mapToSpectacle($spectacle), $spectaclesData);
    }

    /**
     * @throws DateMalformedStringException
     */
    private function mapToSoiree(array $soireeData): Soiree
    {
        $lieu = $this->fetchLieu($soireeData['lieu']);
        $spectacles = $this->fetchSpectaclesForSoiree($soireeData['id']);

        return new Soiree(
            $soireeData['id'],
            $soireeData['nom'],
            $soireeData['theme'],
            new \DateTime($soireeData['date']),
            $lieu,
            $spectacles
        );
    }
}