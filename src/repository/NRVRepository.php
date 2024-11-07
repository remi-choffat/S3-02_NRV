<?php

namespace iutnc\nrv\repository;

use DateMalformedStringException;
use DateTime;
use InvalidArgumentException;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Spectacle;
use iutnc\nrv\festival\Soiree;
use PDO;
use PDOException;
use RuntimeException;

/**
 * Repository : accès à la base de données
 */
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
            throw new RuntimeException('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }


    /**
     * Charge la configuration de la base de données
     * @param string $file
     * @return void
     */
    public static function setConfig(string $file): void
    {
        if (!file_exists($file)) {
            self::$config = [];
            throw new InvalidArgumentException("Configuration file not found : $file");
        }

        self::$config = parse_ini_file($file);
        if (self::$config === false) {
            throw new RuntimeException("Error parsing configuration file : $file");
        }
    }


    /**
     * Récupère l'instance de NRVRepository
     * @return NRVRepository
     */
    public static function getInstance(): NRVRepository
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Récupère la liste des spectacles
     * @return array|null
     * @throws DateMalformedStringException
     */
    public function getSpectacles(): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SPECTACLE');
        $stmt->execute();
        $spectaclesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$spectaclesData) {
            return null;
        }

        return array_map(fn($spectacle) => $this->mapToSpectacle($spectacle), $spectaclesData);
    }


    /**
     * Récupère un spectacle
     * @param int $idSpectacle l'ID du spectacle
     * @return Spectacle
     * @throws DateMalformedStringException
     */
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


    /**
     * Récupère la liste des soirées
     * @return array|null
     * @throws DateMalformedStringException
     */
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
     * Récupère une soirée
     * @param int $idSoiree l'ID de la soirée
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


    /**
     * Récupère la liste des lieux
     * @param int $lieuId
     * @return Lieu
     */
    private function fetchLieu(int $lieuId): Lieu
    {
        $stmt = $this->pdo->prepare('SELECT * FROM LIEU WHERE id = :id');
        $stmt->execute(['id' => $lieuId]);
        $lieuData = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Lieu($lieuData['id'], $lieuData['nom'], $lieuData['adresse'], $lieuData['nbpldeb'], $lieuData['nbplass']);
    }


    /**
     * Récupère la liste des artistes d'un spectacle
     * @param int $spectacleId
     * @return array
     */
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


    /**
     * Transforme un tableau de données en objet Spectacle
     * @param array $spectacleData
     * @return Spectacle
     * @throws DateMalformedStringException
     */
    private function mapToSpectacle(array $spectacleData): Spectacle
    {
        $lieu = $this->fetchLieu($spectacleData['lieu']);
        $artistes = $this->fetchArtistes($spectacleData['id']);

        return new Spectacle(
            $spectacleData['id'],
            $spectacleData['nom'],
            new DateTime($spectacleData['date']),
            $spectacleData['duree'],
            $artistes,
            $lieu,
            $spectacleData['description'],
            $spectacleData['annule'] === 1,
            $spectacleData['soiree'] ?? null,
        );
    }


    /**
     * Récupère la liste des spectacles d'une soirée
     * @param int $soireeId
     * @return array
     * @throws DateMalformedStringException
     */
    private function fetchSpectaclesForSoiree(int $soireeId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SPECTACLE WHERE soiree = :soireeId');
        $stmt->execute(['soireeId' => $soireeId]);
        $spectaclesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($spectacle) => $this->mapToSpectacle($spectacle), $spectaclesData);
    }


    /**
     * Transforme un tableau de données en objet Soiree
     * @param array $soireeData
     * @return Soiree
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
            new DateTime($soireeData['date']),
            $lieu,
            $spectacles
        );
    }

}