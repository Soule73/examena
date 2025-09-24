import { CheckCircleIcon, XMarkIcon } from "@heroicons/react/16/solid";
import MarkdownRenderer from "../form/MarkdownRenderer";
import { Question } from "@/types";
import { TYPE_COLORS, TYPE_LABELS } from "./TakeQuestion";

interface QuestionReadOnlySection {
    question: Question;
    children?: React.ReactNode;
    isCorrect?: boolean | null;
    score?: number | null;
    questionIndex?: number;
}

const QuestionReadOnlySection: React.FC<QuestionReadOnlySection> = ({ question, children, isCorrect, score, questionIndex }) => {
    return (
        <div key={question.id} className="border-b border-gray-200 pb-6 last:border-b-0">
            <div className="flex items-center  justify-between my-3">
                <h3 className="text-lg font-medium text-gray-900">
                    {questionIndex !== undefined && `Q${questionIndex + 1}. `}
                </h3>
                <div className="flex items-center ml-4">
                    {isCorrect === true && (
                        <span className="text-green-600 text-sm font-medium flex items-center">
                            <CheckCircleIcon className="w-4 h-4 mr-1" />
                            Correct
                        </span>
                    )}
                    {isCorrect === false && (
                        <span className="text-red-600 text-sm font-medium flex items-center">
                            <XMarkIcon className="w-4 h-4 mr-1" />
                            Incorrect
                        </span>
                    )}
                    <span className="text-sm text-gray-500 ml-2">
                        {score && `${score}/`}{question.points} pts
                    </span>
                    <span className={`text-xs ml-2 px-2 py-1 min-w-fit h-max rounded-full ${TYPE_COLORS[question.type] ?? 'bg-gray-100 text-gray-800'}`}>
                        {TYPE_LABELS[question.type] ?? question.type}
                    </span>
                </div>
            </div>

            <div className="mb-4">
                <MarkdownRenderer className="text-gray-700">
                    {question.content}
                </MarkdownRenderer>
            </div>

            {children}
        </div>
    );
};

export default QuestionReadOnlySection;