@props([
    'questions' => [],
    'exam' => null,
])

@php
    // Si un examen est fourni, utiliser ses questions
    $initialQuestions = $exam
        ? $exam->questions
            ->map(function ($question) {
                $questionData = [
                    'id' => $question->id,
                    'content' => $question->content,
                    'type' => $question->type,
                    'points' => $question->points,
                ];

                if ($question->type === 'multiple_choice') {
                    $questionData['choices'] = $question->choices
                        ->map(function ($choice) {
                            return [
                                'id' => $choice->id,
                                'content' => $choice->content,
                                'is_correct' => (bool) $choice->is_correct,
                            ];
                        })
                        ->toArray();
                } elseif ($question->type === 'true_false') {
                    $questionData['correct_answer'] = $question->correct_answer;
                } elseif ($question->type === 'text') {
                    $questionData['suggested_answer'] = $question->suggested_answer ?? '';
                }

                return $questionData;
            })
            ->toArray()
        : $questions;
@endphp

<div x-data="advancedQuestionsManager(@js($initialQuestions))" class="space-y-6">
    <!-- En-tête avec bouton d'ajout -->
    <div class="flex justify-end">
        <div x-data="{ open: false }" class="relative">
            <button type="button" @click="open = !open"
                class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Ajouter une question
                <svg class="-mr-1 ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" @click.away="open = false" x-transition
                class="absolute right-0 mt-2 w-64 rounded-xl shadow-lg bg-white ring-1 ring-gray-100 z-10">
                <div class="py-2">
                    <button type="button" @click="addQuestion('multiple_choice'); open = false"
                        class="group flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 w-full text-left transition-colors">
                        <div
                            class="mr-3 h-8 w-8 flex items-center justify-center rounded-lg bg-blue-50 group-hover:bg-blue-100">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Choix multiples</div>
                            <div class="text-xs text-gray-500">Question avec plusieurs options</div>
                        </div>
                    </button>
                    <button type="button" @click="addQuestion('true_false'); open = false"
                        class="group flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 w-full text-left transition-colors">
                        <div
                            class="mr-3 h-8 w-8 flex items-center justify-center rounded-lg bg-green-50 group-hover:bg-green-100">
                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Vrai/Faux</div>
                            <div class="text-xs text-gray-500">Question binaire</div>
                        </div>
                    </button>
                    <button type="button" @click="addQuestion('text'); open = false"
                        class="group flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 w-full text-left transition-colors">
                        <div
                            class="mr-3 h-8 w-8 flex items-center justify-center rounded-lg bg-purple-50 group-hover:bg-purple-100">
                            <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Réponse libre</div>
                            <div class="text-xs text-gray-500">Question ouverte</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Message si aucune question -->
    <div x-show="questions.length === 0"
        class="text-center py-16 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            </path>
        </svg>
        <h3 class="text-sm font-medium text-gray-900 mb-2">Aucune question ajoutée</h3>
        <p class="text-sm text-gray-500">Commencez par ajouter votre première question pour créer l'examen</p>
    </div>

    <!-- Liste des questions -->
    <div class="space-y-4">
        <template x-for="(question, index) in questions" :key="question.id">
            <div x-data="{ collapsed: false }" class="border border-gray-200 rounded-xl bg-white overflow-hidden">
                <!-- En-tête de la question -->
                <div class="px-6 py-4 bg-gray-50 cursor-pointer" @click="collapsed = !collapsed">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="h-4 w-4 text-gray-500 transition-transform" :class="{ 'rotate-90': !collapsed }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                            <h4 class="text-sm font-semibold text-gray-900" x-text="`Question ${index + 1}`"></h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="{
                                    'bg-blue-100 text-blue-700': question.type === 'multiple_choice',
                                    'bg-green-100 text-green-700': question.type === 'true_false',
                                    'bg-purple-100 text-purple-700': question.type === 'text'
                                }"
                                x-text="getQuestionTypeLabel(question.type)">
                            </span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-xs text-gray-500" x-text="`${question.points} pts`"></span>
                            <button type="button" @click.stop="removeQuestion(index)"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-700 focus:outline-none transition-colors">
                                <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>

                    <!-- Aperçu du contenu quand collapsé -->
                    <div x-show="collapsed" class="mt-2">
                        <p class="text-sm text-gray-600 truncate" x-text="question.content || 'Aucun énoncé défini'">
                        </p>
                    </div>
                </div>

                <!-- Contenu de la question -->
                <div x-show="!collapsed" x-transition class="px-6 py-5 space-y-5 border-t border-gray-100">
                    <!-- Type et points (hidden inputs) -->
                    <input type="hidden" x-bind:name="`questions[${index}][type]`" x-model="question.type">
                    <!-- ID de la question pour les mises à jour -->
                    <input type="hidden" x-bind:name="`questions[${index}][id]`" x-model="question.id"
                        x-show="question.id">

                    <!-- Points -->
                    <div class="flex items-center space-x-4">
                        <label class="text-xs font-medium text-gray-700 uppercase tracking-wide w-16">Points</label>
                        <input x-bind:name="`questions[${index}][points]`" type="number" x-model="question.points"
                            min="0.5" step="0.5" required
                            class="w-20 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Énoncé de la question -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2 uppercase tracking-wide">
                            Énoncé de la question
                            <span class="normal-case text-xs text-gray-500 font-normal">(Markdown supporté)</span>
                        </label>
                        <div x-data="markdownEditor(question, index)" x-init="initEditor()" @destroy="destroy()">
                            <textarea x-ref="editor" x-bind:name="`questions[${index}][content]`" x-model="question.content"
                                placeholder="Saisissez votre question ici... 

