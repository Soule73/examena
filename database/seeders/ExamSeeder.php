<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les enseignants
        $teachers = \App\Models\User::role('teacher')->get();
        
        if ($teachers->isEmpty()) {
            echo "Aucun enseignant trouvé. Veuillez d'abord créer des enseignants.\n";
            return;
        }

        // Créer des examens pour chaque enseignant
        foreach ($teachers as $teacher) {
            // Examen 1: Mathématiques
            $mathExam = \App\Models\Exam::create([
                'title' => 'Examen de Mathématiques - Algèbre',
                'description' => 'Test sur les équations du second degré et les fonctions',
                'teacher_id' => $teacher->id,
                'duration' => 60,
                'start_time' => now()->addDays(7),
                'end_time' => now()->addDays(10),
                'is_active' => true,
            ]);

            // Questions pour l'examen de maths (format Markdown)
            $mathQuestions = [
                [
                    'question' => '## Équation du Second Degré

Résolvez l\'équation quadratique suivante :

$$x^2 - 5x + 6 = 0$$

### Méthode suggérée :
Utiliser la factorisation ou la formule quadratique.

### Rappel :
Une équation de la forme $ax^2 + bx + c = 0$ peut être résolue par factorisation.

### Étapes de résolution :
1. Chercher deux nombres dont le produit est 6 et la somme est 5
2. Factoriser l\'équation
3. Résoudre chaque facteur',
                    'choices' => [
                        ['text' => 'x = 2 et x = 3', 'is_correct' => true],
                        ['text' => 'x = 1 et x = 6', 'is_correct' => false],
                        ['text' => 'x = -2 et x = -3', 'is_correct' => false],
                        ['text' => 'x = 0 et x = 5', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => '## Calcul de Dérivée

Calculez la dérivée de la fonction suivante :

$$f(x) = 3x^2 + 2x - 1$$

### Règles à appliquer :
- Dérivée de $x^n$ est $n \cdot x^{n-1}$
- Dérivée de $ax$ est $a$
- Dérivée d\'une constante est $0$

### Calcul étape par étape :
1. $\frac{d}{dx}(3x^2) = 3 \cdot 2x^{2-1} = 6x$
2. $\frac{d}{dx}(2x) = 2$
3. $\frac{d}{dx}(-1) = 0$

Donc : $f\'(x) = 6x + 2 + 0 = 6x + 2$',
                    'choices' => [
                        ['text' => 'f\'(x) = 6x + 2', 'is_correct' => true],
                        ['text' => 'f\'(x) = 3x + 2', 'is_correct' => false],
                        ['text' => 'f\'(x) = 6x - 1', 'is_correct' => false],
                        ['text' => 'f\'(x) = 3x² + 2', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => '## Racine Carrée

Calculez la valeur de la racine carrée suivante :

$$\sqrt{16}$$

### Définition :
La racine carrée d\'un nombre est la valeur qui, multipliée par elle-même, donne ce nombre.

### Exemple :
$\sqrt{9} = 3$ car $3 \times 3 = 9$

### Question :
Quelle est la valeur de $\sqrt{16}$ ?',
                    'choices' => [
                        ['text' => '4', 'is_correct' => true],
                        ['text' => '8', 'is_correct' => false],
                        ['text' => '2', 'is_correct' => false],
                        ['text' => '16', 'is_correct' => false],
                    ]
                ]
            ];

            foreach ($mathQuestions as $questionData) {
                $question = \App\Models\Question::create([
                    'exam_id' => $mathExam->id,
                    'content' => $questionData['question'],
                    'type' => 'multiple_choice',
                    'points' => 5,
                ]);

                foreach ($questionData['choices'] as $choiceData) {
                    \App\Models\Choice::create([
                        'question_id' => $question->id,
                        'content' => $choiceData['text'],
                        'is_correct' => $choiceData['is_correct'],
                    ]);
                }
            }

            // Examen 2: Informatique
            $csExam = \App\Models\Exam::create([
                'title' => 'Examen d\'Informatique - Programmation',
                'description' => 'Test sur les concepts de base de la programmation',
                'teacher_id' => $teacher->id,
                'duration' => 45,
                'start_time' => now()->addDays(5),
                'end_time' => now()->addDays(8),
                'is_active' => false,
            ]);

            // Questions pour l'examen d'informatique
            $csQuestions = [
                [
                    'question' => '# Concepts de Programmation

## Variables en Programmation

### Question :
Qu\'est-ce qu\'une variable en programmation ?

Une variable est un concept fondamental en programmation.

### Caractéristiques d\'une variable :
- Elle permet de stocker des données
- Elle peut contenir différents types de données
- On peut modifier son contenu
- Elle a un nom pour l\'identifier

### Remarque :
Les variables sont essentielles dans tous les langages de programmation.',
                    'choices' => [
                        ['text' => 'Un espace de stockage pour des données', 'is_correct' => true],
                        ['text' => 'Une fonction mathématique', 'is_correct' => false],
                        ['text' => 'Un type de boucle', 'is_correct' => false],
                        ['text' => 'Un algorithme de tri', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => '# Opérateurs en Programmation

## Opérateur Modulo

### Question :
Quel est le résultat de `5 % 3` en programmation ?

L\'opérateur modulo (%) retourne le reste de la division entière.

### Calcul étape par étape :
1. Division : $5 \div 3 = 1$ (quotient entier)
2. Multiplication : $1 \times 3 = 3$
3. Soustraction : $5 - 3 = 2$ (reste)

### Formule :
```
5 % 3 = 2
```

### Utilité :
L\'opérateur modulo est souvent utilisé pour vérifier si un nombre est pair/impair.',
                    'choices' => [
                        ['text' => '2', 'is_correct' => true],
                        ['text' => '1', 'is_correct' => false],
                        ['text' => '5', 'is_correct' => false],
                        ['text' => '3', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => '# Types de Données

## Types de Données Primitifs

### Question :
Lequel de ces éléments est un type de données primitif dans la plupart des langages de programmation ?

### Types de données courants :
- **Primitifs :** int, float, boolean, char
- **Composés :** array, object, struct
- **Abstraits :** list, queue, stack

### Note :
Les types primitifs sont les types de base fournis directement par le langage.',
                    'choices' => [
                        ['text' => 'Integer (int)', 'is_correct' => true],
                        ['text' => 'Array (tableau)', 'is_correct' => false],
                        ['text' => 'Object (objet)', 'is_correct' => false],
                        ['text' => 'Function (fonction)', 'is_correct' => false],
                    ]
                ]
            ];

            foreach ($csQuestions as $questionData) {
                $question = \App\Models\Question::create([
                    'exam_id' => $csExam->id,
                    'content' => $questionData['question'],
                    'type' => 'multiple_choice',
                    'points' => 3,
                ]);

                foreach ($questionData['choices'] as $choiceData) {
                    \App\Models\Choice::create([
                        'question_id' => $question->id,
                        'content' => $choiceData['text'],
                        'is_correct' => $choiceData['is_correct'],
                    ]);
                }
            }

            // Examen 3: Physique
            $physicsExam = \App\Models\Exam::create([
                'title' => 'Examen de Physique - Mécanique',
                'description' => 'Test sur les lois de Newton et la cinématique',
                'teacher_id' => $teacher->id,
                'duration' => 90,
                'start_time' => now()->addDays(12),
                'end_time' => now()->addDays(15),
                'is_active' => true,
            ]);

            // Questions pour l'examen de physique
            $physicsQuestions = [
                [
                    'question' => '# Lois de Newton

## Première Loi de Newton (Principe d\'Inertie)

### Énoncé de la question :
Un objet de masse m = 2 kg se déplace en ligne droite à vitesse constante v = 5 m/s sur une surface sans frottement.

### Situation :
```
Objet ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━►
       v = 5 m/s (constante)
```

### Question :
Quelle est la force résultante appliquée sur cet objet ?

### Rappels théoriques :
- **1ère loi de Newton :** Un objet au repos reste au repos, un objet en mouvement rectiligne uniforme continue son mouvement, si et seulement si la force résultante est nulle.
- **Force résultante :** $\sum F = ma$

### Astuce :
Si la vitesse est constante, quelle est l\'accélération ?',
                    'choices' => [
                        ['text' => 'F = 0 N', 'is_correct' => true],
                        ['text' => 'F = 10 N', 'is_correct' => false],
                        ['text' => 'F = mv = 2×5 = 10 N', 'is_correct' => false],
                        ['text' => 'F = mg = 2×9.8 = 19.6 N', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => '# Cinématique

## Mouvement Rectiligne Uniformément Accéléré (MRUA)

### Données du problème :
Une voiture accélère de façon constante :
- **Vitesse initiale :** $v_0 = 0$ m/s (départ à l\'arrêt)
- **Accélération :** $a = 2$ m/s²
- **Temps :** $t = 5$ s

### Schéma :
```
t=0s        t=1s        t=2s        t=3s        t=4s        t=5s
v=0 ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ v=?
     └─────────── accélération constante ────────────┘
```

### Formule à utiliser :
$$v = v_0 + at$$

### Question :
Quelle est la vitesse finale de la voiture après 5 secondes ?

| Temps (s) | Vitesse (m/s) |
|-----------|---------------|
| 0         | 0             |
| 1         | 2             |
| 2         | 4             |
| 3         | 6             |
| 4         | 8             |
| 5         | ?             |',
                    'choices' => [
                        ['text' => 'v = 10 m/s', 'is_correct' => true],
                        ['text' => 'v = 5 m/s', 'is_correct' => false],
                        ['text' => 'v = 25 m/s', 'is_correct' => false],
                        ['text' => 'v = 2 m/s', 'is_correct' => false],
                    ]
                ]
            ];

            foreach ($physicsQuestions as $questionData) {
                $question = \App\Models\Question::create([
                    'exam_id' => $physicsExam->id,
                    'content' => $questionData['question'],
                    'type' => 'multiple_choice',
                    'points' => 8,
                ]);

                foreach ($questionData['choices'] as $choiceData) {
                    \App\Models\Choice::create([
                        'question_id' => $question->id,
                        'content' => $choiceData['text'],
                        'is_correct' => $choiceData['is_correct'],
                    ]);
                }
            }
        }

        // Créer quelques réponses d'étudiants pour avoir des statistiques
        $students = \App\Models\User::role('student')->get();
        $exams = \App\Models\Exam::all();

        foreach ($students->take(2) as $student) {
            foreach ($exams->take(1) as $exam) {
                foreach ($exam->questions as $question) {
                    $choices = $question->choices;
                    if ($choices->count() > 0) {
                        // 80% de chance de donner la bonne réponse
                        $correctChoice = $choices->where('is_correct', true)->first();
                        $randomChoice = $choices->random();
                        
                        $selectedChoice = (rand(1, 10) <= 8 && $correctChoice) 
                            ? $correctChoice 
                            : $randomChoice;

                        \App\Models\Answer::create([
                            'user_id' => $student->id,
                            'question_id' => $question->id,
                            'answer_text' => $selectedChoice->content,
                        ]);
                    }
                }
            }
        }

        echo "Examens et questions créés avec succès !\n";
        echo "- " . \App\Models\Exam::count() . " examens créés\n";
        echo "- " . \App\Models\Question::count() . " questions créées\n";
        echo "- " . \App\Models\Choice::count() . " choix créés\n";
        echo "- " . \App\Models\Answer::count() . " réponses d'étudiants créées\n";
    }
}