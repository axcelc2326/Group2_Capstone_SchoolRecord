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
    DashboardController
};
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ✅ Default landing page
Route::get('/', function () {
    return Inertia::render('Welcome', [
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
    // Route::get('/parent/dashboard', [ParentController::class, 'index'])->name('parent.dashboard');

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
    // Route::get('/teacher/dashboard', [TeacherController::class, 'index'])->name('teacher.dashboard');

    Route::get('/grades/enter/{student}', [GradeController::class, 'create'])->name('grades.create');
    Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');
    
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
    // Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');

    Route::get('/admin/classes/assign', [AdminController::class, 'assignTeacherForm'])->name('admin.assign-teacher');
    Route::post('/admin/classes/assign', [AdminController::class, 'assignTeacher'])->name('admin.assign-teacher.submit');

    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

// ✅ Breeze Auth Routes
require __DIR__ . '/auth.php';
