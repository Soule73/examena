import React from 'react';
import MarkdownRenderer from '@/Components/form/MarkdownRenderer';
import MarkdownEditor from '@/Components/form/MarkdownEditor';
import Section from '@/Components/Section';
import { Choice, Question } from '@/types';
import { Checkbox } from '../form/Input';

type AnswerValue = string | number | number[];

interface TakeQuestionProps {
    question: Question;
    answers: Record<number, AnswerValue>;
    onAnswerChange: (questionId: number, value: AnswerValue) => void;
}

interface BaseChoiceProps {
    questionId: number;
    choices: Choice[];
    answers: Record<number, AnswerValue>;
    onAnswerChange: (questionId: number, value: AnswerValue) => void;
}

/* ---------- Utilities ---------- */

export const TYPE_LABELS: Record<string, string> = {
    multiple: 'Choix multiples',
    one_choice: 'Choix unique',
    boolean: 'Vrai/Faux',
    text: 'Réponse texte',
};

export const TYPE_COLORS: Record<string, string> = {
    multiple: 'bg-blue-100 text-blue-800',
    one_choice: 'bg-green-100 text-green-800',
    boolean: 'bg-yellow-100 text-yellow-800',
    text: 'bg-purple-100 text-purple-800',
};

export const questionIndexLabel = (idx: number, bgClass = 'bg-gray-100 text-gray-800') => (
    <span className={`inline-flex items-center justify-center h-6 w-6 rounded-full ${bgClass} text-xs font-medium mr-2`}>
        {String.fromCharCode(65 + idx)}
    </span>
);

/* ---------- Subcomponents ---------- */

const TakeQuestionMultiple: React.FC<BaseChoiceProps> = ({ questionId, choices, answers, onAnswerChange }) => {
    const current = Array.isArray(answers[questionId]) ? (answers[questionId] as number[]) : [];

    const toggleChoice = (choiceId: number, checked: boolean) => {
        if (checked) onAnswerChange(questionId, [...current, choiceId]);
        else onAnswerChange(questionId, current.filter((id) => id !== choiceId));
    };

    return (
        <div className="space-y-3 flex flex-col">
            {choices.map((choice, idx) => (
                <Checkbox
                    key={choice.id}
                    type="checkbox"
                    label={<>
                        {questionIndexLabel(idx, 'bg-blue-100 text-blue-800')}
                        <MarkdownRenderer>{choice.content}</MarkdownRenderer>
                    </>}
                    checked={current.includes(choice.id)}
                    onChange={(e) => toggleChoice(choice.id, e.target.checked)}
                    value={choice.id}
                    labelClassName="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                />
            ))}
        </div>
    );
};

const TakeQuestionOneChoice: React.FC<BaseChoiceProps> = ({ questionId, choices, answers, onAnswerChange }) => {
    const onChange = (value: number) => onAnswerChange(questionId, value);

    return (
        <div className="space-y-3 flex flex-col">
            {choices.map((choice, idx) => (
                <Checkbox
                    key={choice.id}
                    type="radio"
                    name={`question_${questionId}`}
                    label={<>
                        {questionIndexLabel(idx)}
                        <MarkdownRenderer>{choice.content}</MarkdownRenderer>
                    </>}
                    checked={answers[questionId] === choice.id}
                    onChange={() => onChange(choice.id)}
                    value={choice.id}
                    labelClassName="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                />
            ))}
        </div>
    );
};

const TakeQuestionBoolean: React.FC<BaseChoiceProps> = ({ questionId, choices, answers, onAnswerChange }) => {
    const onChange = (value: number) => onAnswerChange(questionId, value);

    return (
        <div className="space-y-3 flex flex-col">
            {choices.map((choice) => {
                const normalized = choice.content?.toString().toLowerCase() ?? '';
                const isTrue = ['true', 'vrai'].includes(normalized);
                const badgeClass = isTrue ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                const labelText = isTrue ? 'Vrai' : 'Faux';

                return (
                    <Checkbox
                        key={choice.id}
                        type="radio"
                        name={`question_${questionId}`}
                        checked={answers[questionId] === choice.id}
                        onChange={() => onChange(choice.id)}
                        value={choice.id}
                        labelClassName="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                        label={<>
                            <span className={`inline-flex items-center justify-center h-6 w-6 rounded-full ${badgeClass} text-xs font-medium mr-2`}>
                                {isTrue ? 'V' : 'F'}
                            </span>
                            <span className="text-gray-900">{labelText}</span>
                        </>}
                    />
                );
            })}
        </div>
    );
};

const TakeQuestionText: React.FC<{
    questionId: number;
    answers: Record<number, AnswerValue>;
    onAnswerChange: (questionId: number, value: AnswerValue) => void;
}> = ({ questionId, answers, onAnswerChange }) => (
    <div>
        <MarkdownEditor
            value={typeof answers[questionId] === 'string' ? (answers[questionId] as string) : ''}
            onChange={(value) => onAnswerChange(questionId, value)}
            placeholder="Tapez votre réponse ici... (Markdown supporté)"
            rows={6}
            helpText="Vous pouvez utiliser la syntaxe Markdown pour formater votre réponse"
        />
    </div>
);

/* ---------- Main Component ---------- */

const TakeQuestion: React.FC<TakeQuestionProps> = ({ question, answers, onAnswerChange }) => {
    return (
        <Section
            key={question.id}
            className="!justify-start !items-start relative"
            centerHeaderItems={false}
            title={<MarkdownRenderer>{question.content}</MarkdownRenderer>}
            actions={
                <div className="flex space-x-2 top-5 right-5 absolute">
                    <div className="text-sm min-w-fit font-medium h-max text-blue-600 px-2 py-1 rounded">
                        {question.points} point(s)
                    </div>

                    <span className={`text-xs px-2 py-1 min-w-fit h-max rounded-full ${TYPE_COLORS[question.type] ?? 'bg-gray-100 text-gray-800'}`}>
                        {TYPE_LABELS[question.type] ?? question.type}
                    </span>
                </div>
            }
        >
            {question.type === 'multiple' && (
                <TakeQuestionMultiple questionId={question.id} choices={question.choices ?? []} answers={answers} onAnswerChange={onAnswerChange} />
            )}

            {question.type === 'one_choice' && (
                <TakeQuestionOneChoice questionId={question.id} choices={question.choices ?? []} answers={answers} onAnswerChange={onAnswerChange} />
            )}

            {question.type === 'boolean' && (
                <TakeQuestionBoolean questionId={question.id} choices={question.choices ?? []} answers={answers} onAnswerChange={onAnswerChange} />
            )}

            {question.type === 'text' && (
                <TakeQuestionText questionId={question.id} answers={answers} onAnswerChange={onAnswerChange} />
            )}
        </Section>
    );
};

export default TakeQuestion;
export { TakeQuestionMultiple, TakeQuestionOneChoice, TakeQuestionBoolean, TakeQuestionText };
