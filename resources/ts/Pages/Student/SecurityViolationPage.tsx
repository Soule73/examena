import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Exam } from '@/types';
import { route } from 'ziggy-js';

interface Props {
    exam: Exam;
    reason: string;
    violationDetails?: string;
}

const SecurityViolationPage: React.FC<Props> = ({ exam, reason, violationDetails }) => {
    const getViolationMessage = (violationType: string) => {
        switch (violationType) {
            case 'tab_switch':
                return "Vous avez quitté l'onglet de l'examen";
            // case 'dev_tools':
            //     return "Vous avez tenté d'ouvrir les outils de développement";
            // case 'copy_paste':
            //     return "Vous avez tenté de copier ou coller du contenu";
            case 'multiple_violations':
                return "Violations de sécurité répétées détectées";
            case 'fullscreen_exit':
                return "Vous avez quitté le mode plein écran";
            default:
                return "Violation de sécurité détectée";
        }
    };

    return (
        <>
            <Head title="Violation de Sécurité" />

            <div className="min-h-screen bg-red-50 flex items-center justify-center px-4">
                <div className="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
                    <div className="mb-6">
                        <div className="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                            <svg className="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h1 className="text-2xl font-bold text-red-900 mb-2">
                            Examen Terminé
                        </h1>
                        <p className="text-red-700">
                            Violation de sécurité détectée
                        </p>
                    </div>

                    <div className="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <p className="text-red-800 font-medium mb-2">
                            {getViolationMessage(reason)}
                        </p>
                        {violationDetails && (
                            <p className="text-red-600 text-sm">
                                {violationDetails}
                            </p>
                        )}
                    </div>

                    <div className="space-y-4 text-left">
                        <div>
                            <h3 className="font-semibold text-gray-900 mb-2">Informations sur l'examen :</h3>
                            <div className="bg-gray-50 rounded p-3 text-sm">
                                <p><span className="font-medium">Examen :</span> {exam.title}</p>
                                <p><span className="font-medium">Statut :</span> Terminé automatiquement</p>
                                <p><span className="font-medium">Raison :</span> Violation de sécurité</p>
                            </div>
                        </div>

                        <div>
                            <h3 className="font-semibold text-gray-900 mb-2">Conséquences :</h3>
                            <ul className="bg-yellow-50 border border-yellow-200 rounded p-3 text-sm text-yellow-800 space-y-1">
                                <li>• Votre examen a été soumis automatiquement</li>
                                <li>• Vos réponses seront examinées par votre enseignant</li>
                                <li>• Cette violation sera notée dans votre dossier</li>
                                <li>• Le score final sera déterminé manuellement</li>
                            </ul>
                        </div>

                        <div>
                            <h3 className="font-semibold text-gray-900 mb-2">Prochaines étapes :</h3>
                            <div className="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800">
                                <p>Votre enseignant a été notifié de cette violation. Vous pourrez consulter vos résultats une fois que l'examen aura été corrigé.</p>
                            </div>
                        </div>
                    </div>

                    <div className="mt-8 pt-6 border-t border-gray-200">
                        <Link
                            href={route('student.exams.index')}
                            className="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors"
                        >
                            Retour à la liste des examens
                        </Link>
                    </div>
                </div>
            </div>
        </>
    );
};

export default SecurityViolationPage;