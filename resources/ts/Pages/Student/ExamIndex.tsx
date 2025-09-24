import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps, ExamAssignment } from '@/types';
import Section from '@/Components/Section';
import StudentExamAssignmentList from '@/Components/exam/StudentExamAssignmentList';
import { PaginationType } from '@/types/datatable';

interface Props extends PageProps {
    pagination: PaginationType<ExamAssignment>;
}

export default function StudentExamIndex({ pagination }: Props) {
    return (
        <AuthenticatedLayout title="Mes examens">
            <Head title="Mes examens" />

            <div className="space-y-8">
                <Section
                    title={`Examens (${pagination?.total || 0})`}
                    collapsible={true}
                >
                    <StudentExamAssignmentList
                        data={pagination}
                        variant="full"
                        showFilters={true}
                        showSearch={true}
                    />
                </Section>
            </div>
        </AuthenticatedLayout>
    );
}