Projet réalisé dans le cadre de cours de programmation avancé BUT3 RA.

# Installation

- `docker compose up -d`
- `docker exec -it symfony_php sh`
- `composer install` ('S'il y a une erreur à cette étape, supprimer le dossier `var/cache`)
- `php bin/console doctrine:schema:create`
- `php bin/console doctrine:migrations:migrate`
- `php bin/console doctrine:fixtures:load` (S'il y a une erreur à cette étape là, c'est probablement du a la ram, allez dans `src/DataFixtures/AppFixtures.php` et augmenter la ram maximum alloué)

## Travail réalisé (par rapport au sujet)

- 1 : fait
- 2 : fait
- 3 : fait
- 4 : fait
- 5 : fait
- 6 : Addition, suppresion faite, pas de modification
- 7 : fait
- 8 : panier + achat fait (avec gestion des stocks)
- 9 (sujet 2) : update stock en direct, update panier presque en direct

Compte de connexion :
compte user
- user1@example.com
- password1

compte admin
- user2@example.com
- password2

Petite note :
J'avais ajouté des règles a la bdd pour gérer en stock/plus de stock, mais je n'ai pas trouvé comment ajouter de règles a la bdd via les fixtures/en l'automatisant.

## Stack technique

- PHP 8.3
- Symfony 7.1
- MySQL
- Docker
- Bootstrap (déjà intégré dans le projet)

