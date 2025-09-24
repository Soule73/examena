import { useState, useCallback } from 'react';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';

interface UseExamSecurityViolationOptions {
    examId: number;
    onViolation?: (type: string) => void;
}

export function useExamSecurityViolation({ examId, onViolation }: UseExamSecurityViolationOptions) {
    const [examTerminated, setExamTerminated] = useState<boolean>(false);
    const [terminationReason, setTerminationReason] = useState<string>('');

    const securityViolationForm = useForm({
        violation_type: '',
        violation_details: '',
        answers: {} as Record<number, string | number | number[]>
    });

    // Fonction pour terminer immédiatement l'examen suite à une violation
    const terminateExamForViolation = useCallback((violationType: string, answers: Record<number, string | number | number[]>) => {
        const reasons = {
            'tab_switch': 'Examen terminé : Changement d\'onglet détecté',
            'fullscreen_exit': 'Examen terminé : Sortie du mode plein écran détectée',
            'multiple_violations': 'Examen terminé : Violations multiples de sécurité détectées'
        };

        const reason = reasons[violationType as keyof typeof reasons] || 'Examen terminé : Violation de sécurité détectée';

        setTerminationReason(reason);
        setExamTerminated(true);

        // Préparer les données pour la soumission avec violation
        securityViolationForm.setData({
            violation_type: violationType,
            violation_details: reason,
            answers: answers
        });

        // Envoyer directement à la route de violation de sécurité
        securityViolationForm.post(route('student.exams.security-violation', examId), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Violation enregistrée avec succès
                // L'état examTerminated reste true pour afficher SecurityViolationPage
            },
            onError: () => {
                // Même en cas d'erreur, marquer comme terminé pour sécurité
                setExamTerminated(true);
                // On pourrait logger l'erreur ici si nécessaire
            }
        });

        // Appeler le callback si fourni
        if (onViolation) {
            onViolation(violationType);
        }
    }, [examId, securityViolationForm, onViolation]);

    // Handler pour les violations critiques
    const handleViolation = useCallback((type: string, answers: Record<number, string | number | number[]>) => {
        // Seules les violations critiques terminent l'examen
        if (type === 'tab_switch' || type === 'fullscreen_exit') {
            terminateExamForViolation(type, answers);
        }
    }, [terminateExamForViolation]);

    const handleBlocked = useCallback((answers: Record<number, string | number | number[]>) => {
        // En cas de blocage (violations répétées), terminer immédiatement
        terminateExamForViolation('multiple_violations', answers);
    }, [terminateExamForViolation]);

    return {
        examTerminated,
        terminationReason,
        handleViolation,
        handleBlocked,
        terminateExamForViolation
    };
}