Vous pouvez utiliser la syntaxe Markdown :
**gras**, *italique*, `code`, [lien](url)

# Titre 1
## Titre 2
- Liste à puces
1. Liste numérotée

> Citation"
                                required
                                class="block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                                rows="8"></textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Utilisez la barre d'outils ci-dessus pour formater votre texte ou tapez directement en
                            Markdown.
                        </p>
                    </div>

                    <!-- Options pour choix multiple -->
                    <div x-show="question.type === 'multiple_choice'" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Options de réponse
                            </label>
                            <button type="button" @click="addChoice(index)"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-700 focus:outline-none transition-colors">
                                <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Ajouter une option
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(choice, choiceIndex) in question.choices" :key="choice.id">
                                <div
                                    class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <!-- ID du choix pour les mises à jour -->
                                    <input type="hidden"
                                        x-bind:name="`questions[${index}][choices][${choiceIndex}][id]`"
                                        x-model="choice.id" x-show="choice.id">
                                    <input type="checkbox"
                                        x-bind:name="`questions[${index}][choices][${choiceIndex}][is_correct]`"
                                        x-model="choice.is_correct" value="1"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <input x-bind:name="`questions[${index}][choices][${choiceIndex}][content]`"
                                        type="text" x-model="choice.content"
                                        x-bind:placeholder="`Option ${String.fromCharCode(65 + choiceIndex)}`" required
                                        class="flex-1 px-3 py-2 border border-gray-200 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <!-- Hidden input pour les choix non cochés -->
                                    <input type="hidden"
                                        x-bind:name="`questions[${index}][choices][${choiceIndex}][is_correct]`"
                                        x-bind:value="choice.is_correct ? 1 : 0">
                                    <button type="button" @click="removeChoice(index, choiceIndex)"
                                        x-show="question.choices.length > 2"
                                        class="inline-flex items-center p-1 text-red-600 hover:text-red-700 focus:outline-none transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Réponse pour vrai/faux -->
                    <div x-show="question.type === 'true_false'" class="space-y-3">
                        <label class="block text-xs font-medium text-gray-700 mb-2 uppercase tracking-wide">
                            Réponse correcte
                        </label>
                        <div class="flex space-x-4">
                            <label
                                class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                                <input type="radio" x-bind:name="`questions[${index}][correct_answer]`"
                                    value="true" x-model="question.correct_answer"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-900">Vrai</span>
                            </label>
                            <label
                                class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                                <input type="radio" x-bind:name="`questions[${index}][correct_answer]`"
                                    value="false" x-model="question.correct_answer"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-900">Faux</span>
                            </label>
                        </div>
                    </div>

                    <!-- Réponse suggérée pour questions textuelles -->
                    <div x-show="question.type === 'text'" class="space-y-3">
                        <label class="block text-xs font-medium text-gray-700 mb-2 uppercase tracking-wide">
                            Réponse suggérée (optionnelle)
                        </label>
                        <textarea x-bind:name="`questions[${index}][suggested_answer]`" x-model="question.suggested_answer" rows="2"
                            placeholder="Éléments de réponse attendus ou critères d'évaluation..."
                            class="block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function advancedQuestionsManager(initialQuestions = []) {
        return {
            questions: initialQuestions.length > 0 ? initialQuestions : [],
            questionId: initialQuestions.length > 0 ? Math.max(...initialQuestions.map(q => q.id || 0)) + 1 : 0,
            choiceId: 0,

            init() {
                // Calculer le choiceId maximum pour éviter les conflits
                if (this.questions.length > 0) {
                    let maxChoiceId = 0;
                    this.questions.forEach(question => {
                        if (question.choices) {
                            question.choices.forEach(choice => {
                                if (choice.id && choice.id > maxChoiceId) {
                                    maxChoiceId = choice.id;
                                }
                            });
                        }
                    });
                    this.choiceId = maxChoiceId + 1;
                }
            },

            addQuestion(type = 'multiple_choice') {
                const question = {
                    id: this.questionId++,
                    content: '',
                    type: type,
                    points: 1
                };

                if (type === 'multiple_choice') {
                    question.choices = [{
                            id: this.choiceId++,
                            content: '',
                            is_correct: false
                        },
                        {
                            id: this.choiceId++,
                            content: '',
                            is_correct: false
                        }
                    ];
                } else if (type === 'true_false') {
                    question.correct_answer = 'true';
                } else if (type === 'text') {
                    question.suggested_answer = '';
                }

                this.questions.push(question);
            },

            removeQuestion(index) {
                this.questions.splice(index, 1);
            },

            addChoice(questionIndex) {
                this.questions[questionIndex].choices.push({
                    id: this.choiceId++,
                    content: '',
                    is_correct: false
                });
            },

            removeChoice(questionIndex, choiceIndex) {
                if (this.questions[questionIndex].choices.length <= 2) {
                    return;
                }

                this.questions[questionIndex].choices.splice(choiceIndex, 1);
            },

            getQuestionTypeLabel(type) {
                const labels = {
                    'multiple_choice': 'Choix multiples',
                    'true_false': 'Vrai/Faux',
                    'text': 'Réponse libre'
                };
                return labels[type] || type;
            }
        }
    }

    // Fonction pour l'éditeur Markdown
    function markdownEditor(question, index) {
        return {
            editor: null,
            isInitialized: false,

            initEditor() {
                // Attendre que le DOM soit prêt et que EasyMDE soit chargé
                this.$nextTick(() => {
                    // Vérifier si l'éditeur n'est pas déjà initialisé et si EasyMDE est disponible
                    if (!this.isInitialized && window.EasyMDE && this.$refs.editor) {
                        try {
                            this.editor = new EasyMDE({
                                element: this.$refs.editor,
                                placeholder: "Saisissez votre question ici... Vous pouvez utiliser la syntaxe Markdown pour la mise en forme.",
                                spellChecker: false,
                                autofocus: false,
                                status: false,
                                hideIcons: ['guide'],
                                toolbar: [
                                    "bold", "italic", "heading-2", "heading-3", "|",
                                    "quote", "unordered-list", "ordered-list", "|",
                                    "link", "|",
                                    "preview", "side-by-side", "|",
                                    {
                                        name: "guide",
                                        action: "https://www.markdownguide.org/basic-syntax/",
                                        className: "fa fa-question-circle",
                                        title: "Guide Markdown",
                                        default: true
                                    }
                                ],
                                shortcuts: {
                                    "toggleBold": "Ctrl-B",
                                    "toggleItalic": "Ctrl-I",
                                    "togglePreview": "Ctrl-P"
                                },
                                initialValue: question.content || '',
                                renderingConfig: {
                                    singleLineBreaks: false,
                                    codeSyntaxHighlighting: true,
                                }
                            });

                            // Synchroniser avec Alpine.js - écouter les changements
                            this.editor.codemirror.on('change', () => {
                                question.content = this.editor.value();
                            });

                            this.isInitialized = true;
                        } catch (error) {
                            console.warn('Impossible d\'initialiser l\'éditeur Markdown:', error);
                            // Fallback: laisser la textarea normale
                        }
                    }
                });
            },

            destroy() {
                if (this.editor && this.isInitialized) {
                    try {
                        this.editor.toTextArea();
                        this.editor = null;
                        this.isInitialized = false;
                    } catch (error) {
                        console.warn('Erreur lors de la destruction de l\'éditeur:', error);
                    }
                }
            }
        }
    }
</script>
