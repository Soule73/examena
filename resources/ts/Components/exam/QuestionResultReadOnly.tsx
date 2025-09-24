import { Choice } from "@/types";
import { CheckIcon } from "@heroicons/react/16/solid";
import MarkdownRenderer from "../form/MarkdownRenderer";
import { questionIndexLabel } from "./TakeQuestion";

interface QuestionResultReadOnlyTextProps {
    userText?: string;
    label?: string;
}

const QuestionResultReadOnlyText: React.FC<QuestionResultReadOnlyTextProps> = ({ userText, label = "Votre réponse" }) => {
    return (
        <div className="p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <p className="text-sm text-gray-600 mb-1">{label}</p>
            <MarkdownRenderer>
                {userText || 'Aucune réponse fournie'}
            </MarkdownRenderer>
        </div>
    );
};

interface QuestionResultReadOnlyChoicesProps {
    choices: Choice[];
    userChoices: Choice[];
    type: 'one_choice' | 'multiple' | 'boolean';
    isTeacherView?: boolean;
}

const QuestionResultReadOnlyChoices: React.FC<QuestionResultReadOnlyChoicesProps> = ({ choices, userChoices, type, isTeacherView = false }) => {
    return (
        <div className="space-y-2">
            {(choices ?? []).map((choice, idx) => {
                const isSelected = userChoices.some(uc => uc.id === choice.id);
                const isCorrect = choice.is_correct;

                const bg = isSelected
                    ? isCorrect
                        ? 'bg-green-50 border-green-200'
                        : 'bg-red-50 border-red-200'
                    : isCorrect
                        ? 'bg-green-50 border-green-200'
                        : 'bg-gray-50 border-gray-200'
                    ;

                const text = isSelected
                    ? isCorrect
                        ? 'text-green-800 font-medium'
                        : 'text-red-800 font-medium'
                    : isCorrect
                        ? 'text-green-800 font-medium'
                        : 'text-gray-700'
                    ;

                const border = type === 'multiple'
                    ? 'rounded border-2'
                    : 'rounded-full border-2'
                    ;

                const borderColor = isSelected
                    ? isCorrect
                        ? 'border-green-500 bg-green-500'
                        : 'border-red-500 bg-red-500'
                    : isCorrect
                        ? 'border-green-500 bg-green-500'
                        : 'border-gray-300'
                    ;

                const indexLabel = type === "boolean" ? (
                    (() => {
                        const normalized = choice.content?.toString().toLowerCase() ?? '';
                        const isTrue = ['true', 'vrai'].includes(normalized);
                        const badgeClass = isTrue ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        return (
                            <span className={`inline-flex items-center justify-center h-6 w-6 rounded-full ${badgeClass} text-xs font-medium mr-2`}>
                                {isTrue ? 'V' : 'F'}
                            </span>
                        );
                    })()
                ) : questionIndexLabel(idx, isCorrect ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800');
                return (
                    <div
                        key={choice.id}
                        className={`p-3 rounded-lg border ${bg}`}
                    >
                        <div className="flex items-center">
                            <div className={`w-4 h-4 mr-3 flex items-center justify-center ${border} ${borderColor}`}>
                                {(isSelected || isCorrect) && (
                                    <CheckIcon className="w-4 h-4 fill-white" />
                                )}
                            </div>
                            {indexLabel}
                            <span className={`${text}`}>
                                {type === 'boolean' ? (
                                    (() => {
                                        const normalized = choice.content?.toString().toLowerCase() ?? '';
                                        const isTrue = ['true', 'vrai'].includes(normalized);
                                        return isTrue ? 'Vrai' : 'Faux';
                                    })()
                                ) : choice.content}
                            </span>
                            {isSelected && !isCorrect && (
                                <span className="ml-2 text-xs text-red-600 font-medium">
                                    {isTeacherView ? "Réponse de l'étudiant (incorrecte)" : "Votre réponse (incorrecte)"}
                                </span>
                            )}
                            {(isCorrect && isSelected) && (
                                <span className="ml-2 text-xs text-green-600 font-medium">
                                    {isTeacherView ? "Réponse de l'étudiant (correcte)" : "Votre réponse (correcte)"}
                                </span>
                            )}
                            {isCorrect && !isSelected && (
                                <span className="ml-2 text-xs text-green-600 font-medium">
                                    Bonne réponse
                                </span>
                            )}
                        </div>
                    </div>
                );
            })}
        </div>
    );
};


interface QuestionTeacherReadOnlyChoicesProps {
    choices: Choice[];
    type: 'one_choice' | 'multiple' | 'boolean';
}

const QuestionTeacherReadOnlyChoices: React.FC<QuestionTeacherReadOnlyChoicesProps> = ({ choices, type }) => {
    return (
        <div className="space-y-2">
            {(choices ?? []).map((choice, idx) => {
                // const isSelected = userChoices.some(uc => uc.id === choice.id);
                const isCorrect = choice.is_correct;

                const bg = isCorrect
                    ? 'bg-green-50 border-green-200'
                    : 'bg-gray-50 border-gray-200'
                    ;

                const text = isCorrect
                    ? 'text-green-800 font-medium'
                    : 'text-gray-700'
                    ;

                const border = type === 'multiple'
                    ? 'rounded border-2'
                    : 'rounded-full border-2'
                    ;

                const borderColor = isCorrect
                    ? 'border-green-500 bg-green-500'
                    : 'border-gray-300'
                    ;

                const indexLabel = type === "boolean" ? (
                    (() => {
                        const normalized = choice.content?.toString().toLowerCase() ?? '';
                        const isTrue = ['true', 'vrai'].includes(normalized);
                        const badgeClass = isTrue ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        return (
                            <span className={`inline-flex items-center justify-center h-6 w-6 rounded-full ${badgeClass} text-xs font-medium mr-2`}>
                                {isTrue ? 'V' : 'F'}
                            </span>
                        );
                    })()
                ) : questionIndexLabel(idx, isCorrect ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800');

                return (
                    <div
                        key={choice.id}
                        className={`p-3 rounded-lg border ${bg}`}
                    >
                        <div className="flex items-center">
                            <div className={`w-4 h-4 mr-3 flex items-center justify-center ${border} ${borderColor}`}>
                                {isCorrect && (
                                    <CheckIcon className="w-4 h-4 fill-white" />
                                )}
                            </div>
                            {indexLabel}

                            <span className={`${text}`}>
                                {type === 'boolean' ? (
                                    (() => {
                                        const normalized = choice.content?.toString().toLowerCase() ?? '';
                                        const isTrue = ['true', 'vrai'].includes(normalized);
                                        return isTrue ? 'Vrai' : 'Faux';
                                    })()
                                ) : choice.content}
                            </span>
                            {isCorrect && (
                                <span className="ml-2 text-xs text-green-600 font-medium">
                                    Correct
                                </span>
                            )}
                        </div>
                    </div>
                );
            })}
        </div>
    );
};

export { QuestionResultReadOnlyText, QuestionTeacherReadOnlyChoices, QuestionResultReadOnlyChoices };