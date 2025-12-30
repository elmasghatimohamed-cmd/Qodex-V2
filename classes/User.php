<?php
/**
 * Classe User
 * Gère les opérations liées aux utilisateurs
 * 
 * CONCEPTS OOP DÉMONTRÉS:
 * - Encapsulation (propriétés privées)
 * - Accesseurs (getters/setters)
 * - Visibilité (private, public)
 */

class User
{
    // ============================================
    // PROPRIÉTÉS PRIVÉES (Encapsulation)
    // ============================================


    private $db;


    private $id;


    private $nom;


    private $email;


    private $role;


    private $createdAt;

    // ============================================
    // CONSTRUCTEUR
    // ============================================

    /**
     * Constructeur - Initialise la connexion à la base de données
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ============================================
    // GETTERS (Accesseurs en lecture)
    // ============================================

    // Getter pour l'ID

    public function getId()
    {
        return $this->id;
    }

    // Getter pour le nom

    public function getNom()
    {
        return $this->nom;
    }

    // Getter pour l'email

    public function getEmail()
    {
        return $this->email;
    }

    // Getter pour le rôle

    public function getRole()
    {
        return $this->role;
    }

    // Getter pour la date de création

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    // ============================================
    // SETTERS (Accesseurs en écriture)
    // ============================================

    // Setter pour le nom (avec validation)

    public function setNom($nom)
    {
        // Validation: le nom ne peut pas être vide
        if (!empty($nom)) {
            $this->nom = $nom;
        }
    }

    // Setter pour l'email (avec validation)

    public function setEmail($email)
    {
        if (Security::validateEmail($email)) {
            $this->email = $email;
            return true;
        }
        return false;
    }

    // Setter pour le rôle (avec validation)

    public function setRole($role)
    {
        // Seuls 'enseignant' et 'etudiant' sont autorisés
        if (in_array($role, ['enseignant', 'etudiant'])) {
            $this->role = $role;
            return true;
        }
        return false;
    }

    // ============================================
    // MÉTHODES PRIVÉES (Helper methods)
    // ============================================

    // Remplit les propriétés depuis un tableau (résultat SQL)

    private function hydrate($data)
    {
        $this->id = $data['id'] ?? null;
        $this->nom = $data['nom'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->role = $data['role'] ?? '';
        $this->createdAt = $data['created_at'] ?? '';
    }

    // ============================================
    // MÉTHODES PUBLIQUES (CRUD)
    // ============================================

    // Crée un nouvel utilisateur

    public function create($nom, $email, $password, $role)
    {
        // Validation
        if (empty($nom) || empty($email) || empty($password)) {
            return false;
        }

        if (!Security::validateEmail($email)) {
            return false;
        }

        if (!Security::validatePassword($password)) {
            return false;
        }

        // Vérifier si l'email existe déjà
        if ($this->emailExists($email)) {
            return false;
        }

        // Hash du mot de passe
        $passwordHash = Security::hashPassword($password);

        // Insertion
        $sql = "INSERT INTO users (nom, email, password_hash, role) VALUES (?, ?, ?, ?)";
        try {
            $this->db->query($sql, [$nom, $email, $passwordHash, $role]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Vérifie si un email existe

    public function emailExists($email)
    {
        $sql = "SELECT id FROM users WHERE email = ? AND deleted_at IS NULL";
        $result = $this->db->query($sql, [$email]);
        return $result->rowCount() > 0;
    }

    // Connexion utilisateur

    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL";
        $result = $this->db->query($sql, [$email]);
        $user = $result->fetch();

        if ($user && Security::verifyPassword($password, $user['password_hash'])) {
            // Remplir les propriétés de l'objet
            $this->hydrate($user);

            // Initialiser la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Régénérer l'ID de session
            session_regenerate_id(true);

            return $user;
        }

        return false;
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    // Récupère un utilisateur par ID

    public function getById($id)
    {
        $sql = "SELECT id, nom, email, role, created_at FROM users WHERE id = ? AND deleted_at IS NULL";
        $result = $this->db->query($sql, [$id]);
        $data = $result->fetch();

        if ($data) {
            // Remplir les propriétés
            $this->hydrate($data);
        }

        return $data;
    }

    // Met à jour un utilisateur

    public function update($id, $nom, $email)
    {
        $sql = "UPDATE users SET nom = ?, email = ? WHERE id = ?";
        try {
            $this->db->query($sql, [$nom, $email, $id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Suppression logique d'un utilisateur

    public function softDelete($id)
    {
        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = ?";
        try {
            $this->db->query($sql, [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
