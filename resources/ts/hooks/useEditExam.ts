import { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { QuestionFormData, Exam } from '@/types';

interface ExamEditData {
    title: string;
    description: string;
    duration: number;
    start_time: string;
    end_time: string;
    is_active: boolean;
    questions: QuestionFormData[];
    deletedQuestionIds: number[];
    deletedChoiceIds: number[];
}

export const useEditExam = (exam: Exam, onClearHistory?: () => void) => {
    const [questions, setQuestions] = useState<QuestionFormData[]>([]);
    const [deletedQuestionIds, setDeletedQuestionIds] = useState<number[]>([]);
    const [deletedChoiceIds, setDeletedChoiceIds] = useState<number[]>([]);

    const { data, setData, put, processing, errors } = useForm<ExamEditData>({
        title: exam.title || '',
        description: exam.description || '',
        duration: exam.duration || 60,
        start_time: exam.start_time ? exam.start_time.slice(0, 16) : '',
        end_time: exam.end_time ? exam.end_time.slice(0, 16) : '',
        is_active: exam.is_active ?? true,
        questions: [],
        deletedQuestionIds: [],
        deletedChoiceIds: []
    });

    // Initialiser les questions depuis l'examen
    useEffect(() => {
        if (exam.questions) {
            const formattedQuestions: QuestionFormData[] = exam.questions.map((q, index) => ({
                id: q.id,
                content: q.content,
                type: q.type,
                points: q.points,
                order_index: q.order_index || index,
                choices: q.choices?.map((c, choiceIndex) => {
                    // Normaliser les valeurs pour les questions boolean
                    let content = c.content;
                    if (q.type === 'boolean') {
                        // Convertir les anciennes valeurs "Vrai"/"Faux" vers "true"/"false"
                        if (content === 'Vrai' || content === 'vrai') {
                            content = 'true';
                        } else if (content === 'Faux' || content === 'faux') {
                            content = 'false';
                        }
                        // S'assurer que nous avons soit 'true' soit 'false'
                        if (content !== 'true' && content !== 'false') {
                            content = choiceIndex === 0 ? 'true' : 'false';
                        }
                    }
                    return {
                        id: c.id,
                        content,
                        is_correct: c.is_correct,
                        order_index: c.order_index || choiceIndex
                    };
                }) || []
            }));
            setQuestions(formattedQuestions);
            setData('questions', formattedQuestions);
        }
    }, [exam]);

    // Log des erreurs pour débogage
    useEffect(() => {
        if (Object.keys(errors).length > 0) {
            console.log('Erreurs de validation détectées:', errors);
        }
    }, [errors]);

    const handleQuestionsChange = (newQuestions: QuestionFormData[]) => {
        setQuestions(newQuestions);
        setData('questions', newQuestions);
    };

    const handleQuestionDelete = (questionId: number) => {

        const newDeletedQuestionIds = [...deletedQuestionIds, questionId];
        setDeletedQuestionIds(newDeletedQuestionIds);
        setData('deletedQuestionIds', newDeletedQuestionIds);

        const filteredQuestions = questions.filter(q => q.id !== questionId);
        setQuestions(filteredQuestions);
        setData('questions', filteredQuestions);
    };

    const handleChoiceDelete = (choiceId: number, questionIndex: number) => {
        const newDeletedChoiceIds = [...deletedChoiceIds, choiceId];
        setDeletedChoiceIds(newDeletedChoiceIds);
        setData('deletedChoiceIds', newDeletedChoiceIds);

        const updatedQuestions = questions.map((q, i) => {
            if (i === questionIndex) {
                return {
                    ...q,
                    choices: q.choices.filter(c => c.id !== choiceId)
                };
            }
            return q;
        });
        setQuestions(updatedQuestions);
        setData('questions', updatedQuestions);
    };

    const handleFieldChange = (field: string, value: any) => {
        setData(field as keyof ExamEditData, value);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const validationErrors: any = {};

        if (!data.title.trim()) {
            validationErrors.title = 'Le titre est requis';
        }

        if (!data.duration || data.duration < 1) {
            validationErrors.duration = 'La durée doit être d\'au moins 1 minute';
        }

        const questionsToValidate = questions.length > 0 ? questions : data.questions;
        for (let i = 0; i < questionsToValidate.length; i++) {
            const question = questionsToValidate[i];
            if (!question.content.trim()) {
                validationErrors[`question_${i}_content`] = 'Le contenu de la question est requis';

                return;
            }
        }

        if (Object.keys(validationErrors).length > 0) {
            console.error('Erreurs de validation:', validationErrors);
            return;
        }

        setData('questions', questions);
        setData('deletedQuestionIds', deletedQuestionIds);
        setData('deletedChoiceIds', deletedChoiceIds);

        console.log('Données soumises:', { ...data, questions, deletedQuestionIds, deletedChoiceIds });

        put(route('teacher.exams.update', exam.id), {
            onSuccess: () => {
                if (onClearHistory) {
                    onClearHistory();
                }
            }
        });
    };

    return {
        data,
        errors,
        processing,
        questions,
        handleQuestionsChange,
        handleQuestionDelete,
        handleChoiceDelete,
        handleFieldChange,
        handleSubmit
    };
};