# Présentation du projet

Ce projet a été développé dans le cadre du titre DWWM (Développeur Web et Web Mobile), niveau V. Il s'agit d'un site vitrine et de vente en ligne pour une brasserie artisanale locale. Le site permet de visualiser les produits de la brasserie et de passer des commandes pour un retrait en point de retrait, sans livraison. Le projet est disponible en version desktop et mobile.

## Fonctionnalités
- Paiement en ligne sécurisé avec Stripe.
- Confirmation de commande générée automatiquement en format PDF.
- Inscription à la newsletter
- Création de compte utilisateur
- Création de liste de produits favoris
- Historique des commandes avec possibilité de télécharger les factures au format PDF
- Panneau administrateur : 
    - Gestion des produits et du stock
    - Gestion des commandes
    - Rédaction et envoi de newsletters
    - Gestion des comptes utilisateurs

## Framework : Symfony

## Langages : PHP, CSS, HTML

## Technologies : Chart.js, DOMPDF 

## APIs : Stripe, Google reCAPTCHA v3

## Sécurité :
- Throttling, Regex, Captcha pour sécuriser les formulaires
- Symfony Security Bundle pour la gestion des utilisateurs et des rôles

## Accessibilité - 95/100 (Lighthouse)
- Mode sombre (dark mode)
- Navigation clavier

## SEO - 91/100 (Lighthouse)

## Conformité RGPD
- Collecte des données limitée aux informations strictement nécessaires à la commande et à la création du compte
- Mise en place d'une bannière cookies pour informer les utilisateurs de l'utilisation des cookies
- Possibilité de suppression du compte utilisateur et anonymisation des données associées
- Fonctionnalité permettant la désinscription à la newsletter
