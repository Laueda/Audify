

#  Audify — Plateforme web communautaire musicale

##  Présentation

**Audify** est une plateforme web dédiée à la découverte musicale et aux échanges entre passionnés de musique.

Ce projet a été réalisé en équipe de trois étudiants dans le cadre de notre formation en informatique à l'ESGI.

L'application comprend deux espaces :
- **Front Office** : interface destinée aux utilisateurs pour découvrir des artistes et interagir avec la communauté.
- **Back Office** : interface d'administration permettant de gérer les utilisateurs, les contenus et les fonctionnalités de la plateforme.

**Ma contribution personnelle : développement du Back Office.**

## Technologies utilisées

- **Langages :** PHP, HTML, CSS, JavaScript
- **Base de données :** MySQL
- **Framework CSS :** Bootstrap
- **API externe :** Deezer
- **Bibliothèques :** PHPMailer, FPDF
- **Environnement de développement :** MAMP, Visual Studio Code
- **Hébergement du projet :** VPS OVH, Apache

## Fonctionnalités du Front Office

- Inscription, connexion et gestion des profils utilisateurs
- Recherche et découverte d'artistes avec l'API Deezer
- Publications, commentaires et mentions J'aime
- Création et gestion de playlists
- Forums de discussion et sondages
- Messagerie privée
- Consultation et création d'événements musicaux
- Inscription à la newsletter

## Back Office — Ma contribution

J'ai développé l'interface d'administration d'Audify, permettant aux administrateurs de superviser et de gérer la plateforme.

### Gestion des utilisateurs
- Consultation et recherche des utilisateurs
- Modification des informations et des statuts des comptes
- Suspension et suppression de comptes
- Consultation des journaux d'activité

### Gestion des droits et de la sécurité
- Gestion des rôles et des droits d'accès
- Restriction de l'accès aux fonctionnalités d'administration
- Utilisation des sessions PHP pour l'authentification

### Modération et gestion des contenus
- Gestion des signalements
- Modération des publications et commentaires
- Gestion des artistes et des événements

### Tableau de bord et statistiques
- Affichage d'indicateurs sur l'activité de la plateforme
- Suivi des utilisateurs et des contenus
- Consultation des statistiques administratives

### Gestion des newsletters
- Création et modification des newsletters
- Gestion des abonnés
- Envoi d'emails avec PHPMailer et SMTP

### Autres fonctionnalités
- Gestion des concours
- Export de documents PDF avec FPDF
- Gestion et consultation des commandes

## Organisation du projet

```text
audify/
├── PA_BO/                 # Back Office (administration)
├── JS/                    # Scripts JavaScript
├── images/                # Ressources graphiques
├── index.php              # Page d'accueil
├── login.php              # Connexion utilisateur
├── register.php           # Inscription
├── composer.json          # Dépendances PHP
└── README.md              # Documentation du projet
```

## Installation locale

**Prérequis :**
- PHP
- MySQL
- Apache (MAMP, XAMPP ou équivalent)
- Composer

1. Cloner le repository GitHub.
2. Installer les dépendances PHP avec `composer install`.
3. Configurer une base de données MySQL.
4. Ajouter les fichiers de configuration locaux nécessaires à la connexion à la base de données et aux services SMTP.
5. Démarrer Apache et MySQL, puis accéder au projet depuis le navigateur.

**Remarque :** les fichiers contenant des identifiants et les données de la base MySQL ne sont pas inclus dans le repository.

## Travail en équipe

Projet réalisé en collaboration avec deux autres étudiants.

**Responsabilité principale :** conception et développement des fonctionnalités du Back Office, notamment l'administration des utilisateurs, la gestion des droits, la modération, les statistiques et les newsletters.

## Compétences mobilisées

- Développement web avec PHP et MySQL
- Manipulation et exploitation d'une base de données relationnelle
- Gestion des sessions et des autorisations
- Développement d'interfaces d'administration
- Intégration de bibliothèques externes
- Gestion de projet et collaboration en équipe
- Déploiement d'une application web sur serveur Linux
