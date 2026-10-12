# Contrat MVP

Préfixe `/api/mvp`, JSON et `Authorization: Bearer <token>`. Une ressource privée appartenant à un autre compte retourne 404. Les champs inconnus sont refusés dans les opérations MVP.

| Méthode | Chemin | Opération |
|---|---|---|
| GET/POST | /rpgs | Lister ses JDR / créer un JDR |
| PUT | /rpgs/{id} | Modifier nom, description et modèle |
| GET/POST | /campaigns | Lister ses parties / créer une partie |
| GET/PUT | /campaigns/{id} | Lire fiches et journal / modifier la campagne |
| POST | /campaigns/{id}/sheets | Créer une fiche depuis le modèle du JDR |
| PUT | /sheets/{id} | Modifier une fiche avec son modèle d'origine |
| POST | /campaigns/{id}/sessions | Ajouter une séance au journal |

JDR : `name`, `description`, `fields`. Chaque champ comporte une clé unique stable (`[a-z][a-z0-9_]{0,39}`), un libellé et un type `text`, `number` ou `boolean`. Maximum 40 champs. PUT exige `expectedTemplateVersion` ; une version périmée retourne 409. La version progresse à l'enregistrement Doctrine.

Partie : `name`, `description`, `rpgId` à la création et `notes` à la mise à jour. Le propriétaire ne peut pas associer le JDR d'un autre compte.

Fiche : `name`, `notes`, `values` indexées par les clés du modèle. Les nombres JSON doivent être numériques, les booléens stricts. Les valeurs absentes sont autorisées ; les clés inconnues sont refusées. La fiche copie les champs et la version à sa création : modifier le modèle du JDR ne change pas les anciennes fiches. Les personnages historiques restent conservés séparément ; leur import dans une partie sera un lot ultérieur.

Séance : `title`, `date` au format YYYY-MM-DD, `notes`. La date doit exister. Le journal est limité à 1000 séances par partie. L'édition/suppression de séances et de parties n'est pas encore proposée dans cette version.

Les notes de campagne et les fiches utilisent du texte simple ; le navigateur les affiche sans interpréter de HTML. Le contenu historique des wikis reste compatible avec le format Quill.
