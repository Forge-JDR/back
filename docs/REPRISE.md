# Reprise — MVP Forge JDR

## Objectif accepté

Une application web en ligne permettant de créer un compte, un JDR personnalisé, des fiches de personnage et une partie regroupant plusieurs fiches et les informations de la campagne en cours.

## Hypothèses de la première version

Le créateur gère ses propres JDR, parties et fiches. Le partage avec des joueurs et le temps réel sont différés. Un modèle de fiche est associé au JDR ; ses champs personnalisables commencent par texte, nombre et case à cocher. Ces choix restent ajustables pendant la reprise.

## Modèle cible à implémenter

- JDR : propriétaire, nom, description/règles, modèle de fiche versionné.
- Modèle : champs identifiés par une clé stable, libellé, type et ordre.
- Partie : propriétaire, JDR associé, titre, état, description de campagne et notes de progression.
- Fiche : propriétaire, partie associée, nom, version du modèle et valeurs des champs. Une évolution du modèle doit conserver les anciennes valeurs.
- Séance : partie, date, titre et compte rendu.

Les noms techniques seront fixés après récupération du schéma Doctrine. Les données existantes doivent être migrées ; aucune suppression/recréation de base en production.

## Lots et critères de validation

1. **Socle et conservation des données** : installation reproductible, démarrage sans fixtures ni modifications automatiques du schéma, clés persistantes, comptes et sessions fiables, champs sensibles protégés. Un redémarrage conserve données et clés.
2. **Personnalisation** : création du JDR et du modèle, fiches avec valeurs enregistrées et relues. Tester une évolution du modèle et un contenu long.
3. **Parties/campagnes** : création d'une partie depuis un JDR, plusieurs fiches, notes et séances persistantes. Un autre compte ne peut ni lire ni modifier ces ressources privées.
4. **Mise en ligne** : tests bloquants, environnement de préproduction, stockage durable des images, sauvegarde/restauration, configuration sans secrets versionnés, déploiement et retour arrière documentés.

Publication publique/modération, recherche avancée, traduction complète, thème sombre, invitations et jeu en direct sont reportés hors MVP.

## Premier lot sur reprise

- Démarrage serveur sans fixtures, schema:update, création de base, installation Composer ni régénération des clés. Les clés doivent être provisionnées et lisibles avant démarrage ; les migrations deviennent une opération de maintenance explicite.
- PUT /api/me limité à username, pseudo, password et currentPassword ; validation et vérification de l'ancien mot de passe. Le front envoie currentPassword et affiche les rejets.
- Login et refresh utilisent des tokens distincts, propagent les erreurs, mutualisent les refresh concurrents et limitent les nouvelles tentatives. L'inscription appelle ensuite le login pour obtenir un refresh token.
- Intercepteurs installés une fois au chargement de l'application ; données utilisateur effacées lors de l'expiration ou déconnexion.

## Vérification effectuée le 12 octobre 2026

- Front : `node --test --test-isolation=none tests/reprise/auth.test.mjs` — 11 tests réussis sous Node 24. Ils exécutent les modules avec HTTP et stockage simulés ; ils ne remplacent pas le build React ou un test navigateur/API.
- Back : `python3 tests/reprise/startup_test.py` — 3 tests réussis ; commandes externes simulées, contrôle de deux redémarrages, clés absentes et échec du cache.
- Back : `sh -n launchApp.sh` — réussi.
- Tests PHP ajoutés : `php bin/phpunit tests/Unit/AccountUpdateTest.php` — NON exécutés ici (PHP et Composer absents).

## Blocages restant à lever

Le shell ne peut pas joindre son proxy réseau dans l'environnement actuel ; les fichiers ont été lus et publiés via le connecteur GitHub. Aucun build complet ni lancement Symfony/React effectué. Le Dockerfile dépend encore de .env.prod, migrations, composer.lock et symfony.lock absents du dépôt ; les ports Apache/Compose doivent être alignés. Le prochain lot doit rendre cette installation reproductible et exécuter les tests PHP et le parcours réel.

La limitation des champs d'inscription/JDR/personnages, les transitions de publication et les droits des sous-ressources restent à traiter. Le projet n'est pas encore prêt à être exposé en ligne. Aucun déploiement n'a été effectué et main n'a pas été modifiée.
