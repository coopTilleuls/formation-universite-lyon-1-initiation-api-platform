# formation-univeriste-lyon-1-initiation-api-platform
Repository temporaire pour exporter le code des exercices de la formation donnée à l'Université Lyon 1 (initiation). 

## Pré-requis

- PHP >= 8.2 avec [PDO sqlite configuré](https://www.php.net/manual/en/ref.pdo-sqlite.php)
- Symfony CLI
- Composer (ou composer via la symfony CLI)

## Installation

1. Installer les dépendances avec composer :
    ```bash
    composer install
    ```
2. Lancer le serveur local avec symfony :
    ```bash
    symfony serve
    ```

🎉 Votre serveur est disponible sur [http://127.0.0.1:8000](http://127.0.0.1:8000) par défaut,
ou tout autre port indiqué par la commande `symfony serve`.

**Il est recommandé de le lancer en HTTPS en installant un certificat SSL au préalable avec la commande `symfony server:ca:install`.**

## Exercice

Le Coffre à Idées de Cadeaux

1. Entité Doctrine : Gift (La donnée brute en base).

2. Opération de Création : Une ressource GiftCreate (POST).

   - Ne doit accepter que label et price.
   - Utilise un State Processor pour transformer ce DTO en entité Gift.

3. Opération de Consultation : Une ressource GiftRead (GET).

   - Ne doit exposer que id, label, et l'email de l'auteur (masqué partiellement).
   - Utilise un State Provider pour transformer l'entité en ce DTO.

4. Voter : Seul l'auteur peut consulter son idée de cadeau si elle est marquée comme "privée".

## Important

- Le code est simplifié pour la formation. Attention à ne pas utiliser ce code en production.
- Des tests sont manquants (nécessite d'autres connaissances).
- Le code est en anglais, la documentation et les commentaires dans le code en français.

## Resources complémentaires aux slides et au code

- [API Platform documentation](https://api-platform.com/docs)
- [Symfony documentation](https://symfony.com/doc/current/index.html)
- [Twig documentation](https://twig.symfony.com/)

Made by Vincent AMSTOUTZ pour L'Université Lyon 1.
