# Mini_chat - Application de Chat en Temps Réel

Une application web de chat développée en PHP.

## 🚀 Fonctionnalités

### 👥 Gestion des Utilisateurs
- **Authentification sécurisée** avec hachage des mots de passe
- **Système de rôles** : Utilisateurs, Administrateurs, Comptes inactifs
- **Gestion des sessions** avec sécurité renforcée
- **Récupération de mot de passe** par email avec code de validation
- **Statut en ligne/hors ligne** des utilisateurs

### 💬 Messagerie
- **Chat en temps réel** entre utilisateurs
- **Envoi de fichiers** (images, vidéos, documents, etc.)
- **Modification et suppression** de messages
- **Indicateurs de lecture** (vus/non vus)
- **Historique des conversations**
- **Liens cliquables** automatiques dans les messages

### 🛡️ Sécurité
- **Chiffrement AES-256** pour les données sensibles
- **Protection CSRF** avec SameSite cookies
- **Validation et échappement** des entrées utilisateur
- **Sessions sécurisées** avec paramètres HTTPOnly

### 👨‍💼 Administration
- **Interface d'administration** complète
- **Gestion des utilisateurs** (activation/désactivation)
- **Historique des actions** des utilisateurs
- **Suppression de comptes** avec confirmation

## 📁 Structure du Projet

```
mini_chat/
├── 📁 account/                 # Interface utilisateur connecté
│   ├── index.php              # Point d'entrée principal
│   ├── index_users.php        # Interface utilisateur standard
│   ├── index_admin.php        # Interface administrateur
│   ├── chat_users.php         # Système de chat
│   ├── history.php            # Historique des actions
│   ├── compte_delete.php      # Suppression de comptes
│   ├── list.php               # Liste des utilisateurs
│   ├── ajax.php               # Requêtes AJAX
│   ├── script.php             # Scripts JavaScript
│   ├── nav.php                # Navigation
│   └── lg.php                 # Gestion de la déconnexion
├── 📁 assets/                 # Ressources statiques
│   ├── 📁 css/               # Styles CSS
│   │   ├── style.css         # Style principal
│   │   ├── account.css       # Styles interface utilisateur
│   │   └── admin.css         # Styles administration
│   ├── 📁 php/               # Fonctions PHP
│   │   ├── connexion.php     # Configuration base de données
│   │   └── fonctions.php     # Fonctions utilitaires
│   ├── 📁 docs/              # Documents partagés
│   └── 📁 images/            # Images et icônes
├── 📁 documents_projet/       # Documentation projet
│   ├── minichat.sql          # Structure base de données
│   ├── diagramme de class.mermaid
│   └── diagramme de class.png
├── index.php                  # Page de connexion
├── forgot_password.php        # Récupération mot de passe
├── php_server.bat            # Script de démarrage serveur
└── README.md                 # Documentation
```

## 🗄️ Base de Données

### Tables Principales

#### `users`
- **id** : Identifiant unique
- **pseudo** : Nom d'utilisateur (max 6 caractères)
- **email** : Adresse email
- **password** : Mot de passe haché
- **type** : Rôle (users/admin/inactif)
- **online** : Statut en ligne (oui/non)
- **discute** : Utilisateur avec qui discuter
- **code_valid** : Code de validation pour mot de passe
- **dateCreate** : Date de création

#### `chat`
- **id** : Identifiant unique
- **idu** : ID expéditeur
- **id_receiver** : ID destinataire
- **pseudo** : Pseudo expéditeur
- **message** : Contenu du message
- **docs** : Fichier joint
- **vue** : Statut de lecture (oui/non)
- **delete** : Message supprimé (oui/non)
- **dateSent** : Date d'envoi

#### `historique`
- **id** : Identifiant unique
- **id_users** : ID utilisateur
- **action** : Action effectuée
- **dateAction** : Date de l'action

#### `amis`
- **id** : Identifiant unique
- **idu_one** : Premier utilisateur
- **idu_two** : Deuxième utilisateur
- **valide** : Amitié validée (oui/non)

## 🛠️ Installation

