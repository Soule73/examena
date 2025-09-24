import Section from '@/Components/Section';
import { ExamAssignment, User } from '@/types';
import { PaginationType } from '@/types/datatable';
import StudentExamAssignmentList from '@/Components/exam/StudentExamAssignmentList';
import ShowUser from './ShowUser';


interface Props {
    user: User;
    examsAssignments: PaginationType<ExamAssignment>;
}

export default function ShowStudent({ user, examsAssignments }: Props) {

    return (
        <ShowUser user={user} >

            <Section title="Examens assignés" subtitle="Liste des examens auxquels l'étudiant est inscrit">
                <StudentExamAssignmentList
                    data={examsAssignments}
                    variant="admin"
                    showFilters={true}
                    showSearch={true}
                />
            </Section>
        </ShowUser >
    );
}