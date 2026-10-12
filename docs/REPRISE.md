# Reprise — MVP Forge JDR

## Objectif

Application web en ligne : compte, JDR personnalisé, fiche de personnage et partie regroupant plusieurs fiches et les informations de campagne.

## Parcours implémenté sur reprise

L'utilisateur crée son compte, définit un JDR avec un modèle de fiche (texte, nombre, case à cocher), crée une partie liée à ce JDR, ajoute plusieurs personnages, modifie leurs valeurs et conserve ses notes et comptes rendus de séances. L'atelier est l'accueil connecté. Les parties sont privées et gérées par leur créateur ; invitations et temps réel restent hors de ce lot.

Chaque fiche garde le modèle et sa version à la création. Les modifications ultérieures du modèle ne suppriment pas les anciennes valeurs. Les personnages historiques sont conservés ; leur import dans les nouvelles parties sera un lot ultérieur. Suppression des parties/fiches et édition des séances ne sont pas encore proposées.

## Socle remis en état

- Environnement Docker de développement, MariaDB 10.11 et ports alignés.
- Locks Composer et Symfony versionnés ; Symfony 7.4 et API Platform 4 choisis après résolution des dépendances. Faker remplacé par fakerphp/faker pour les fixtures de développement.
- Deux migrations : base historique vierge puis ajout des parties/fiches et des modèles. Une base existante nécessite une comparaison du schéma et une sauvegarde/restauration avant baseline.
- Démarrage de production sans fixtures, migrations, modifications de schéma, installation Composer ou remplacement des clés.
- Inscription validée et champs de compte bornés ; changement de mot de passe contrôlé.
- Renouvellement de session corrigé et gardes de navigation réactives.
- Écritures historiques bornées pour empêcher les transferts de propriété ; publication directe par l'auteur interdite.
- CORS limité à l'origine configurée, routage Apache et header Authorization corrigés.
- CI front/back avec tests bloquants et construction ; ancien déploiement automatique incomplet remplacé par les vérifications.

## Validation locale du 12 octobre 2026

- PHPUnit : 12 tests et 86 assertions, dont le parcours complet, les types, les droits entre deux comptes, la protection du compte et des endpoints historiques.
- Tests des sessions front : 11 réussis.
- Tests React : 6 réussis ; types des valeurs, champ numérique vide, erreurs de chargement/sauvegarde, conservation des notes non enregistrées lors de l'ajout d'une séance et redirection après expiration de session.
- Tests de démarrage : 3 réussis, incluant deux redémarrages et les erreurs de clés/cache.
- Builds React et image Nginx réussis ; validation Composer stricte, audit des dépendances PHP sans avis de sécurité retourné, lint du conteneur et des fichiers PHP réussis.
- Migrations appliquées sur MariaDB vierge ; Doctrine valide le schéma final.
- Parcours HTTP réel sous Apache/MariaDB : compte, login, JDR, partie, deux fiches, notes, séance, refresh et relecture. Vérification après redémarrage : données et session conservées.
- Image API de production construite et parcours HTTP testé localement en APP_ENV=prod/APP_DEBUG=0.

Ces vérifications n'établissent pas encore un test complet dans un navigateur réel ni une migration sur des données de production. Les anciens tests distants ne sont pas exécutés.

## Reste avant mise en ligne

1. Vérifier le parcours dans un navigateur réel, notamment mobile, et fixer les derniers détails d'usage.
2. Choisir/configurer le domaine et l'hébergement, fournir les accès, secrets et clés persistantes. Aucun compte d'hébergement n'est configuré dans l'environnement actuel.
3. Mettre en place HTTPS, stockage durable des fichiers, sauvegarde/restauration et suivi des erreurs.
4. Déployer en préproduction puis valider la version candidate avant livraison publique.

La branche reprise contient le chantier ; main reste inchangée. Aucun déploiement public effectué. Les instructions de lancement sont dans les README des deux dépôts ; le contrat API dans docs/MVP_API.md du back.
