# Cahier des charges – Application de passage d’examen en ligne (local)

## Objectif
Développer une application web de passage d’examen en ligne fonctionnant en local (localhost) pour IRIS, établissements d’enseignement supérieur de niveau Bac+2 à Bac+5 (BTS, Licence Pro, Master 1/2).

## Technologies
- PHP avec framework Laravel (version simple, sans Breeze)
- MySQL pour la base de données
- HTML/CSS pour l’interface
- Fonctionnement en local (localhost)

## Fonctionnalités principales
1. **Authentification simple** (enseignant / étudiant)
2. **Gestion des examens** :
   - Création par les enseignants
   - Affectation à des étudiants
   - Choix de la durée, des questions, des types de réponses
3. **Passation d’examen par les étudiants** :
   - Interface dédiée avec minuterie
   - Enregistrement des réponses
   - Soumission finale
4. **Correction et résultats** :
   - Correction automatique (QCM)
   - Résultats consultables par l’étudiant
   - Tableau de bord enseignant avec statistiques
5. **Sécurité minimale** :
   - Contrôle des accès
   - Protection contre les triches simples
6. **Export / Import** :
   - Possibilité d’exporter les résultats en PDF ou CSV
   - Sauvegarde locale des données

## Livrables
- Projet Laravel complet lien github
- Cahier des charges en PDF et DOCX
- Fichier SQL pour créer la base de données
- Présentation PowerPoint (.pptx)

## Public cible
- Étudiants et enseignants de filières informatiques et développement logiciel

## Contraintes
- Pas de déploiement en ligne requis
- Interface simple et compréhensible
- Le projet doit pouvoir être lancé facilement sur une machine locale (avec XAMPP ou Laravel built-in server)

## Équipe
- 1 développeur Laravel
- 1 testeur / examinateur