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
    UserManagementController
};
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ✅ Default landing page
Route::get('/', function () {
    return Inertia::render('LandingPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
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
Route::middleware(['auth', 'role:parent',  'verified'])->group(function () {
    Route::middleware(['role:parent'])->get('/parent/grades', [GradeController::class, 'viewGrades'])->name('parent.grades');

    Route::get('/students/register', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');

    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
});

/*
|--------------------------------------------------------------------------
| 👨‍🏫 TEACHER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:teacher',  'verified'])->group(function () {
    Route::get('/grades/enter/{student}', [GradeController::class, 'create'])->name('grades.create');
    Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');
    Route::get('/teacher/student/{student}/grades', [TeacherController::class, 'viewStudentGrades'])->name('teacher.student.grades');

    Route::get('/teacher/announcements/create', [AnnouncementController::class, 'createTeacher'])->name('teacher.announcements.create');
    Route::post('/teacher/announcements', [AnnouncementController::class, 'storeTeacher'])->name('teacher.announcements.store');
    Route::get('/announcements/{id}/edit', [AnnouncementController::class, 'editTeacher'])->name('announcements.editTeacher');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    
    Route::get('/students/approve', [TeacherController::class, 'approveStudents'])->name('students.approval.list');
    Route::put('/students/{student}/approve', [StudentController::class, 'approve'])->name('students.approve');

    Route::get('/teacher/students', [TeacherController::class, 'myStudents'])->name('teacher.students');

    Route::get('/sf5/{class}', [GradeRemarkController::class, 'download'])->name('sf5.download');
});

/*
|--------------------------------------------------------------------------
| 🧑‍💼 ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin',  'verified'])->group(function () {
    Route::get('/classes', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');

    Route::get('/admin/classes/assign', [AdminController::class, 'assignTeacherForm'])->name('admin.assign-teacher');
    Route::post('/admin/classes/assign', [AdminController::class, 'assignTeacher'])->name('admin.assign-teacher.submit');

    Route::get('/announcements/create', [AnnouncementController::class, 'createAdmin'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'storeAdmin'])->name('announcements.store');
    Route::get('/announcements/{id}/edit', [AnnouncementController::class, 'editAdmin'])->name('announcements.editAdmin');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [UserManagementController::class, 'show'])->name('admin.users.show');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

// ✅ Breeze Auth Routes
require __DIR__ . '/auth.php';
