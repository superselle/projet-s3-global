CONTEXTE :
Le projet algorithmique est TERMINÉ et FONCTIONNEL.
Il respecte le cours, les consignes de rendu et le rapport.
Cependant, le code est TROP LONG, TROP COMMENTÉ et TROP AVANCÉ
par rapport au niveau attendu d’un étudiant.

OBJECTIF :
Réduire DRASTIQUEMENT le nombre de lignes et la complexité apparente,
SANS changer le comportement du programme.

RÔLE :
Tu agis comme un étudiant sérieux qui simplifie son code
avant rendu pour éviter toute suspicion d’IA.

PRIORITÉ ABSOLUE :
La SIMPLICITÉ pédagogique prime sur la perfection technique.

---

### ÉTAPE 1 — NETTOYAGE DES COMMENTAIRES (MAJEUR)

Tu dois :
- Supprimer tous les commentaires inutiles ou évidents
- Supprimer TOUTES les références au cours (pages, sections, etc.)
- Supprimer les annotations inutiles comme :
  - @author
  - descriptions évidentes d’attributs ou méthodes

EXEMPLES À SUPPRIMER :
- Commentaires expliquant une variable dont le nom est clair
- Commentaires de plus de 2–3 lignes
- Commentaires “académiques” ou trop parfaits

EXEMPLES ACCEPTÉS :
- Un court commentaire expliquant une idée globale
- Un commentaire uniquement si la logique n’est pas évidente

---

### ÉTAPE 2 — SIMPLIFICATION DU CODE (CRITIQUE)

Tu dois remplacer TOUS les éléments suivants par des versions SIMPLES
et VUES EN COURS :

❌ INTERDITS (À SUPPRIMER OU REMPLACER) :
- Lambdas (`->`)
- Streams (`stream()`, `filter`, `map`, `count`, `min`, etc.)
- `computeIfAbsent`
- `Comparator.comparing*`
- Chaînage fluide moderne
- Toute API Java “élégante” non vue en cours

✅ À UTILISER À LA PLACE :
- Boucles `for` simples
- `if` / `else`
- Variables temporaires
- Comparaisons explicites
- Listes manipulées à la main

---

### ÉTAPE 3 — EXCEPTIONS (SIMPLIFIER FORTEMENT)

INTERDICTIONS :
- Créer des classes d’exception personnalisées
- Multiplier les messages complexes ou trop précis
- Gestion d’erreurs trop défensive

AUTORISÉ :
- `IllegalArgumentException`
- `RuntimeException`
- Messages simples et courts

EXEMPLE ATTENDU :

throw new IllegalArgumentException("Données invalides");



---

### ÉTAPE 4 — SUPPRESSION DES SÉCURITÉS INUTILES

Supprimer :
- Constructeurs privés “par sécurité” inutiles
- Vérifications impossibles dans le contexte du projet
- Tests artificiels (ex : groupes de 10 si non demandé)
- Cas ultra-limites non exigés par les consignes

PRINCIPE :
> On code pour ce qui est DEMANDÉ, pas pour tous les cas possibles.

---

### ÉTAPE 5 — RÉDUCTION DES LIGNES

Objectif :
- Réduire significativement le nombre total de lignes
- Regrouper le code quand c’est lisible
- Supprimer toute redondance

CONTRAINTE :
- Le comportement final DOIT rester identique
- Les résultats DOIVENT être les mêmes

---

### FORMAT DE TRAVAIL

Tu dois :
1. Simplifier fichier par fichier
2. Ne PAS réécrire inutilement ce qui est déjà simple
3. Ne PAS ajouter de nouvelles fonctionnalités
4. Ne PAS expliquer excessivement ce que tu fais

IMPORTANT :
Le résultat final doit ressembler à un code écrit
par un bon étudiant, PAS par un expert ni une IA.
