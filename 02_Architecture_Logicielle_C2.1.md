# 02. Rapport de Conception de l'Architecture Logicielle (C2.1)

---

## 1. Introduction à l'Architecture Applicative

L'architecture logicielle de **ChatApp** a été conçue pour maximiser trois piliers : la testabilité, la maintenabilité et la performance. Ce document détaille les patterns structurels et comportementaux mis en œuvre pour garantir une séparation stricte des préoccupations.

---

## 2. Analyse Comparative des Patterns

### 2.1 Pourquoi une Architecture en Couches ?
Nous avons opté pour une **Architecture Multicouches (N-Tier)** plutôt qu'une architecture monolithique plane.

| Architecture | Avantages | Inconvénients | État dans le projet |
|--------------|-----------|---------------|---------------------|
| **Monolithique Simple** | Rapide à coder, moins de fichiers. | Couplage fort, difficile à tester. | Rejeté |
| **Microservices** | Scalabilité extrême, indépendance. | Très complexe à orchestrer (réseau, latence). | Rejeté (Overkill) |
| **Architecture en Couches** | Isolation des responsabilités, Testabilité unitaire facilitée. | Un peu de boilerplate (mapping). | **Retenu** |

### 2.2 Vers l'Architecture Hexagonale (Clean Architecture)
Bien que l'implémentation actuelle soit une architecture en couches, elle tend vers l'hexagone. Le `ChatService` agit comme le **Cœur Métier**, tandis que Prisma et les API Routes agissent comme des **Adapteurs de Sortie et d'Entrée**.

---

## 3. Implémentation Détaillée des Patterns

### 3.1 Pattern Singleton (Prisma Client)
Pour éviter l'épuisement des connexions à la base de données, l'instance du client Prisma est gérée via un pattern Singleton.

```javascript
// backend/lib/prisma.js
import { PrismaClient } from '@prisma/client';

let prisma;

if (process.env.NODE_ENV === 'production') {
  prisma = new PrismaClient();
} else {
  if (!global.prisma) {
    global.prisma = new PrismaClient();
  }
  prisma = global.prisma;
}

export default prisma;
```

### 3.2 Service Layer Pattern
Toute la logique de préparation du prompt IA et de gestion de l'historique est centralisée dans `chatService.js`. Cela permet de :
1.  Réutiliser la logique de chat dans une API REST, mais potentiellement aussi dans une CLI ou un bot Telegram.
2.  Mocker facilement les appels à la base de données pour les tests unitaires.

---

## 4. Documentation des Interfaces (OpenAPI / Swagger Style)

L'interopérabilité entre le frontend et le backend est régie par une API RESTful documentée.

### 4.1 Endpoint : Envoi de message
*   **Action** : `POST /api/chat`
*   **Description** : Traite un nouveau message utilisateur, l'enregistre, interroge l'IA et retourne la réponse.

**Structure de la Requête (JSON) :**
```json
{
  "message": "string (obligatoire)",
  "username": "string (optionnel)",
  "conversationId": "number (optionnel)"
}
```

**Structure de la Réponse (200 OK) :**
```json
{
  "reply": "string (réponse assistant)",
  "conversationId": "number",
  "id": "number",
  "createdAt": "date ISO"
}
```

### 4.2 Endpoint : Historique des conversations
*   **Action** : `GET /api/conversations`
*   **Description** : Liste toutes les sessions de chat pour l'utilisateur authentifié.

---

## 5. Stratégies de Scalabilité et Performance

### 5.1 Scalabilité Horizontale (Stateless Content)
Le serveur Next.js ne stocke aucune information de session locale (Memory). Toute l'information est exportée soit dans des **JWT (JSON Web Tokens)** côté client, soit dans la base de données PostgreSQL. Cela permet de monter ou descendre le nombre d'instances de serveur dynamiquement (Auto-scaling) sans rupture de service pour l'utilisateur.

### 5.2 Optimisation des Requêtes (Batching & Indexing)
Pour maintenir des performances optimales avec des millions de messages :
1.  **Indexation de `userId` et `conversationId`** dans PostgreSQL pour accélérer les recherches.
2.  **Pagination (Infinite Scroll)** : Le backend ne renvoie que les 20 derniers messages par défaut pour économiser la bande passante et la mémoire.

### 5.3 Stratégie de Caching
L'utilisation de `Redis` (prévue en production) permet de mettre en cache les profils utilisateurs et les résultats des requêtes IA les plus fréquentes (ex: "Qu'est-ce que ChatApp ?") pour réduire les coûts d'API.

---

## 6. Monitoring et Résilience Logicielle

### 6.1 Logging Logstratifié
Toutes les interactions avec l'IA sont loguées de manière structurée (`level`, `timestamp`, `path`, `latency`). Cela permet, via une pile ELK ou Datadog, de surveiller en temps réel :
*   Le taux d'erreur de l'API Groq.
*   Le temps de réponse moyen (p95).
*   La consommation de tokens par utilisateur.

### 6.2 Résilience (Circuit Breaker)
En cas de défaillance majeure de l'API IA, le système bascule sur un mode dégradé affichant un message poli à l'utilisateur et enregistrant l'incident pour une analyse ultérieure.

---

## Conclusion Technique

L'architecture logicielle de ChatApp dépasse le cadre d'un simple projet de démonstration. En appliquant des patterns éprouvés comme le Singleton, le Service Layer et l'Architecture Layered, nous garantissons une base saine pour des évolutions futures vers des systèmes de type Agentic AI ou RAG complexe.

---
**Annexes Techniques :**
*   *Annexe C : Plan de tests de charge.*
*   *Annexe D : Rapport de performance Lighthouse.*
