<?php
session_start();
require_once __DIR__ . '/../model/Utilisateur.php';
require_once __DIR__ . '/config.php';

class AuthController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function inscription($data) {
        if (empty($data['nom']) || empty($data['email']) || empty($data['password'])) {
            return array('success' => false, 'message' => 'Tous les champs sont obligatoires');
        }
        
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($data['nom']);
        $utilisateur->setEmail($data['email']);
        $utilisateur->setPassword($data['password']); // Haches the password
        $utilisateur->setAge($data['age'] ?? 0);
        $utilisateur->setPoids($data['poids'] ?? 0);
        $utilisateur->setTaille($data['taille'] ?? 0);
        $utilisateur->setRole($data['role'] ?? 'Sportif');
        
        try {
            if ($utilisateur->save($this->pdo)) {
                $_SESSION['user_email'] = $utilisateur->getEmail();
                $_SESSION['user_nom'] = $utilisateur->getNom();
                $_SESSION['role'] = $utilisateur->getRole() ?? 'Sportif';
                return array('success' => true, 'message' => 'Inscription réussie', 'role' => $_SESSION['role']);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Constraint violation (Duplicate Email)
                return array('success' => false, 'message' => 'Cet email est deja utilise');
            }
        }
        return array('success' => false, 'message' => 'Erreur lors de l\'inscription dans la bd');
    }

    public function connexion($email, $password) {
        if (empty($email) || empty($password)) {
            return array('success' => false, 'message' => 'Email et mot de passe requis');
        }
        
        $user = Utilisateur::findByEmail($this->pdo, $email);
        if ($user && password_verify($password, $user->getPassword())) {
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_nom'] = $user->getNom();
            $_SESSION['role'] = $user->getRole();
            return array('success' => true, 'message' => 'Connexion réussie', 'role' => $user->getRole());
        }
        return array('success' => false, 'message' => 'Email ou mot de passe incorrect');
    }
}

// Automatically process POST requests coming from forms
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $auth = new AuthController($pdo);
    
    if ($_GET['action'] === 'inscription') {
        $result = $auth->inscription($_POST);
        if ($result['success']) {
            if ($result['role'] === 'Admin') {
                header('Location: ../view/admin.php');
            } else {
                header('Location: ../view/profil.php');
            }
            exit();
        } else {
            echo "<script>alert('".$result['message']."'); window.location.href='../view/inscription.html';</script>";
        }
    }
    
    if ($_GET['action'] === 'connexion') {
        $result = $auth->connexion($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['success']) {
            if ($result['role'] === 'Admin') {
                header('Location: ../view/admin.php');
            } else {
                header('Location: ../view/profil.php');
            }
            exit();
        } else {
            echo "<script>alert('".$result['message']."'); window.location.href='../view/connexion.html';</script>";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'logout') {
        session_destroy();
        header('Location: ../view/connexion.html');
        exit();
    }
}
?>
