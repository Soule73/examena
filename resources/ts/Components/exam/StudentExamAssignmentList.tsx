import React from 'react';
import { Link } from '@inertiajs/react';
import { EyeIcon } from '@heroicons/react/24/outline';
import { DataTable } from '@/Components/DataTable';
import Badge from '@/Components/Badge';
import { Button } from '@/Components';
import { route } from 'ziggy-js';
import { formatDate, formatDuration, getAssignmentStatusWithLabel, getAssignmentBadgeLabel, getAssignmentBadgeType, assignmentStatusColors, assignmentStatusLabels } from '@/utils/formatters';
import { calculateScoreDisplay } from '@/utils/examUtils';
import { ExamAssignment } from '@/types';
import { DataTableConfig, PaginationType } from '@/types/datatable';
import type { FilterConfig } from '@/types/datatable';

interface StudentExamAssignmentListProps {
    data: PaginationType<ExamAssignment>;
    variant?: 'full' | 'dashboard' | 'admin';
    showFilters?: boolean;
    showSearch?: boolean;
}

/**
 * Composant de tableau commun pour afficher les examens assignés aux étudiants
 * Utilisé dans ExamIndex et Dashboard Student pour assurer la cohérence
 */
const StudentExamAssignmentList: React.FC<StudentExamAssignmentListProps> = ({
    data,
    variant = 'full',
    showFilters = true,
    showSearch = true
}) => {

    // Fonctions utilitaires pour les colonnes
    // Fonctions utilitaires placées avant la déclaration des colonnes
    const renderExam = (assignment: ExamAssignment, variant: string) => (
        <div>
            <div className="text-sm font-medium text-gray-900">
                {assignment.exam?.title || 'Titre non disponible'}
            </div>
            {variant === 'dashboard' ? (
                <div className="text-sm max-w-sm truncate text-gray-500">
                    {assignment.exam?.description || ''}
                </div>
            ) : assignment.exam?.description && (
                <div className="text-sm text-gray-500 truncate max-w-xs">
                    {assignment.exam.description}
                </div>
            )}
        </div>
    );

    const renderDuration = (assignment: ExamAssignment, dashboard?: boolean) => (
        <span className="text-sm text-gray-900">
            {assignment.exam ? (dashboard ? `${assignment.exam.duration} min` : formatDuration(assignment.exam.duration)) : '-'}
        </span>
    );

    const renderScore = (assignment: ExamAssignment, dashboard?: boolean) => {
        const grade = calculateScoreDisplay(assignment);
        if (dashboard) {
            if (assignment.status === 'graded' && grade) {
                return (
                    <div className="text-sm">
                        <span className={`font-medium ${grade.colorClass}`}>{grade.text}</span>
                    </div>
                );
            }
            return <span className="text-sm text-gray-400">-</span>;
        }
        return grade ? (
            <span className={`text-sm font-medium ${grade.colorClass}`}>{grade.text}</span>
        ) : (
            <span className="text-sm text-gray-500">{assignment.status === 'submitted' ? 'En attente' : 'Non noté'}</span>
        );
    };

    const renderStatus = (assignment: ExamAssignment, dashboard?: boolean) => (
        dashboard ? (
            <span className={`inline-flex px-2 py-1 text-xs font-medium rounded-full ${assignmentStatusColors[assignment.status] || assignmentStatusColors['default']}`}>
                {assignmentStatusLabels[assignment.status] || assignmentStatusLabels['default']}
            </span>
        ) : (
            <Badge
                type={getAssignmentBadgeType(assignment.status)}
                label={getAssignmentBadgeLabel(assignment.status)}
            />
        )
    );

    const renderActions = (assignment: ExamAssignment, dashboard?: boolean) => (
        dashboard ? (
            <Button
                size="sm"
                color='secondary'
                variant={'outline'}
                onClick={() => window.location.href = route('student.exams.show', assignment.exam_id)}
            >
                Voir
            </Button>
        ) : (
            <Link
                href={route('student.exams.show', assignment.exam_id)}
                className="text-green-600 hover:text-green-900 p-1"
                title="Voir l'examen"
            >
                <EyeIcon className="h-4 w-4" />
            </Link>
        )
    );

    const renderDate = (assignment: ExamAssignment, variant: string) => (
        <span className="text-sm text-gray-500">
            {assignment.submitted_at
                ? formatDate(assignment.submitted_at, variant === 'admin' ? 'short' : undefined, 'datetime')
                : variant === 'admin' ? 'Non soumis' : formatDate(assignment.started_at || assignment.created_at, 'datetime')
            }
        </span>
    );

    const columns: DataTableConfig<ExamAssignment>["columns"] =
        variant === 'dashboard' ? [
            {
                key: 'exam_title',
                label: 'Examen',
                render: (item) => renderExam(item, 'dashboard')
            },
            {
                key: 'duration',
                label: 'Durée',
                render: (item) => renderDuration(item, true)
            },
            {
                key: 'status',
                label: 'Statut',
                render: (item) => renderStatus(item, true)
            },
            {
                key: 'score',
                label: 'Note',
                render: (item) => renderScore(item, true)
            },
            {
                key: 'actions',
                label: 'Actions',
                render: (item) => renderActions(item, true)
            }
        ] : variant === 'admin' ? [
            {
                key: 'exam',
                label: 'Examen',
                render: (item) => renderExam(item, 'admin')
            },
            {
                key: 'date',
                label: 'Soumis le',
                render: (item) => renderDate(item, 'admin')
            },
            {
                key: 'duration',
                label: 'Durée',
                render: (item) => renderDuration(item)
            },
            {
                key: 'score',
                label: 'Note',
                render: (item) => renderScore(item)
            },
            {
                key: 'status',
                label: 'Statut',
                render: (item) => renderStatus(item, true)
            }
        ] : [
            {
                key: 'exam',
                label: 'Examen',
                render: (item) => renderExam(item, 'full')
            },
            {
                key: 'date',
                label: 'Date',
                render: (item) => renderDate(item, 'full')
            },
            {
                key: 'duration',
                label: 'Durée',
                render: (item) => renderDuration(item)
            },
            {
                key: 'score',
                label: 'Note',
                render: (item) => renderScore(item)
            },
            {
                key: 'status',
                label: 'Statut',
                render: (item) => renderStatus(item)
            },
            {
                key: 'actions',
                label: 'Actions',
                className: 'text-right',
                render: (item) => renderActions(item)
            }
        ];

    // Filtres communs
    const filters: FilterConfig[] = showFilters ? [
        {
            key: 'status',
            label: 'Statut',
            type: 'select',
            options: getAssignmentStatusWithLabel().filter(status => status.value !== 'all')
        }
    ] : [];

    // Placeholders et empty states
    const searchPlaceholder = showSearch
        ? (variant === 'dashboard' ? 'Rechercher un examen...' : variant === 'admin' ? "Rechercher par titre d'examen ou nom d'étudiant..." : "Rechercher par titre d'examen...")
        : undefined;

    const emptyState = {
        title: "Aucun examen assigné",
        subtitle: variant === 'admin'
            ? "Aucun examen n'a été assigné aux étudiants."
            : "Vous n'avez actuellement aucun examen assigné."
    };

    const emptySearchState = {
        title: "Aucun examen trouvé",
        subtitle: "Aucun examen ne correspond à vos critères de recherche ou de filtre.",
        resetLabel: "Réinitialiser les filtres"
    };

    const perPageOptions = variant === 'dashboard' ? [5, 10, 15, 20] : [10, 25, 50];

    const tableConfig: DataTableConfig<ExamAssignment> = {
        columns,
        filters,
        searchPlaceholder,
        emptyState,
        emptySearchState,
        perPageOptions
    };

    return (
        <DataTable
            data={data}
            config={tableConfig}
        />
    );
};
export default StudentExamAssignmentList;