import { Exam, User } from '@/types';
import { PaginationType } from '@/types/datatable';
import ShowUser from './ShowUser';
import Section from '@/Components/Section';
import TeacherExamList from '@/Components/exam/TeacherExamList';


interface Props {
    user: User;
    exams: PaginationType<Exam>;
}

export default function ShowTeacher({ user, exams }: Props) {

    return (
        <ShowUser user={user} >
            <Section title="Examens créés" subtitle="Liste des examens créés par l'enseignant">
                <TeacherExamList
                    data={exams}
                    variant="admin"
                    showFilters={true}
                    showSearch={true}
                />
            </Section>
        </ShowUser >
    );
}