### Prérequis
- **PHP** 7.4 ou supérieur
- **MySQL** 5.7 ou supérieur
- **Serveur web** (Apache/Nginx) ou serveur PHP intégré

### Étapes d'Installation

1. **Cloner le projet**
   ```bash
   git clone [url-du-repo]
   cd mini_chat
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL nommée `minichat`
   - Importer le fichier `documents_projet/minichat.sql`

3. **Configurer la connexion**
   - Modifier `assets/php/connexion.php` avec vos paramètres de base de données
   - Modifier la clé de chiffrement dans `assets/php/fonctions.php`

4. **Configurer l'envoi d'emails**
   - Modifier les paramètres SMTP dans `forgot_password.php`
   - Configurer l'adresse d'expédition

5. **Démarrer le serveur**
   ```bash
   # Option 1 : Serveur PHP intégré
   php -S localhost:8000
   
   # Option 2 : Script Windows
   php_server.bat
   ```

## 🔧 Configuration

### Base de Données
```php
// assets/php/connexion.php
$bdd = new PDO('mysql:host=localhost; dbname=minichat', 'username', 'password');
```

### Chiffrement
```php
// assets/php/fonctions.php
$key = 'votre_cle_de_chiffrement_securisee';
```

### Email
```php
// forgot_password.php
$headers .= "From: votre_email@domaine.com";
```

## 🎨 Interface Utilisateur

### Design Responsive
- **Interface moderne** avec animations CSS
- **Design adaptatif** pour mobile et desktop
- **Thème sombre/clair** intégré
- **Icônes Font Awesome** pour une meilleure UX

### Fonctionnalités UI
- **Chargement progressif** avec animations
- **Notifications en temps réel**
- **Interface intuitive** pour la navigation
- **Formulaires validés** côté client et serveur

## 🔒 Sécurité

### Mesures Implémentées
- **Hachage des mots de passe** avec Bcrypt
- **Protection contre les injections SQL** avec requêtes préparées
- **Échappement des données** avec `htmlspecialchars()`
- **Validation des fichiers** uploadés
- **Sessions sécurisées** avec paramètres HTTPOnly
- **Chiffrement AES-256** pour les données sensibles

### Bonnes Pratiques
- **Séparation des préoccupations** (MVC simplifié)
- **Gestion d'erreurs** centralisée
- **Logs d'activité** pour audit
- **Validation côté serveur** obligatoire

## 📱 Utilisation

### Connexion
1. Accéder à `http://localhost:8000`
2. Saisir pseudo et mot de passe
3. Choisir un utilisateur pour discuter

### Administration
1. Se connecter avec un compte administrateur
2. Accéder aux outils de gestion
3. Gérer les utilisateurs et consulter l'historique

### Chat
1. Sélectionner un utilisateur dans la liste
2. Envoyer des messages texte ou fichiers
3. Modifier/supprimer ses propres messages
4. Voir les indicateurs de lecture

## 🚀 Déploiement

### Serveur de Production
1. **Configurer un serveur web** (Apache/Nginx)
2. **Installer PHP et MySQL**
3. **Configurer les permissions** des dossiers
4. **Sécuriser la base de données**
5. **Configurer SSL** pour HTTPS

### Variables d'Environnement
- Configurer les paramètres de base de données
- Définir les clés de chiffrement
- Configurer les paramètres SMTP

## 👨‍💻 Développement

### Technologies Utilisées
- **Backend** : PHP 7.4+
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript, Vue.js 3
- **Sécurité** : OpenSSL, Bcrypt
- **UI** : Font Awesome, CSS Grid/Flexbox

### Architecture
- **Architecture modulaire** avec séparation des responsabilités
- **Pattern MVC** simplifié
- **Gestion d'état** via sessions PHP
- **API REST** pour les requêtes AJAX

## 📄 Licence

Ce projet est développé par **WebDshy** et est disponible sous licence libre.

## 👨‍💼 Auteur

- **WebDshy** : https://github.com/WebDshy
- **Portfolio** : https://webdshy.alwaysdata.net

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer des améliorations
- Soumettre des pull requests