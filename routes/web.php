<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceScanController::class, 'public'])->name('scan.public');
Route::post('/scan', [AttendanceScanController::class, 'store'])->name('scan.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AppController::class, 'dashboard'])->name('dashboard');

    Route::get('/students', [AppController::class, 'students'])->name('students.index');
    Route::get('/students/create', [AppController::class, 'studentForm'])->name('students.create');
    Route::post('/students', [AppController::class, 'saveStudent'])->name('students.store');
    Route::get('/students/{student}/edit', [AppController::class, 'studentForm'])->name('students.edit');
    Route::put('/students/{student}', [AppController::class, 'saveStudent'])->name('students.update');

    Route::get('/classes', [AppController::class, 'classes'])->name('classes.index');
    Route::get('/classes/create', [AppController::class, 'classForm'])->name('classes.create');
    Route::get('/classes/{class}/edit', [AppController::class, 'classForm'])->name('classes.edit');
    Route::post('/classes', [AppController::class, 'saveClass'])->name('classes.store');
    Route::put('/classes/{class}', [AppController::class, 'saveClass'])->name('classes.update');
    Route::delete('/classes/{class}', [AppController::class, 'destroyClass'])->name('classes.destroy');

    Route::get('/users', [AppController::class, 'users'])->name('users.index');
    Route::post('/users', [AppController::class, 'saveUser'])->name('users.store');

    Route::get('/cards', [AppController::class, 'cards'])->name('cards.index');
    Route::get('/my-card', [AppController::class, 'myCard'])->name('cards.mine');

    Route::get('/scan-internal', [AttendanceScanController::class, 'internal'])->name('scan.internal');
    Route::get('/manual-attendance', [AppController::class, 'manual'])->name('attendance.manual');
    Route::post('/manual-attendance', [AppController::class, 'saveManual'])->name('attendance.manual.store');

    Route::get('/student-history', [AppController::class, 'history'])->name('student.history');

    Route::get('/reports', [AppController::class, 'reports'])->name('reports.index');
    Route::get('/reports/print', [AppController::class, 'printReport'])->name('reports.print');
    Route::get('/reports/excel', [AppController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/pdf', [AppController::class, 'exportPdf'])->name('reports.pdf');

    Route::get('/subjects', [AppController::class, 'subjects'])->name('subjects.index');
    Route::post('/subjects', [AppController::class, 'saveSubject'])->name('subjects.store');
    Route::put('/subjects/{subject}', [AppController::class, 'saveSubject'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [AppController::class, 'destroySubject'])->name('subjects.destroy');

    Route::get('/grades', [AppController::class, 'grades'])->name('grades.index');
    Route::post('/grades', [AppController::class, 'saveGrades'])->name('grades.store');
    Route::get('/grades/print', [AppController::class, 'printGrades'])->name('grades.print');

    Route::get('/promotion', [AppController::class, 'promotion'])->name('promotion.index');
    Route::post('/promotion/decisions', [AppController::class, 'savePromotionDecisions'])->name('promotion.decisions');
    Route::post('/promotion/process', [AppController::class, 'processPromotion'])->name('promotion.process');

    Route::get('/settings', [AppController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [AppController::class, 'saveSettings'])->name('settings.store');
});
