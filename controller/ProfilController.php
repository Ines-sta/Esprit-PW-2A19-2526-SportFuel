<?php
require_once __DIR__ . '/../model/ProfilSportif.php';

class ProfilController {
    public function saveProfil($data) {
        // Validation des données
        if (empty($data['sport']) || empty($data['objectif'])) {
            return array('success' => false, 'message' => 'Les champs obligatoires manquent');
        }
        
        // Créer un profil sportif
        $profil = new ProfilSportif();
        $profil->setSport($data['sport']);
        $profil->setObjectif($data['objectif']);
        $profil->setNiveau($data['niveau'] ?? 'Débutant');
        $profil->setFrequence($data['frequence'] ?? 0);
        
        return array('success' => true, 'message' => 'Profil enregistré', 'profil' => $profil);
    }

    public function getProfil($utilisateur_id) {
        // Récupérer le profil de l'utilisateur
        return array('success' => true, 'message' => 'Profil récupéré');
    }
}
?>
