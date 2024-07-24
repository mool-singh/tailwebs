<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Models\Student;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    $students = Student::where('user_id',auth()->id())->count('id');
   
    return view('dashboard',compact('students'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('students',StudentController::class);

});

require __DIR__.'/auth.php';
