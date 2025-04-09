# 🧾 Documentation technique du projet – Brasserie Artisanale

## 📌 Présentation générale

Ce projet a été développé dans le cadre du **titre professionnel DWWM (Développeur Web et Web Mobile)**. Il s'agit d'une application web responsive permettant à une **brasserie artisanale locale** de présenter ses produits et de proposer un service de commande en ligne avec retrait en point de vente. Le site est disponible en **version desktop et mobile**, et intègre un **espace administrateur** pour la gestion des contenus et des commandes.

> **Technologies principales :** Symfony, PHP, HTML5, CSS3, JavaScript, Stripe, Chart.js, Dompdf

---

## 🎯 Objectifs fonctionnels

### Pour les visiteurs :
- 🔍 Navigation sur les pages vitrines (bières, goodies, brasserie, taproom)
- 🛒 Parcours d’achat sans création de compte
- 🧾 Paiement en ligne sécurisé via Stripe
- 📥 Téléchargement automatique de la facture PDF après commande

### Pour les utilisateurs authentifiés :
- 👤 Création de compte sécurisée (e-mail + mot de passe fort)
- ❤️ Gestion des favoris
- 📜 Historique des commandes
- 📄 Téléchargement de factures en PDF
- ✉️ Inscription à la newsletter

### Pour les administrateurs :
- 🧮 Gestion du catalogue produit (CRUD)
- 📦 Gestion des stocks
- 📬 Création et envoi de newsletters
- 🧑‍💼 Gestion des comptes utilisateurs
- 📊 Visualisation des ventes via des graphiques interactifs (Chart.js)
- 💰 Suivi du chiffre d’affaires mensuel

---

## ⚙️ Stack technique

| Élément        | Technologie ou outil            |
|----------------|---------------------------------|
| Framework      | Symfony 7                       |
| Langages       | PHP 8.3.9, HTML5, CSS3         |
| Front          | Twig, JavaScript                |
| Graphiques     | Chart.js                        |
| PDF            | Dompdf                          |
| Paiement       | Stripe API                      |
| Captcha        | Google reCAPTCHA v3             |
| Sécurité       | Symfony SecurityBundle, regex   |
| Base de données| MySQL (Doctrine ORM)            |
| Interface admin| EasyAdminBundle                 |

---

## 🔐 Sécurité

- 🔑 **Gestion des rôles** via `ROLE_USER`, `ROLE_ADMIN`
- 🔒 **Mots de passe hachés** (Bcrypt, via PasswordHasher Symfony)
- 🧠 **Regex personnalisées** pour valider les champs critiques
- 🔁 **Protection CSRF** active sur tous les formulaires sensibles
- ⚠️ **Throttling / rate limiting** via reCAPTCHA v3 (anti-bot)
- 🕵️ **Anonymisation des comptes supprimés** (hash + salt)
- 🧪 **Vérification e-mail** et **réinitialisation sécurisée du mot de passe**

---

## 📈 Performances

- 🔧 **Optimisation via PageSpeed Insights**
- 🚀 Chargement rapide des pages
- 🎯 Score Lighthouse :
  - **Accessibilité : 95/100**
  - **SEO : 91/100**
  - **Performance : optimisée**

---

## ♿ Accessibilité

- 🌙 Mode sombre (dark mode)
- ⌨️ Navigation clavier facilitée
- 🎨 Contrastes élevés conformes aux WCAG
- 🔤 Polices lisibles, tailles responsives
- 🖼️ Texte alternatif pour toutes les images (`alt`)

---

## 📬 RGPD & respect des données personnelles

- 🧾 Collecte **minimale** de données (seulement e-mail + nom/prénom pour commande)
- 🔐 Suppression du compte possible à tout moment (via l'espace personnel)
- 🧹 Données anonymisées post-suppression
- 🍪 Bannière de cookies transparente (générée avec CookieConsent)
- 🔕 Lien de désinscription présent dans chaque newsletter

---

## 📁 Structure des fichiers (simplifiée)

```
/src
  /Controller
  /Entity
  /Repository
  /Service
  /Security
  /Form
/templates
/public
/config
/migrations
```

---

## 📌 Environnement de développement

- ✅ **VSCode** avec extensions Symfony et Twig
- ✅ **Laragon** (Windows) / **MAMP** (MacOS)
- ✅ **HeidiSQL / PHPMyAdmin** pour gestion BDD
- ✅ **Git + GitHub** pour le versioning

---

## 📚 Annexes utiles

- ✅ README complet dans le dépôt GitHub
- 📸 Captures d’écran du front + back (admin)
- 📄 MCD / MLD réalisés avec Looping & JMerise
- 🧠 Figma : maquette UI/UX desktop & mobile

---

## 🧪 Installation du projet en local

### Prérequis

- PHP ≥ 8.1
- Composer
- Symfony CLI (optionnel mais recommandé)
- Serveur local type **MAMP**, **WAMP**, **Laragon** ou **XAMPP**
- MySQL/MariaDB
- Un navigateur web moderne (Chrome, Firefox…)

### Étapes d'installation

1. **Cloner le dépôt**

```bash
git clone https://github.com/ton-utilisateur/brasserie-artisanale.git
cd brasserie-artisanale
```

2. **Installer les dépendances PHP**

```bash
composer install
```

3. **Configurer l’environnement**

Créer un fichier `.env.local` à partir du fichier `.env` :

```
cp .env .env.local
```

Modifier les variables de connexion à la base de données dans `.env.local` :

```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/nom_de_la_base"
```

4. **Créer la base de données et exécuter les migrations**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. **Charger des données de test (facultatif)**

```bash
php bin/console doctrine:fixtures:load
```

6. **Lancer le serveur local**

```bash
symfony server:start
```

Accéder ensuite au site via `http://localhost:8000`

---

## 🚀 Déploiement

Le projet **n’a pas été déployé en production**, mais le code est entièrement versionné sur GitHub et prêt à être hébergé sur un serveur PHP/MySQL (OVH, Ionos, etc.). Un futur déploiement pourrait s’appuyer sur un processus automatisé (CI/CD) ou manuel via FTP/SSH.
