<?php

namespace App\Providers;

use App\Services\ExamService;
use App\Services\Student\AnswerService;
use Illuminate\Support\ServiceProvider;
use App\Services\Shared\DashboardService;
use App\Services\Shared\UserAnswerService;
use App\Services\Student\ExamScoringService;
use App\Services\Student\ExamSessionService;
use App\Services\Admin\AdminDashboardService;
use App\Services\Admin\UserManagementService;
use App\Services\Teacher\ExamAssignmentService;
use App\Services\Teacher\TeacherDashboardService;
use App\Services\Student\SecurityViolationService;
use App\Services\Teacher\ExamScoringService as TeacherExamScoringService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExamService::class, ExamService::class);

        $this->app->bind(ExamSessionService::class, ExamSessionService::class);
        $this->app->bind(AnswerService::class, AnswerService::class);
        $this->app->bind(SecurityViolationService::class, SecurityViolationService::class);
        $this->app->bind(ExamScoringService::class, ExamScoringService::class);

        $this->app->bind(ExamAssignmentService::class, ExamAssignmentService::class);
        $this->app->bind(TeacherExamScoringService::class, TeacherExamScoringService::class);

        $this->app->bind(UserAnswerService::class, UserAnswerService::class);
        $this->app->bind(DashboardService::class, DashboardService::class);

        $this->app->bind(TeacherDashboardService::class, TeacherDashboardService::class);
        $this->app->bind(AdminDashboardService::class, AdminDashboardService::class);

        $this->app->bind(UserManagementService::class, UserManagementService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
