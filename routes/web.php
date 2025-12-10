<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentRequestController;
use App\Models\DocumentRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConcernController;
use Inertia\Inertia;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\BillPaymentController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\SocialServiceController;
use App\Http\Controllers\EmergencyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/documents', [DocumentRequestController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentRequestController::class, 'store'])->name('documents.store');
    Route::get('/concerns/report', [ConcernController::class, 'create'])->name('concerns.create');
    Route::post('/concerns', [ConcernController::class, 'store'])->name('concerns.store');
    Route::get('/emergency', [EmergencyController::class, 'index'])->name('emergency.index');
    Route::post('/emergency/sos', [EmergencyController::class, 'store'])->name('emergency.store');
        Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');
    // --- ENVIRONMENT (Green Guard) ---
Route::get('/environment', [EnvironmentController::class, 'index'])->name('environment.index');
Route::post('/environment/report', [EnvironmentController::class, 'store'])->name('environment.store');

// --- SOCIAL SERVICES (Care Portal) ---
Route::get('/social-services', [SocialServiceController::class, 'index'])->name('social.index');
Route::post('/social-services/apply', [SocialServiceController::class, 'store'])->name('social.store');
    Route::get('/health', [HealthController::class, 'index'])->name('health.index');
    Route::post('/health/book', [HealthController::class, 'store'])->name('health.store');
    // Temporary Admin Route
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');


// --- RESTRICTED ROUTES (Only Business Owners can post) ---
Route::middleware(['auth', 'business'])->group(function () {
    Route::get('/jobs/create', [JobPostingController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobPostingController::class, 'store'])->name('jobs.store');
});
Route::get('/bills/pay', [BillPaymentController::class, 'create'])->name('bills.create');
Route::post('/bills', [BillPaymentController::class, 'store'])->name('bills.store');
Route::get('/bills/history', [BillPaymentController::class, 'index'])->name('bills.history');
// --- ADMIN OFFICIALS AREA ---
Route::prefix('admin')->group(function () {
    
    // Document Processing Center
    Route::get('/documents', [AdminDocumentController::class, 'index'])->name('admin.documents.index');
    Route::patch('/documents/{id}', [AdminDocumentController::class, 'update'])->name('admin.documents.update');
    Route::get('/documents/{id}', [AdminDocumentController::class, 'show'])->name('admin.documents.show');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
});
// PUBLIC VERIFICATION ROUTE
Route::get('/documents/verify/{id}', function ($id) {
    
    // 1. Find the document (must be completed)
    $document = DocumentRequest::with('user')->findOrFail($id);
    
    if($document->status !== 'completed') {
        abort(404, 'Document not found or not yet issued.');
    }

    // 2. Generate the QR Code (URL to this same page)
    // We generate an SVG image string
    $qrCode = QrCode::size(200)->generate(route('documents.verify', $id));

    // 3. Show the Certificate Page
    return Inertia::render('Documents/Verify', [
        'document' => $document,
        'qrCode' => $qrCode // Pass the SVG to the frontend
    ]);
})->name('documents.verify');
});


require __DIR__.'/auth.php';
