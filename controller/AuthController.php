<?php
require_once __DIR__ . '/../model/Utilisateur.php';

class AuthController {
    public function inscription($data) {
        // Validation des données
        if (empty($data['nom']) || empty($data['email']) || empty($data['password'])) {
            return array('success' => false, 'message' => 'Tous les champs sont obligatoires');
        }
        
        // Créer un nouvel utilisateur
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($data['nom']);
        $utilisateur->setEmail($data['email']);
        $utilisateur->setPassword($data['password']);
        $utilisateur->setAge($data['age'] ?? 0);
        $utilisateur->setPoids($data['poids'] ?? 0);
        $utilisateur->setTaille($data['taille'] ?? 0);
        
        return array('success' => true, 'message' => 'Inscription réussie', 'utilisateur' => $utilisateur);
    }

    public function connexion($email, $password) {
        // Logique de connexion
        if (empty($email) || empty($password)) {
            return array('success' => false, 'message' => 'Email et mot de passe requis');
        }
        
        return array('success' => true, 'message' => 'Connexion réussie');
    }
}
?>
