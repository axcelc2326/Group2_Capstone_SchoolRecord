<?php

use App\Http\Controllers\{
    ProfileController,
    StudentController,
    ClassController,
    GradeController,
    GradeRemarkController,
    AnnouncementController,
    AnalyticsController,
    ParentController,
    TeacherController,
    AdminController,
    DashboardController,
    UserManagementController,
    TeacherStudentController,
    SubjectController,
    SF5Controller,
    HonorRollController,
    RecordsController,
    ParentAccountController
};
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ✅ Default landing page
Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ✅ Authenticated Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ✅ Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| 👨‍👩‍👧 PARENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:parent', 'verified'])->group(function () {
    // Parents Student
    Route::get('/students/index', [StudentController::class, 'index'])->name('students.index');

    // Parent-specific grade viewing
    Route::get('/parent/grades', [GradeController::class, 'viewGrades'])->name('parent.grades');
    Route::get('/student/{id}/grades/pdf', [GradeController::class, 'generatePdf'])->name('student.grades.pdf');

    // Announcements from Admin and Class
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
});

/*
|--------------------------------------------------------------------------
| 👨‍🏫 TEACHER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:teacher', 'verified'])->group(function () {
    // Student management
    Route::get('/teacher/students', [TeacherController::class, 'myStudents'])->name('teacher.students');
    Route::get('/teacher/student/{student}/grades', [TeacherController::class, 'viewStudentGrades'])->name('teacher.student.grades');
    
    // Grade management
    Route::get('/grades/modal/{studentId}', [GradeController::class, 'InputGrade'])->name('grades.InputGrade');
    Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');

    // Teacher-specific announcements
    Route::get('/teacher/announcements/create', [AnnouncementController::class, 'createTeacher'])->name('teacher.announcements.create');
    Route::post('/teacher/announcements', [AnnouncementController::class, 'storeTeacher'])->name('teacher.announcements.store');
    Route::put('/teacher/announcements/{id}', [AnnouncementController::class, 'updateTeacher'])->name('announcements.updateTeacher');
    Route::delete('/teacher/announcements/{id}', [AnnouncementController::class, 'destroyTeacher'])->name('teacher.announcements.destroy');

    // Advanced student management
    Route::put('/students/{student}/unapprove', [TeacherStudentController::class, 'unapproveStudent'])->name('teacher.students.unapprove');
    Route::put('/teacher/students/unapprove-all', [TeacherStudentController::class, 'unapproveAll'])->name('teacher.students.unapproveAll');
    Route::delete('/teacher/students/{student}/clear-grades', [TeacherStudentController::class, 'clearGrades'])->name('teacher.students.clearGrades');
    Route::delete('/teacher/students/clear-all-grades', [TeacherStudentController::class, 'clearAllGrades'])->name('teacher.students.clearAllGrades');
    Route::get('/teacher/classes/available', [TeacherStudentController::class, 'getAvailableClasses'])->name('teacher.classes.available');
    Route::put('/teacher/students/promote', [TeacherStudentController::class, 'promoteStudents'])->name('teacher.students.promote');
    
    // Teachers Analytics
    Route::get('/teacher/analytics', [AnalyticsController::class, 'teacherAnalytics'])->name('teacher.analytics');
    Route::get('/teacher/analytics/class/{class}/students', [AnalyticsController::class, 'showTeacherClassStudents'])->name('analytics.teacher.class.students');

    // SF5 Download (School Form 5)
    Route::get('/sf5/form', [SF5Controller::class, 'showForm'])->name('sf5.form');
    Route::post('/sf5/download', [SF5Controller::class, 'download'])->name('sf5.download');
    Route::put('/sf5/{id}', [SF5Controller::class, 'update'])->name('sf5.update');
    Route::delete('/sf5/{id}', [SF5Controller::class, 'destroy'])->name('sf5.destroy');
    Route::get('/sf5/download-again/{id}', [SF5Controller::class, 'downloadAgain'])->name('sf5.download.again');

    // Honor Download Form
    Route::get('/honorlist', [HonorRollController::class, 'index'])->name('honorlist.index');
    Route::post('/honorlist/generate', [HonorRollController::class, 'store'])->name('honorlist.generate');
    Route::put('/honor-roll/{id}', [HonorRollController::class, 'update'])->name('honorroll.update');
    Route::delete('/honor-rolls/{honorRoll}', [HonorRollController::class, 'destroy'])->name('honor-rolls.destroy');
    Route::get('/honorlist/{honorRoll}/download', [HonorRollController::class, 'download'])->name('honorlist.download');
});

/*
|--------------------------------------------------------------------------
| 🧑‍💼 ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin', 'verified'])->group(function () {
    // Class management
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store'); 
    Route::put('/classes/{id}', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy'); 
    Route::get('/api/classes/{id}/subjects', [ClassController::class, 'viewSubjects']);
    
    // Subject management
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

    // Teacher assignment
    Route::get('/admin/classes/assign', [AdminController::class, 'assignTeacherForm'])->name('admin.assign-teacher');
    Route::post('/admin/classes/assign', [AdminController::class, 'assignTeacher'])->name('admin.assign-teacher.submit');

    // Admin announcements
    Route::get('/announcements/create', [AnnouncementController::class, 'createAdmin'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'storeAdmin'])->name('announcements.store');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'updateAdmin'])->name('announcements.update');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroyAdmin'])->name('announcements.destroy');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/grade/{gradeLevel}', [AnalyticsController::class, 'showGradeLevelStudents'])->name('analytics.grade.students');

    // Add Teacher User
    Route::resource('teachers', TeacherController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::put('/teachers/{id}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');

    // Reports Management 
    Route::get('/records', [RecordsController::class, 'index'])->name('admin.records.index');
    Route::get('/records/{type}/{id}', [RecordsController::class, 'show'])->name('admin.records.show');// JSON endpoint for modal details
    Route::get('/records/{type}/{id}/download', [RecordsController::class, 'download'])->name('admin.records.download');// re-generate & download PDF
    Route::post('/records/{type}/{id}/toggle-reviewed', [RecordsController::class, 'toggleReviewed'])->name('admin.records.toggleReviewed');// toggle mark reviewed/unreviewed
});

/*
|--------------------------------------------------------------------------
| 🧑‍💼 ADMIN AND TEACHER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|teacher'])->group(function () {
    // Creates an account for Parents
    Route::get('/parents', [ParentController::class, 'index'])->name('parents.index');
    Route::post('/parents', [ParentController::class, 'store'])->name('parents.store');
    Route::put('/parents/{parent}', [ParentController::class, 'update'])->name('parents.update');
    Route::delete('/parents/{id}', [ParentController::class, 'destroy'])->name('parents.destroy');
    Route::put('/parents/{id}/toggle-status', [ParentController::class, 'toggleStatus'])->name('parents.toggle-status');

    // Pass the data into the Modal
    Route::get('/parents/{parent}/students', [ParentController::class, 'getStudents'])->name('parents.students.index');

    // Generator for parents account
    Route::get('/teacher/parent-accounts/download-filtered', [ParentAccountController::class, 'downloadFiltered'])->name('parent-accounts.downloadFiltered');

    // Register Student into the parent with edit and delete
    Route::post('/parents/{parent}/students', [StudentController::class, 'store'])->name('parents.students.store');
    Route::put('/parents/{parent}/students/{student}', [StudentController::class, 'update'])->name('parents.students.update');
    Route::delete('/parents/{parent}/students/{student}', [StudentController::class, 'destroy'])->name('parents.students.destroy');
});

// ✅ Breeze Auth Routes
require __DIR__ . '/auth.php';