# 04. Rapport d'Intégration et Tests des Composants Logiciels (C2.3)

---

## 1. Introduction à la Stratégie Qualité

Le succès d'une application comme **ChatApp** ne réside pas seulement dans ses fonctionnalités, mais dans sa robustesse face aux erreurs et son intégrité au fil des évolutions. Ce document présente la stratégie de vérification et de validation (V&V) mise en place pour garantir la compétence C2.3.

---

## 2. La Pyramide des Tests : Un Modèle de Fiabilité

Plutôt que des tests exhaustifs manuels, nous avons adopté le modèle de la **Pyramide des Tests de Mike Cohn**.

### 2.1 Les Tests Unitaires (Base de la pyramide)
*   **Objectif** : Valider la logique métier la plus petite (fonctions individuelles).
*   **Quantité** : ~70% de la suite de tests.
*   **Exécution** : Immédiate (en millisecondes).
*   **Outil** : Node.js Native Test Runner.

### 2.2 Les Tests d'Intégration (Cœur)
*   **Objectif** : Vérifier que les composants (ChatService + Prisma + DB) travaillent correctement ensemble.
*   **Quantité** : ~20% de la suite de tests.
*   **Contrainte** : Nécessite une base de données de test (instanciée via Docker).

### 2.3 Les Tests End-to-End (E2E) (Sommet)
*   **Objectif** : Simuler un parcours utilisateur complet dans le navigateur.
*   **Quantité** : ~10% de la suite de tests.
*   **Outil** : Playwright / Cypress.

---

## 3. Stratégies de Bouchonnage (Mocking) et Isolation

Pour tester le `ChatService` sans dépendre de l'API Groq (coûteuse et lente) ou de la base de données réelle (état variable), des techniques de mocking sont utilisées.

### 3.1 Mocking de l'ORM Prisma
Nous utilisons des mocks d'objets pour simuler les réponses de la base de données, permettant de tester le traitement des données sans écriture réelle.

```javascript
const mockPrisma = {
  message: {
    create: async ({ data }) => ({ id: Math.random(), ...data }),
    findMany: async () => [{ id: 1, content: "Mock message" }]
  }
};
```

### 3.2 Mocking de l'API IA (Groq)
Il est crucial de tester comment l'application gère les réponses de l'IA, mais aussi ses erreurs (500, timeout). Nous simulons ces comportements pour vérifier la résilience du code de gestion d'erreurs.

---

## 4. Détail de la Campagne de Tests Unitaires

### 4.1 Test de création de message (Scénario Nominal)
Vérifie que pour une entrée valide, le service retourne un objet structuré avec les IDs et timestamps corrects.

### 4.2 Test de gestion de conversation inexistante (Cas Limite)
Vérifie que si un `conversationId` invalide est passé, le système lève une exception contrôlée au lieu de crasher le serveur.

---

## 5. Mesure et Analyse de la Couverture (Coverage)

La couverture de code n'est pas un but en soi, mais un excellent indicateur des "zones d'ombre".

### 5.1 Critères de Qualité (Quality Gates)
Le pipeline de déploiement est configuré pour échouer si les seuils suivants ne sont pas atteints :
*   **Lines Coverage** : > 80% sur les services.
*   **Functions Coverage** : > 75%.
*   **Branches Coverage** : > 70% (gestion de tous les `if/else`).

### 5.2 Exemples de Résultats de Couverture
| Fichier | Stmts | Branch | Funcs | Lines |
|---------|-------|--------|-------|-------|
| `backend/services/chatService.js` | 92.3% | 85.0% | 100% | 92.3% |
| `app/api/chat/route.js` | 78.5% | 66.6% | 80.0% | 78.5% |

---

## 6. Tests de Non-Régression Automatisés

À chaque nouvelle fonctionnalité (ex: génération de CV), l'ensemble de la suite de tests est rejoué. Cela garantit que la nouvelle brique n'a pas altéré la logique de chat standard.

---

## 7. Protocoles de Tests Manuels (UAT)

Certaines validations nécessitent l'œil humain (UX/UI). Un protocole de test manuel est suivi pour chaque version majeure :
1.  **Test d'Accessibilité** : Vérifier le contraste et la navigation clavier.
2.  **Test Responsive** : Validation sur iPhone (iOS/Safari) et Android (Chrome).
3.  **Test de Fluidité** : Vérification du "Lag" perçu lors de l'envoi de longs messages.

---

## Conclusion sur l'Assurance Qualité

La mise en œuvre d'une stratégie de tests rigoureuse (C2.3) transforme ChatApp d'un prototype en une application professionnelle. Le couplage entre tests unitaires, mocks et surveillance de la couverture assure que chaque ligne de code produite contribue à la valeur globale de la solution sans introduire de risques.

---
**Annexes Techniques :**
*   *Annexe G : Logs complets de la dernière campagne de tests.*
*   *Annexe H : Configuration de l'environnement de test (Docker).*
