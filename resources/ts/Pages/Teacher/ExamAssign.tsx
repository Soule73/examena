import { FormEvent } from 'react';
import { router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/Button';
import Section from '@/Components/Section';
import { Checkbox } from '@/Components/form/Input';
import { Exam, User } from '@/types';
import { route } from 'ziggy-js';

interface Props {
    exam: Exam;
    students: User[];
    assignedStudents: number[];
}

export default function ExamAssign({ exam, students, assignedStudents }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        student_ids: assignedStudents || []
    });

    const handleStudentToggle = (studentId: number) => {
        const currentIds = data.student_ids || [];
        const newStudentIds = currentIds.includes(studentId)
            ? currentIds.filter(id => id !== studentId)
            : [...currentIds, studentId];

        setData('student_ids', newStudentIds);
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(`/teacher/exams/${exam.id}/assign`);
    };

    const selectAll = () => {
        setData('student_ids', students.map(student => student.id));
    };

    const deselectAll = () => {
        setData('student_ids', []);
    };

    const selectedCount = (data.student_ids || []).length;

    return (
        <AuthenticatedLayout title={`Assigner l'examen: ${exam.title}`}>

            <Section
                title="Informations sur l'examen"
                subtitle="Détails de l'examen à assigner"
                actions={<Button
                    type="button"
                    onClick={() => router.visit(route('teacher.exams.show', exam.id))}
                    color="secondary"
                    variant="outline"
                >
                    Annuler
                </Button>}
            >
                <div className="space-y-2">
                    <h2 className="text-xl font-semibold text-gray-900">{exam.title}</h2>
                    {exam.description && (
                        <p className="text-gray-600">{exam.description}</p>
                    )}
                    <p className="text-sm text-gray-500">
                        Durée: {exam.duration} minutes
                    </p>
                </div>
            </Section>

            <form onSubmit={handleSubmit}>
                {errors.student_ids && (
                    <div className="text-red-500 text-sm mb-4">{errors.student_ids}</div>
                )}
                <Section
                    title="Sélectionner les étudiants"
                    subtitle={`${selectedCount} étudiant${selectedCount !== 1 ? 's' : ''} sélectionné${selectedCount !== 1 ? 's' : ''}`}
                    actions={
                        <div className="space-x-2">
                            <Button type="button" onClick={selectAll} color="secondary" variant="outline" size="sm">
                                Tout sélectionner
                            </Button>
                            <Button type="button" onClick={deselectAll} color="secondary" variant="outline" size="sm">
                                Tout désélectionner
                            </Button>
                            <Button
                                type="submit" size='sm'
                                disabled={processing || selectedCount === 0}
                                loading={processing}
                            >
                                {processing ? 'Attribution...' : `Assigner à ${selectedCount} étudiant${selectedCount !== 1 ? 's' : ''}`}
                            </Button>
                        </div>
                    }
                >

                    <div className="overflow-y-auto">
                        {students.length === 0 ? (
                            <p className="text-gray-500 text-center py-8">
                                Aucun étudiant disponible
                            </p>
                        ) : (
                            <div className="space-y-2">
                                {students.map((student) => (
                                    <div key={student.id} className="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                        <div className="flex items-center space-x-3">
                                            <Checkbox
                                                id={`student_${student.id}`}
                                                checked={(data.student_ids || []).includes(student.id)}
                                                onChange={() => handleStudentToggle(student.id)}
                                                label={
                                                    <div>
                                                        <div className="font-medium text-gray-900">{student.name}</div>
                                                        <div className="text-sm text-gray-500">{student.email}</div>
                                                    </div>
                                                }
                                            />
                                        </div>
                                        {(assignedStudents || []).includes(student.id) && (
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Déjà assigné
                                            </span>
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </Section>
            </form>
        </AuthenticatedLayout>
    );
}