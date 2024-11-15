<?php

namespace iutnc\nrv\repository;

use DateMalformedStringException;
use DateTime;
use InvalidArgumentException;
use iutnc\nrv\exception\ArtisteDuplicationException;
use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\exception\DateIncompatibleException;
use iutnc\nrv\exception\InscriptionException;
use iutnc\nrv\exception\LieuDuplicationException;
use iutnc\nrv\exception\SoireeAssignationException;
use iutnc\nrv\exception\SpectacleAssignationException;
use iutnc\nrv\festival\Artiste;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Soiree;
use iutnc\nrv\festival\Spectacle;
use iutnc\nrv\User\Utilisateur;
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
     * Récupère la liste de tous les styles existants
     * @return array
     */
    public function getStyles(): array
    {
        $stmt = $this->pdo->query('SELECT DISTINCT style FROM SPECTACLE');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * Récupère la liste de tous les lieux existants
     * @return array
     */
    public function getLieux(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM LIEU');
        $lieuxData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($lieu) => new Lieu($lieu['id'], $lieu['nom'], $lieu['adresse'], $lieu['nbpldeb'], $lieu['nbplass']), $lieuxData);
    }


    /**
     * Récupère la liste des spectacles correspondant aux critères
     * @param array $styles la liste des styles
     * @param array $lieux la liste des lieux
     * @param string|null $dateStart la date de début de la plage de recherche
     * @param string|null $dateEnd la date de fin de la plage de recherche
     * @return array|null la liste des spectacles ou null si aucun spectacle n'est trouvé
     * @throws DateMalformedStringException si la date n'est pas au format attendu
     */
    public function getSpectacles(array $styles = [], array $lieux = [], ?string $dateStart = null, ?string $dateEnd = null): ?array
    {
        $query = 'SELECT * FROM SPECTACLE WHERE 1=1';
        $params = [];

        if (!empty($styles)) {
            $placeholders = implode(',', array_fill(0, count($styles), '?'));
            $query .= " AND style IN ($placeholders)";
            $params = array_merge($params, $styles);
        }

        if (!empty($lieux)) {
            $placeholders = implode(',', array_fill(0, count($lieux), '?'));
            $query .= " AND lieu IN ($placeholders)";
            $params = array_merge($params, $lieux);
        }

        if ($dateStart) {
            $query .= ' AND date >= ?';
            $params[] = (new DateTime($dateStart))->format('Y-m-d H:i:s');
        }

        if ($dateEnd) {
            $query .= ' AND date <= ?';
            $params[] = (new DateTime($dateEnd))->format('Y-m-d H:i:s');
        }

        $query .= ' ORDER BY date';

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
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
     * @throws DateIncompatibleException
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
     * @return array
     * @throws DateMalformedStringException
     */
    public function getSoirees(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SOIREE ORDER BY date');
        $stmt->execute();
        $soireesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$soireesData) {
            return [];
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
     * Récupère la liste des artistes
     * @return array|null
     */
    public function getArtistes(): ?array
    {
        $stmt = $this->pdo->query('SELECT * FROM ARTISTE');
        $artistesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$artistesData) {
            return null;
        }

        return array_map(fn($artiste) => $this->mapToArtiste($artiste), $artistesData);
    }


    /**
     * Récupère la liste des lieux
     * @param int $lieuId
     * @return Lieu
     */
    public function fetchLieu(int $lieuId): Lieu
    {
        $stmt = $this->pdo->prepare('SELECT * FROM LIEU WHERE id = :id');
        $stmt->execute(['id' => $lieuId]);

        if (!$stmt->rowCount()) {
            throw new InvalidArgumentException("Lieu non trouvé");
        }

        $lieuData = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Lieu($lieuData['id'], $lieuData['nom'], $lieuData['adresse'], $lieuData['nbplass'], $lieuData['nbpldeb']);
    }


    /**
     * Récupère la liste des artistes d'un spectacle
     * @param int $spectacleId l'ID du spectacle
     * @return array la liste des artistes
     */
    public function fetchArtistes(int $spectacleId): array
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
     * @throws InvalidArgumentException|DateIncompatibleException
     */
    private function mapToSpectacle(array $spectacleData): Spectacle
    {
        $lieu = $this->fetchLieu($spectacleData['lieu']);
        $artistes = $this->fetchArtistes($spectacleData['id']);

        // Si l'attribut 'annule' est un string, on le convertit en entier
        if (is_string($spectacleData['annule'])) {
            $spectacleData['annule'] = (int)$spectacleData['annule'];
        }

        return new Spectacle(
            $spectacleData['id'],
            $spectacleData['nom'],
            new DateTime($spectacleData['date']),
            $spectacleData['duree'],
            $artistes,
            $spectacleData['style'],
            $lieu,
            $spectacleData['description'],
            $spectacleData['annule'] === 1,
            $spectacleData['url'] ?? null,
            $spectacleData['soiree'] ?? null,
        );
    }


    /**
     * Récupère la liste des spectacles d'une soirée
     * @param int $soireeId
     * @return array
     * @throws DateMalformedStringException|DateIncompatibleException
     */
    private function fetchSpectaclesForSoiree(int $soireeId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM SPECTACLE WHERE soiree = :soireeId ORDER BY date');
        $stmt->execute(['soireeId' => $soireeId]);
        $spectaclesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($spectacle) => $this->mapToSpectacle($spectacle), $spectaclesData);
    }


    /**
     * Transforme un tableau de données en objet Soiree
     * @param array $soireeData
     * @return Soiree
     * @throws DateMalformedStringException|DateIncompatibleException
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


    /**
     * Transforme un tableau de données en objet Artiste
     * @param array $artisteData
     * @return Artiste
     */
    private function mapToArtiste(array $artisteData): Artiste
    {
        return new Artiste($artisteData['id'], $artisteData['nomArtiste']);
    }


    /**
     * Récupère un utilisateur
     * @param string $email
     * @return array
     * @throws AuthnException
     */
    public function getIdPasswd(string $email): array
    {
        $stmt = $this->pdo->prepare('SELECT id, password FROM UTILISATEUR WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new AuthnException('Utilisateur non trouvé');
        } else {
            return $row;
        }
    }


    /**
     * getUtilisateur
     * @param int $id l'ID de l'utilisateur
     * @return Utilisateur
     */
    public function getUtilisateur(int $id): Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT * FROM UTILISATEUR WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $utilisateurData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$utilisateurData) {
            throw new InvalidArgumentException("Utilisateur non trouvé");
        }
        return new Utilisateur($utilisateurData['nom'], $utilisateurData['email'], $utilisateurData['role']);
    }


    /**
     * Récupère les spectacles similaires à un spectacle donné
     * @param int $spectacleId l'ID du spectacle
     * @param string $style le style du spectacle
     * @param int $lieuId l'ID du lieu
     * @param DateTime $date la date du spectacle
     * @return array la liste des spectacles similaires
     * @throws DateMalformedStringException|DateIncompatibleException si la date n'est pas au format attendu
     */
    public function getSimilarSpectacles(int $spectacleId, string $style, int $lieuId, DateTime $date): array
    {
        $query = 'SELECT * FROM SPECTACLE WHERE id != ? AND (style = ? OR lieu = ? OR DATE(date) = ?)';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$spectacleId, $style, $lieuId, $date->format('Y-m-d')]);
        $spectaclesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($spectacle) => $this->mapToSpectacle($spectacle), $spectaclesData);
    }


    /**
     * Ajoute un utilisateur
     * @param Utilisateur $utilisateur l'utilisateur à ajouter
     * @param string $passwordhash
     * @return void l'ID de l'utilisateur ajouté
     * @throws InscriptionException si l'utilisateur existe déjà
     */
    public function addUtilisateur(Utilisateur $utilisateur, string $passwordhash): void
    {
        //verification si l'utilisateur existe déjà
        $stmt = $this->pdo->prepare('SELECT id FROM UTILISATEUR WHERE email = :email');
        $stmt->execute(['email' => $utilisateur->getEmail()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            throw new InscriptionException('Un utilisateur avec cet email existe déjà');
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO UTILISATEUR (nom, email, password, role) VALUES (:nom, :email, :password, :role)');
            $stmt->execute([
                'nom' => $utilisateur->getNom(),
                'email' => $utilisateur->getEmail(),
                'password' => $passwordhash,
                'role' => $utilisateur->getRole()
            ]);
        }
    }


    /**
     * Ajoute un spectacle
     * @param Spectacle $spectacle le spectacle à ajouter
     * @return int l'ID du spectacle ajouté
     */
    public function addSpectacle(Spectacle $spectacle): int
    {
        if ($spectacle->getSoireeId() != null) {
            $soiree = NRVRepository::getInstance()->getSoiree($spectacle->getSoireeId());
            $soiree->ajouterSpectaclePossible($spectacle);
        }
        $stmt = $this->pdo->prepare('SELECT ID FROM SPECTACLE WHERE NOM=:nom');
        $stmt->execute(['nom' => $spectacle->getTitre()]);
        if (is_array($stmt->fetch())) {
            throw new SpectacleAssignationException("Un spectacle de ce nom existe déjà");
        }
        $stmt = $this->pdo->prepare('INSERT INTO SPECTACLE (nom, style, url, date, duree, annule, description, lieu, soiree) VALUES (:nom, :style, :url, :date, :duree, :annule, :description, :lieu, :soiree)');
        $stmt->execute([
            'nom' => $spectacle->getTitre(),
            'style' => $spectacle->getStyle(),
            'url' => $spectacle->getUrl(),
            'date' => $spectacle->getDate()->format('Y-m-d H:i:s'),
            'duree' => $spectacle->getDuree(),
            'annule' => 0,
            'description' => $spectacle->getDescription(),
            'lieu' => $spectacle->getLieu()->getId(),

            'soiree' => $spectacle->getSoireeId()
        ]);
        return $this->pdo->lastInsertId();
    }


    /**
     * Ajoute une soirée
     * @param Soiree $soiree la soirée à ajouter
     * @return int l'ID de la soirée ajoutée
     */
    public function addSoiree(Soiree $soiree): int
    {
        $stmt = $this->pdo->prepare('SELECT ID FROM SOIREE WHERE NOM=:nom');
        $stmt->execute(['nom' => $soiree->getNom()]);
        if (is_array($stmt->fetch())) {
            throw new SoireeAssignationException("Une soiree de ce nom existe déjà");
        }
        $stmt = $this->pdo->prepare('INSERT INTO SOIREE (nom, theme, date, lieu) VALUES (:nom, :theme, :date, :lieu)');
        $stmt->execute([
            'nom' => $soiree->getNom(),
            'theme' => $soiree->getTheme(),
            'date' => $soiree->getDate()->format('Y-m-d H:i:s'),
            'lieu' => $soiree->getLieu()->getId()
        ]);
        return $this->pdo->lastInsertId();
    }


    /**
     * Ajoute un artiste
     * @param string $artiste nom de l'artiste à ajouter
     * @return int l'ID de l'artiste ajouté
     */
    public function addArtiste(string $artiste): int
    {
        $stmt = $this->pdo->prepare('SELECT ID FROM ARTISTE WHERE NOMARTISTE=:nom');
        $stmt->execute(['nom' => $artiste]);
        if (is_array($stmt->fetch())) {
            throw new ArtisteDuplicationException("Un Artiste de ce nom existe déjà");
        }

        $stmt = $this->pdo->prepare('INSERT INTO ARTISTE (nomArtiste) VALUES (:nomArtiste)');
        $stmt->execute([
            'nomArtiste' => $artiste
        ]);
        return $this->pdo->lastInsertId();
    }


    /**
     * Ajoute un lieu
     * @param Lieu $lieu le lieu à ajouter
     * @return int l'ID du lieu ajouté
     */
    public function addLieu(Lieu $lieu): int
    {
        $stmt = $this->pdo->prepare('SELECT ID FROM LIEU WHERE NOM=:nom');
        $stmt->execute(['nom' => $lieu->getAdresse()]);
        if (is_array($stmt->fetch())) {
            throw new LieuDuplicationException("Ce lieu existe déjà");
        }

        $stmt = $this->pdo->prepare('INSERT INTO LIEU (nom, adresse, nbpldeb, nbplass) VALUES (:nom, :adresse, :nbpldeb, :nbplass)');
        $stmt->execute([
            'nom' => $lieu->getNom(),
            'adresse' => $lieu->getAdresse(),
            'nbpldeb' => $lieu->getNbPlacesDebout(),
            'nbplass' => $lieu->getNbPlacesAssises()
        ]);
        return $this->pdo->lastInsertId();
    }


    /**
     * Associe des artistes à un spectacle
     * @param int $id l'ID du spectacle
     * @param array $artistes la liste des artistes à associer
     * @return void
     */
    public function addArtistesToSpectacle(int $id, array $artistes): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO JOUE (idsp, ida) VALUES (:idsp, :ida)');
        foreach ($artistes as $artiste) {
            $stmt->execute([
                'idsp' => $id,
                'ida' => $artiste
            ]);
        }
    }


    /**
     * Met à jour un spectacle existant
     * @param Spectacle $spectacle le spectacle à modifier
     * @return bool true si la mise à jour a réussi, false sinon
     */
    public function updateSpectacle(Spectacle $spectacle): bool
    {
        $stmt = $this->pdo->prepare("UPDATE SPECTACLE 
                SET nom = :titre, style = :style, url = :url, date = :date, duree = :duree, description = :description, lieu = :lieu_id, soiree = :soiree
                WHERE id = :id");
        $stmt->execute([
            'id' => $spectacle->getId(),
            'titre' => $spectacle->getTitre(),
            'style' => $spectacle->getStyle(),
            'url' => $spectacle->getUrl(),
            'date' => $spectacle->getDate()->format('Y-m-d H:i:s'),
            'duree' => $spectacle->getDuree(),
            'description' => $spectacle->getDescription(),
            'lieu_id' => $spectacle->getLieu()->getId(),
            'soiree' => $spectacle->getSoireeId()
        ]);
        return $stmt->rowCount() > 0;
    }


    /**
     * Supprime les artistes associés à un spectacle
     * @param int $idSpectacle l'ID du spectacle
     * @return void
     */
    public function removeArtistesFromSpectacle(int $idSpectacle): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM JOUE WHERE idsp = :idSpectacle');
        $stmt->execute(['idSpectacle' => $idSpectacle]);
    }


    /**
     * Met à jour une soirée existante
     * @param Soiree $soiree la soirée à modifier
     * @return bool true si la mise à jour a réussi, false sinon
     */
    public function updateSoiree(Soiree $soiree): bool
    {
        $stmt = $this->pdo->prepare("UPDATE SOIREE 
                SET nom = :nom, theme = :theme, date = :date, lieu = :lieu_id
                WHERE id = :id");
        $stmt->execute([
            'id' => $soiree->getId(),
            'nom' => $soiree->getNom(),
            'theme' => $soiree->getTheme(),
            'date' => $soiree->getDate()->format('Y-m-d H:i:s'),
            'lieu_id' => $soiree->getLieu()->getId()
        ]);
        return $stmt->rowCount() > 0;
    }


    /**
     * ajoute une image dans la base de données
     * @param String $NameImage
     */
    public function UploadImage(string $NameImage): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO IMAGE (nom) VALUES (:nom)');
        $stmt->execute([
            'nom' => $NameImage
        ]);
    }


    /**
     * ajoute une image à un spectacle
     * @param int $idSpectacle
     * @param array $idImage
     */
    public function addImagesToSpectacle(int $idSpectacle, array $images): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO IMAGESPECTACLE (idi, idsp) VALUES (:idi, :idsp)');
        foreach ($images as $image) {
            $idImage = $this->getIdImage($image);
            $stmt->execute([
                'idi' => $idImage,
                'idsp' => $idSpectacle
            ]);
        }
    }


    /**
     * ajoute une image à une soirée
     * @param int $idSoiree
     * @param array $idImage
     */
    public function addImagesToSoiree(int $idSoiree, array $images): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO IMAGESOIREE (idi, ids) VALUES (:idi, :ids)');
        foreach ($images as $image) {
            $idImage = $this->getIdImage($image);
            $stmt->execute([
                'idi' => $idImage,
                'ids' => $idSoiree
            ]);
        }
    }

    /**
     * ajoute une image à un lieu
     * @param int $idLieu
     * @param array $idImage
     */
    public function addImageToLieu(int $idLieu, array $images): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO IMAGELIEU (idi, idl) VALUES (:idi, :idl)');
        foreach ($images as $image) {
            $idImage = $this->getIdImage($image);
            $stmt->execute([
                'idi' => $idImage,
                'idl' => $idLieu
            ]);
        }
    }


    /**
     * supprime les images d'un spectacle
     * @param int $idSpectacle
     */
    public function removeImagesFromSpectacle(int $idSpectacle): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM IMAGESPECTACLE WHERE idsp = :idsp');
        $stmt->execute(['idsp' => $idSpectacle]);
    }


    /**
     * supprime les images d'une soirée
     * @param int $idSoiree
     */
    public function removeImagesFromSoiree(int $idSoiree): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM IMAGESOIREE WHERE ids = :ids');
        $stmt->execute(['ids' => $idSoiree]);
    }


    /**
     * getImages retourne les images d'un spectacle
     * @param int $idSpectacle
     * @return array
     */
    public function getImagesSpectacle(int $idSpectacle): array
    {
        $stmt = $this->pdo->prepare('SELECT nom FROM IMAGE INNER JOIN IMAGESPECTACLE ON IMAGE.id = IMAGESPECTACLE.idi WHERE idsp = :idsp');
        $stmt->execute(['idsp' => $idSpectacle]);
        $imagesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($image) => $image['nom'], $imagesData);
    }


    /**
     * getImages retourne les images d'une soirée
     * @param int $idSoiree l'ID de la soirée
     * @return array
     */
    public function getImagesSoiree(int $idSoiree): array
    {
        $stmt = $this->pdo->prepare('SELECT nom FROM IMAGE INNER JOIN IMAGESOIREE ON IMAGE.id = IMAGESOIREE.idi WHERE ids = :ids');
        $stmt->execute(['ids' => $idSoiree]);
        $imagesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($image) => $image['nom'], $imagesData);
    }


    /**
     * getImages retourne les images d'un lieu
     * @param int $idLieu
     * @return array
     */
    public function getImagesLieu(int $idLieu): array
    {
        $stmt = $this->pdo->prepare('SELECT nom FROM IMAGE INNER JOIN IMAGELIEU ON IMAGE.id = IMAGELIEU.idi WHERE idl = :idl');
        $stmt->execute(['idl' => $idLieu]);
        $imagesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($image) => $image['nom'], $imagesData);
    }


    /**
     * getImages retourne les images
     * @return array
     */
    public function getImages(): array
    {
        $stmt = $this->pdo->prepare('select nom from IMAGE');
        $stmt->execute();
        $imagesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($image) => $image['nom'], $imagesData);
    }


    /**
     * Annule ou restaure un spectacle
     * @param int $idSpectacle l'ID du spectacle
     * @param bool $annule true pour annuler, false pour restaurer
     * @return void
     */
    public function modifierAnnulationSpectacle(int $idSpectacle, bool $annule): void
    {
        $stmt = $this->pdo->prepare('UPDATE SPECTACLE SET annule = :annule WHERE id = :id');
        $stmt->execute([
            'id' => $idSpectacle,
            'annule' => $annule ? 1 : 0
        ]);
    }

    /**
     * getIdImage retourne l'id d'une image
     * @param string $nom
     * @throws InvalidArgumentException
     */
    public function getIdImage(string $nom)
    {
        $stmt = $this->pdo->prepare('SELECT id FROM IMAGE WHERE nom = :nom');
        $stmt->execute([
            'nom' => $nom
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new InvalidArgumentException('Image non trouvée');
        } else {
            return $row['id'];
        }
    }
}