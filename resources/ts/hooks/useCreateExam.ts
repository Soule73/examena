import { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { QuestionFormData } from '@/types';

interface ExamCreateData {
    title: string;
    description: string;
    duration: number;
    start_time: string;
    end_time: string;
    is_active: boolean;
    questions: QuestionFormData[];
}

export const useCreateExam = () => {
    const [questions, setQuestions] = useState<QuestionFormData[]>([]);

    const { data, setData, post, processing, errors, reset } = useForm<ExamCreateData>({
        title: '',
        description: '',
        duration: 60,
        start_time: '',
        end_time: '',
        is_active: true,
        questions: []
    });

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

    const handleFieldChange = (field: string, value: any) => {
        setData(field as keyof ExamCreateData, value);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Validation côté client
        const validationErrors: any = {};

        // Validation du titre
        if (!data.title.trim()) {
            validationErrors.title = 'Le titre est requis';
        }

        // Validation de la durée
        if (!data.duration || data.duration < 1) {
            validationErrors.duration = 'La durée doit être d\'au moins 1 minute';
        }

        // Validation des questions
        if (questions.length === 0) {
            alert('Vous devez ajouter au moins une question');
            return;
        }

        for (let i = 0; i < questions.length; i++) {
            const question = questions[i];
            if (!question.content.trim()) {
                alert(`La question ${i + 1} doit avoir un contenu`);
                return;
            }
        }

        // Si des erreurs, ne pas soumettre
        if (Object.keys(validationErrors).length > 0) {
            console.error('Erreurs de validation:', validationErrors);
            return;
        }

        setData('questions', questions);

        post(route('teacher.exams.store'), {
            onSuccess: () => {
                reset();
                setQuestions([]);
            }
        });
    };

    return {
        data,
        errors,
        processing,
        questions,
        handleQuestionsChange,
        handleFieldChange,
        handleSubmit,
        reset
    };
};