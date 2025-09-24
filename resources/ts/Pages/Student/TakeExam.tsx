import { Head } from '@inertiajs/react';
import Modal from '@/Components/Modal';
import { Button } from '@/Components';
import TextEntry from '@/Components/TextEntry';
import { Answer, Exam, ExamAssignment, Question } from '@/types';
import { ArrowsPointingOutIcon, ExclamationCircleIcon, QuestionMarkCircleIcon } from '@heroicons/react/24/outline';
import TakeQuestion from '@/Components/exam/TakeQuestion';
import useTakeExam from '@/hooks/exam/useTakeExam';
import SecurityViolationPage, { CanNotTakeExam } from '@/Pages/Student/Exams/SecurityViolationPage';
import AlertEntry from '@/Components/AlertEntry';
import Section from '@/Components/Section';
import { formatTime } from '@/utils';
import ConfirmationModal from '@/Components/ConfirmationModal';

interface TakeExamProps {
    exam: Exam;
    assignment: ExamAssignment;
    questions: Question[];
    userAnswers: Answer[];
}

export default function TakeExam({ exam, assignment, questions = [], userAnswers = [] }: TakeExamProps) {

    const {
        answers,
        isSubmitting,
        showConfirmModal,
        setShowConfirmModal,
        timeLeft,
        security,
        processing,
        handleAnswerChange,
        handleSubmit,
        autoSave,
        examTerminated,
        terminationReason,
        showFullscreenModal,
        enterFullscreen,
        examCanStart
    } = useTakeExam({ exam, questions, userAnswers });

    if (examTerminated) {
        return (
            <SecurityViolationPage
                exam={exam}
                reason={terminationReason || "Violation de sécurité détectée"}
            />
        );
    }

    if (assignment.submitted_at) {
        return (
            <CanNotTakeExam
                title="Examen Terminé"
                message="Vous avez déjà terminé cet examen."
                icon={<ExclamationCircleIcon className="h-12 w-12 text-yellow-500 mx-auto mb-4" />}
            />
        );
    }

    if (
        !questions || questions.length === 0
    ) {
        return (
            <CanNotTakeExam
                title="Aucune question disponible"
                subtitle='Cet examen ne contient aucune question.'
                message="Veuillez contacter votre enseignant pour plus d'informations."
                icon={<ExclamationCircleIcon className="h-12 w-12 text-yellow-500 mx-auto mb-4" />}
            />
        );
    }


    return (
        <div className="bg-gray-50 min-h-screen">
            <Head title={`Examen - ${exam.title}`} />

            <div className="bg-white py-4 border-b border-gray-200 fixed w-full z-10 top-0">
                <div className="container mx-auto flex justify-between items-center">
                    <TextEntry
                        className=' text-start'
                        label={exam.title}
                        value={exam.description ? (exam.description.length > 100 ? exam.description.substring(0, 100) + '...' : exam.description) : ''}
                    />

                    <TextEntry
                        className=' text-center'
                        label="Temps restant"
                        value={formatTime(timeLeft)}
                    />

                    {!security.isFullscreen && <TextEntry
                        className=' text-center'
                        label={"Mode plein écran requis"}
                        value=""
                    />}
                    <Button
                        size="sm"
                        color="primary"

                        onClick={() => setShowConfirmModal(true)}
                        disabled={isSubmitting || processing}
                        loading={isSubmitting || processing}

                    >
                        {isSubmitting || processing ? 'Soumission...' : "Terminer l'examen"}
                    </Button>
                </div>
            </div>

            <div className="pt-20 max-w-6xl mx-auto">
                <div className="container mx-auto px-4 py-8">
                    <Section title="Instructions importantes" collapsible>
                        <AlertEntry type="warning" title="IMPORTANT">
                            <p>
                                Toute violation des règles de sécurité (changement d'onglet,
                                sortie du mode plein écran) terminera
                                <strong> IMMÉDIATEMENT</strong> votre examen.
                            </p>
                            <p>
                                Vos réponses seront automatiquement sauvegardées.
                                Aucun avertissement ne sera donné.
                            </p>
                        </AlertEntry>
                    </Section>

                    {examCanStart && questions.length > 0 && (
                        questions.map((currentQ) => (
                            <TakeQuestion
                                key={currentQ.id}
                                question={currentQ}
                                answers={answers}
                                onAnswerChange={handleAnswerChange}
                            />
                        ))
                    )}

                    {!examCanStart && (
                        <Section title="Activation du mode plein écran requise" collapsible={false}>
                            <AlertEntry type="info" title="ATTENTION">
                                <p>
                                    Pour commencer cet examen, vous devez d'abord activer le mode plein écran.
                                    Les questions ne s'afficheront qu'après l'activation du mode plein écran.
                                </p>
                            </AlertEntry>
                        </Section>
                    )}
                </div>
            </div>

            {/* Informations de sécurité et sauvegarde */}
            <div className="fixed bottom-4 right-4 text-sm text-gray-600 bg-white px-3 py-2 rounded shadow">
                {autoSave.isSaving && <span>Sauvegarde automatique en cours...</span>}
                {autoSave.lastSaved && (
                    <span>Dernière sauvegarde: {autoSave.lastSaved.toLocaleTimeString()}</span>
                )}
                {!autoSave.lastSaved && !autoSave.isSaving && (
                    <span>Auto-sauvegarde activée</span>
                )}
            </div>

            <ConfirmationModal
                title="Confirmer la soumission"
                message="Êtes-vous sûr de vouloir terminer cet examen ? Cette action est irréversible."
                icon={QuestionMarkCircleIcon}
                type='info'
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handleSubmit}
                loading={isSubmitting || processing}
            >
                <p className="text-gray-600 mb-6 text-center ">
                    Assurez-vous d'avoir répondu à toutes les questions avant de confirmer.
                </p>
            </ConfirmationModal>

            <FullscreenModal
                isOpen={
                    showFullscreenModal
                }
                onEnterFullscreen={enterFullscreen}
            />
        </div>
    );
}

