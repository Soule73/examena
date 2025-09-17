@props([
    'questions' => [],
])

<div x-data="questionsManager(@js($questions))" class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Questions</h3>
        <x-button type="button" @click="addQuestion()" class="bg-indigo-600 hover:bg-indigo-700">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Ajouter une question
        </x-button>
    </div>

    <div x-show="questions.length === 0" class="text-center py-8 text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m-16-4c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252">
            </path>
        </svg>
        <p>Aucune question ajoutée. Cliquez sur "Ajouter une question" pour commencer.</p>
    </div>

    <div class="space-y-4">
        <template x-for="(question, index) in questions" :key="question.id">
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-sm font-medium text-gray-900" x-text="`Question ${index + 1}`"></h5>
                    <button type="button" @click="removeQuestion(index)" class="text-red-600 hover:text-red-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Texte de la question</label>
                        <textarea x-bind:name="`questions[${index}][content]`" x-model="question.content" rows="3"
                            placeholder="Entrez le texte de la question..." required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                        <input x-bind:name="`questions[${index}][points]`" type="number" x-model="question.points"
                            min="1" required
                            class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700">Choix de réponses</label>
                            <button type="button" @click="addChoice(index)"
                                class="text-sm text-indigo-600 hover:text-indigo-500">
                                + Ajouter un choix
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(choice, choiceIndex) in question.choices" :key="choice.id">
                                <div class="flex items-center space-x-2">
                                    <input type="radio" x-bind:name="`questions[${index}][correct_choice]`"
                                        x-bind:value="choiceIndex" x-model="question.correct_choice"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">

                                    <input x-bind:name="`questions[${index}][choices][${choiceIndex}]`" type="text"
                                        x-model="choice.content"
                                        x-bind:placeholder="`Choix ${String.fromCharCode(65 + choiceIndex)}`" required
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />

                                    <button type="button" @click="removeChoice(index, choiceIndex)"
                                        x-show="question.choices.length > 2" class="text-red-600 hover:text-red-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>

                                    <label class="text-sm text-gray-500">Correcte</label>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function questionsManager(initialQuestions = []) {
        return {
            questions: initialQuestions.length > 0 ? initialQuestions : [],
            questionId: initialQuestions.length,
            choiceId: 0,

            init() {
                if (this.questions.length === 0) {
                    this.addQuestion();
                }
            },

            addQuestion() {
                this.questions.push({
                    id: this.questionId++,
                    content: '',
                    points: 1,
                    choices: [{
                            id: this.choiceId++,
                            content: ''
                        },
                        {
                            id: this.choiceId++,
                            content: ''
                        }
                    ],
                    correct_choice: 0
                });
            },

            removeQuestion(index) {
                this.questions.splice(index, 1);
            },

            addChoice(questionIndex) {
                this.questions[questionIndex].choices.push({
                    id: this.choiceId++,
                    content: ''
                });
            },

            removeChoice(questionIndex, choiceIndex) {
                if (this.questions[questionIndex].choices.length <= 2) {
                    return;
                }

                this.questions[questionIndex].choices.splice(choiceIndex, 1);

                // Ajuster correct_choice si nécessaire
                if (this.questions[questionIndex].correct_choice >= choiceIndex) {
                    this.questions[questionIndex].correct_choice = Math.max(0, this.questions[questionIndex]
                        .correct_choice - 1);
                }
            }
        }
    }
</script>
