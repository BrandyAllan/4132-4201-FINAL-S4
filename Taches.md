# Version 1

## 4132 -- Côté Client
* **Authentification :** Implémentation du système de connexion automatique par numéro de téléphone avec vérification des préfixes[cite: 1].
* **Gestion du compte :** Création automatique des nouveaux profils et affichage du solde
* **Opérations clients :** Développement de la logique et des formulaires pour les dépôts, les retraits et les transferts d'argent[cite: 1].
* **Historique :** Intégration du tableau de suivi des transactions pour l'utilisateur connecté
* **UI/UX Client :** Design et intégration des vues clients avec Bootstrap


++ Authetification : 
    __ models
    __ controller
    __ view
    __ css

## 4208 -- Côté Opérateur
* **Base de données :** Conception de la structure globale du fichier `base.sql` et insertion du jeu de données initial (barèmes de test)
* **Gestion des configurations :** Création de l'interface d'administration pour la configuration des préfixes valides (033, 037, etc.)
* **Gestion des frais :** Développement du module de modification dynamique des barèmes de frais par tranche de montant
* **Suivi financier :** Implémentation du calcul et de l'affichage de la situation des gains perçus via les frais
* **Supervision :** Création de la vue d'ensemble listant la situation des comptes de tous les clients
* **UI/UX Admin :** Design et intégration du tableau de bord opérateur avec Bootstrap