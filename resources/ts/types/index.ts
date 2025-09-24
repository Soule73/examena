export type AssignmentStatus = 'assigned' | 'started' | 'submitted' | 'pending_review' | 'graded';

export type QuestionType = 'multiple' | 'text' | 'one_choice' | 'boolean';

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    active?: boolean;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
    roles?: Role[];
}

export interface Role {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
}

export interface Exam {
    id: number;
    title: string;
    description?: string;
    duration: number;
    is_active: boolean;
    start_time?: string;
    end_time?: string;
    teacher_id: number;
    created_at: string;
    updated_at: string;
    creator?: User;
    questions?: Question[];
    questions_count?: number;
}



export interface Question {
    id: number;
    exam_id: number;
    type: QuestionType;
    content: string;
    points: number;
    order_index: number;
    created_at: string;
    updated_at: string;
    choices?: Choice[];
    correct_answer?: string;
}

export interface Choice {
    id: number;
    question_id: number;
    content: string;
    is_correct: boolean;
    order_index: number;
    created_at: string;
    updated_at: string;
}

export interface Answer {
    id: number;
    assignment_id: number;
    question_id: number;
    choice_id?: number;
    answer_text?: string;
    score?: number;
    created_at: string;
    updated_at: string;

    choice?: Choice;
    choices?: Array<{
        choice_id: number;
        choice: Choice;
    }>;
    selectedChoice?: Choice;
}

export interface BackendAnswerData {
    type: 'single' | 'multiple';
    choice_id?: number;
    answer_text?: string;
    choices?: Array<{
        choice_id: number;
        choice: Choice;
    }>;
    choice?: Choice;
}

export type FlashMessageObject = { id: string; message: string } | null;

export interface FlashMessages {
    success?: FlashMessageObject;
    error?: FlashMessageObject;
    warning?: FlashMessageObject;
    info?: FlashMessageObject;
}

export interface ExamAssignment {
    id: number;
    student_id: number;
    exam_id: number;
    assigned_at: string;
    started_at?: string;
    submitted_at?: string;
    score?: number;
    auto_score?: number;
    status: AssignmentStatus;
    teacher_notes?: string;
    security_violations?: any;
    forced_submission: boolean;
    created_at: string;
    updated_at: string;
    exam?: Exam;
    student?: User;
    answers?: Answer[];
}

export type PageProps<T = Record<string, unknown>> = {
    auth: {
        user: User;
    };
    flash: FlashMessages;
} & T;


// Types pour les formulaires de création/édition
export interface QuestionFormData {
    id?: number;
    content: string;
    type: QuestionType;
    points: number;
    order_index: number;
    choices: ChoiceFormData[];
}

export interface ChoiceFormData {
    id?: number;
    content: string;
    is_correct: boolean;
    order_index: number;
}

export interface ExamFormData {
    title: string;
    description: string;
    duration: number;
    is_active: boolean;
    questions: QuestionFormData[];
    deletedQuestionIds?: number[];
    deletedChoiceIds?: number[];
}