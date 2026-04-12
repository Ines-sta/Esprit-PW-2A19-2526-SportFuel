# Guide d'installation - SportFuel

Ce guide vous aidera à configurer et lancer le projet SportFuel sur votre ordinateur local.

## 🛠️ Pré-requis
1.  **XAMPP** ou **WAMP** installé sur votre ordinateur.
2.  Un navigateur Web moderne (Chrome, Edge, Firefox).

## 🚀 Étapes d'installation

### 1. Préparation des fichiers
Déplacez tout le dossier du projet (`SportFuel-Module1`) dans le dossier "serveur" de votre logiciel :
*   **XAMPP** : `C:\xampp\htdocs\`
*   **WAMP** : `C:\wamp64\www\`

### 2. Démarrage du serveur
1.  Ouvrez le panneau de contrôle de XAMPP/WAMP.
2.  Démarrez les services **Apache** et **MySQL**.

### 3. Création de la Base de Données
1.  Allez sur [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).
2.  Cliquez sur l'onglet **Importer**.
3.  Choisissez le fichier `database.sql` qui se trouve à la racine de votre projet.
4.  Cliquez sur le bouton **Importer** en bas de page.
    *   *Note : Cela créera la base de données `sportfuel` et la table `utilisateurs` avec la structure correcte.*

### 4. Configuration (Si nécessaire)
Si vous avez un mot de passe pour votre utilisateur MySQL "root", modifiez le fichier `controller/config.php` :
```php
$mot_de_passe = "votre_mot_de_passe";
```

### 5. Accès au projet
Ouvrez votre navigateur et allez à l'adresse suivante :
[http://localhost/SportFuel-Module1/view/index.html](http://localhost/SportFuel-Module1/view/index.html)

## ⚠️ Notes importantes
*   **Session** : Assurez-vous d'accéder au site via `http://localhost/` et non en ouvrant les fichiers directement (évitez `file:///C:/...`).
*   **Erreurs** : Si vous rencontrez une erreur de connexion, vérifiez que MySQL est bien démarré.
