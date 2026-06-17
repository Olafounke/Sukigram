# 05. Rapport de Projet : Dossier de Synthèse et Conclusion générale

---

## 1. Synthèse du Projet ChatApp

Le projet **ChatApp** représente l'aboutissement d'un cycle de formation axé sur les architectures web modernes et l'intelligence artificielle. Plus qu'une simple interface de chat, il s'agit d'un écosystème complet intégrant des enjeux de sécurité, de performance et d'utilisabilité. Ce document final opère une synthèse transversale des compétences acquises et des résultats obtenus.

---

## 2. Gestion de Projet et Méthodologie Agile

Le cycle de développement s'est appuyé sur une méthodologie **Agile (Scrum Lite)**, garantissant une adaptation constante aux contraintes techniques et aux feedbacks.

### 2.1 Découpage en Sprints
Le projet a été divisé en trois "Sprints" de deux semaines chacun :
1.  **Sprint 1 : Fondations** : Architecture Docker, Schéma de DB, Authentification Google.
2.  **Sprint 2 : Core AI** : Intégration de Groq, Services de chat, Système de prompt asynchrone.
3.  **Sprint 3 : UX & Qualité** : Optimisation frontend, Tests unitaires (C2.3), Documentation finale.

### 2.2 Transparence et Supervision (Daily Stand-ups)
Des points réguliers ont été organisés pour identifier les "bloquants". Par exemple, le choix initial de SQLite a été remis en question lors d'un Stand-up au profit de PostgreSQL pour assurer une meilleure gestion des types avec Prisma.

---

## 3. Gestion des Risques et Plan de Contingence

Un projet informatique est par définition une gestion permanente de l'incertitude.

### 3.1 Registre des Risques Applicatifs

| Risque | Description | Impact | Mitigation |
|--------|-------------|--------|------------|
| **Technique** | Obsolescence d'une dépendance critique (ex: Next.js alpha). | Élevé | Utilisation de versions LTS et `package-lock` strict. |
| **Opérationnel** | Dépassement des quotas d'API Groq. | Critique | Implémentation d'un système de limitation (Rate Limit) proactif. |
| **Sécurité** | Fuite de données via l'historique utilisateur. | Critique | Chiffrement RSA des messages et anonymisation des logs. |

---

## 4. Analyse de l'Utilisabilité (UX)

L'interface de ChatApp a fait l'objet d'une attention particulière pour minimiser la charge cognitive de l'utilisateur.

### 4.1 Principes de Design retenus
*   **Minimalisme** : Interface épurée privilégiant le texte.
*   **Réactivité** : Feedback immédiat via des indicateurs de chargement lors de la génération IA.
*   **Accessibilité** : Respect des standards WCAG (contraste, lecteur d'écran).

---

## 5. Bilan des Compétences Validées

Ce projet a servi de laboratoire pour la validation de quatre compétences majeures du référentiel :

1.  **Compétence C1.3 (Conception)** : Capacité à transformer une vision métier en une architecture informatique documentée (C4/UML).
2.  **Compétence C2.1 (Architecture Logicielle)** : Maîtrise des patterns en couches et des stratégies de scalabilité.
3.  **Compétence C2.2 (Développement)** : Production d'un code "Clean" et respectueux des principes SOLID.
4.  **Compétence C2.3 (Tests)** : Mise en place d'une culture de la qualité par les tests automatisés.

---

## 6. Roadmap Technologique : ChatApp v2.0

Le développement ne s'arrête pas à la version actuelle. Plusieurs axes d'évolution ont été identifiés :

### Axe 1 : RAG (Retrieval Augmented Generation)
Intégrer une base de données de vecteurs (ex: Pinecone) pour permettre à l'utilisateur de "discuter" avec ses propres documents PDF ou rapports de cours.

### Axe 2 : Multi-Modèles
Permettre de basculer dynamiquement entre Groq (vitesse), GPT-4o (logique complexe) et Llama 3 (coût nul en local).

### Axe 3 : Mobile Native
Développer une application compagnon via React Native partageant le même backend pour une expérience multi-dispositif homogène.

---

## 7. Conclusion Finale

Le projet ChatApp est une démonstration concrète de la puissance des outils de développement modernes. En combinant la souplesse de Next.js, la fiabilité de PostgreSQL et l'intelligence de Groq, nous avons construit une solution de rang professionnel. Au-delà des lignes de code, c'est la rigueur de la conception et la discipline des tests qui garantissent la valeur de ce travail.

---

## ANNEXES TECHNIQUES (Compilées)

### Annexe I : Schéma de Base de Données (Prisma Schema)
```prisma
model Message {
  id        Int      @id @default(autoincrement())
  role      String
  content   String
  createdAt DateTime @default(now())
  user      User?    @relation(fields: [userId], references: [id])
}
```

### Annexe J : Liste des Dépendances (package.json)
*   `next` : 16.x
*   `@prisma/client` : 5.x
*   `openai` (SDK compatible Groq) : 4.x
*   `jsonwebtoken` : 9.x

---
**Rapport clôturé le 17 Juin 2026 par Cécile/Victor.**
