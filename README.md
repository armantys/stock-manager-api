# Stock Manager API

API de gestion de stock avec Symfony 7 + API Platform.

Le but : suivre les entrées/sorties de stock par produit et par entrepôt, sans jamais permettre de modifier le stock directement (tout passe par des mouvements).

## Stack

- Symfony 7
- API Platform
- Doctrine ORM
- MySQL

## Fonctionnalités

- CRUD produits / entrepôts
- Mouvements de stock (IN / OUT)
- Le stock est recalculé automatiquement à chaque mouvement
- Vérif que tu ne peux pas sortir plus de stock que ce qui est dispo

## Installation

```bash
composer install
cp .env .env.local
```

Modifie le `DATABASE_URL` dans `.env.local` avec tes infos :

```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/stock_manager?serverVersion=8.0&charset=utf8mb4"
```

Puis :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

L'API tourne sur `https://localhost:8000/api`. La doc Swagger est dispo sur `/api/docs`.

## Endpoints

| Méthode | Route | Description |

| GET | `/api/products` | liste des produits |
| POST | `/api/products` | créer un produit |
| GET | `/api/warehouses` | liste des entrepôts |
| POST | `/api/stock_movements` | créer un mouvement |
| GET | `/api/stock_movements/{id}` | voir un mouvement |

Exemple de création d'un mouvement :

```bash
curl -X POST https://localhost:8000/api/stock_movements \
  -H "Content-Type: application/ld+json" \
  -d '{
    "product": "/api/products/1",
    "warehouse": "/api/warehouses/1",
    "quantity": 10,
    "type": "IN"
  }'
```

Si tu fais un `OUT` plus grand que le stock dispo, l'API renvoie une erreur.

## Comment ça marche

Le stock n'est jamais touché directement. Toute la logique passe par `StockMovementProcessor`, qui appelle `StockService` pour calculer et mettre à jour le `Stock` correspondant (produit + entrepôt). Ça permet de garder un historique de chaque mouvement et d'éviter les incohérences.