// interface ConfirmModalProps {
//     isOpen: boolean;
//     onClose: () => void;
//     onConfirm: () => void;
//     isSubmitting: boolean;
// }

// function ConfirmModal({ isOpen, onClose, onConfirm, isSubmitting }: ConfirmModalProps) {
//     return (
//         <Modal isOpen={isOpen} onClose={onClose}
//         >
//             <div className=' min-h-72 flex flex-col items-center justify-between p-6'>
//                 <QuestionMarkCircleIcon className="h-12 w-12 text-blue-500 mx-auto mb-4" />
//                 <h3 className="text-lg font-bold mb-4">Confirmer la soumission</h3>
//                 <p className="text-gray-600 mb-6 text-center ">
//                     Êtes-vous sûr de vouloir terminer cet examen ? Cette action est irréversible.
//                 </p>
//                 <p className="text-sm text-gray-500 text-center mb-6">
//                     Assurez-vous d'avoir répondu à toutes les questions avant de confirmer.
//                 </p>
//                 <div className="flex justify-end w-full space-x-4">
//                     <Button
//                         size="md"
//                         color="secondary"
//                         variant="outline"
//                         onClick={onClose}
//                     >
//                         Continue l'examen
//                     </Button>
//                     <Button
//                         size="md"
//                         color="primary"
//                         onClick={onConfirm}
//                         disabled={isSubmitting}
//                     >
//                         Confirmer
//                     </Button>
//                 </div>
//             </div>
//         </Modal>
//     );
// }

interface FullscreenModalProps {
    isOpen: boolean;
    onEnterFullscreen: () => void;
}

function FullscreenModal({ isOpen, onEnterFullscreen }: FullscreenModalProps) {
    return (
        <Modal isOpen={isOpen} onClose={() => { }}>
            <div className="p-6">
                <div className="flex items-center justify-center mb-4">
                    <div className="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full">
                        <ArrowsPointingOutIcon
                            className="w-6 h-6 text-blue-600" />
                    </div>
                </div>

                <div className="text-center mb-6">
                    <h3 className="text-lg font-medium text-gray-900 mb-2">
                        Mode plein écran requis
                    </h3>
                    <p className="text-gray-600">
                        Pour des raisons de sécurité, cet examen doit être passé en mode plein écran.
                        Cliquez sur le bouton ci-dessous pour entrer en mode plein écran.
                    </p>
                </div>

                <div className="flex justify-center">
                    <Button
                        size="md"
                        color="primary"
                        variant='outline'
                        onClick={onEnterFullscreen}
                        className="flex items-center"
                    >
                        <ArrowsPointingOutIcon className="w-4 h-4 mr-2" />
                        Entrer en plein écran
                    </Button>
                </div>
            </div>
        </Modal>
    );
}
