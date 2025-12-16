<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarketPriceController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DocumentRequestController;
use App\Models\DocumentRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IssueController;
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
use App\Http\Controllers\BusinessRegistrationController;
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome/Landing Page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ✅ ONBOARDING: Terms/Blueprint page (shown first after login)
Route::get('/onboarding', function () {
    return Inertia::render('Documentation/ProjectBlueprint'); 
})->middleware(['auth', 'verified'])->name('onboarding');

// ✅ DASHBOARD: The actual Command Center (accessed after accepting terms)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
    Route::post('/polls/vote', [PollController::class, 'vote'])->name('polls.vote');
    Route::post('/polls/create', [PollController::class, 'store'])->name('polls.store');

    Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');

    Route::get('/health', [HealthController::class, 'index'])->name('health.index');
    Route::post('/health/book', [HealthController::class, 'store'])->name('health.store');
    
    Route::get('/system-documentation', function () {
        return Inertia::render('System/SystemVisualization');
    })->name('system.docs');

    // --- DOCUMENTATION ROUTES ---
    Route::get('/system-architecture', function () {
        return Inertia::render('Documentation/SystemArchitecture');
    })->name('system.architecture');

    Route::get('/project-blueprint', function () {
        return Inertia::render('Documentation/ProjectBlueprint');
    })->name('project.blueprint');

    // --- MARKET PRICES ROUTES ---
    Route::get('/market-prices', [MarketPriceController::class, 'index'])->name('market.index');
    Route::post('/market-prices', [MarketPriceController::class, 'store'])->name('market.store');
    Route::put('/market-prices/{id}', [MarketPriceController::class, 'update'])->name('market.update');

    Route::get('/establishments', [EstablishmentController::class, 'index'])->name('establishments.index');
    Route::post('/establishments', [EstablishmentController::class, 'store'])->name('establishments.store');

    // --- DOCUMENTS SECTION ---
    Route::get('/documents', [DocumentRequestController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentRequestController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentRequestController::class, 'store'])->name('documents.store');

    Route::get('/concerns/report', [ConcernController::class, 'create'])->name('concerns.create');
    Route::post('/concerns', [ConcernController::class, 'store'])->name('concerns.store');

    Route::get('/emergency', [EmergencyController::class, 'index'])->name('emergency.index');
    Route::post('/emergency/sos', [EmergencyController::class, 'store'])->name('emergency.store');

    Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');

    // --- ENVIRONMENT (Green Guard) ---
    Route::get('/environment', [EnvironmentController::class, 'index'])->name('environment.index');
    Route::post('/environment/report', [EnvironmentController::class, 'store'])->name('environment.store');

    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // --- SOCIAL SERVICES ---
    Route::get('/social-services', [SocialServiceController::class, 'index'])->name('social.index');
    Route::post('/social-services/apply', [SocialServiceController::class, 'store'])->name('social.store');

    // --- BILLS & PAYMENTS ---
    Route::get('/bills/pay', [BillPaymentController::class, 'create'])->name('bills.create');
    Route::post('/bills', [BillPaymentController::class, 'store'])->name('bills.store');
    Route::get('/bills/history', [BillPaymentController::class, 'index'])->name('bills.index');

    // --- MARKETPLACE (PUBLIC ACCESS FOR ALL AUTHENTICATED USERS) ---
    // Browse all businesses
    Route::get('/marketplace', [MarketplaceController::class, 'index'])
        ->name('marketplace.index');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    // View individual business details
    Route::get('/marketplace/{id}', [MarketplaceController::class, 'show'])
        ->name('marketplace.show');
    
    // Like/Unlike a business
    Route::post('/marketplace/{id}/like', [MarketplaceController::class, 'toggleLike'])
        ->name('marketplace.like');
    
    // Submit a review
    Route::post('/marketplace/{id}/review', [MarketplaceController::class, 'submitReview'])
        ->name('marketplace.review');

    // --- RESTRICTED ROUTES (Business Owners Only) ---
    Route::middleware('business')->group(function () {
        Route::get('/jobs/create', [JobPostingController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [JobPostingController::class, 'store'])->name('jobs.store');
    });

    // --- ADMIN OFFICIALS AREA ---
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/documents', [AdminDocumentController::class, 'index'])->name('admin.documents.index');
        Route::patch('/documents/{id}', [AdminDocumentController::class, 'update'])->name('admin.documents.update');
        Route::get('/documents/{id}', [AdminDocumentController::class, 'show'])->name('admin.documents.show');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    });

    // --- PUBLIC VERIFICATION ROUTE ---
    Route::get('/documents/verify/{id}', function ($id) {
        $document = DocumentRequest::with('user')->findOrFail($id);
        
        if($document->status !== 'completed') {
            abort(404, 'Document not found or not yet issued.');
        }

        $qrCode = QrCode::size(200)->generate(route('documents.verify', $id));

        return Inertia::render('Documents/Verify', [
            'document' => $document,
            'qrCode' => $qrCode 
        ]);
    })->name('documents.verify');

    // --- BUSINESS INCUBATION MODULE ---
    
    // 🔹 Registration Portal (Protected: Only residents without active business)
    Route::middleware('can.register.business')->group(function () {
        Route::get('/business/register', [BusinessRegistrationController::class, 'create'])
            ->name('business.register');
        
        Route::post('/business/register', [BusinessRegistrationController::class, 'store'])
            ->name('business.store');
    });

    // 🔹 Business Owner Dashboard & Management (Protected: Must have active business + subscription)
    Route::middleware('has.business')->prefix('business')->name('business.')->group(function () {
        // Main Dashboard
        Route::get('/dashboard', [BusinessDashboardController::class, 'index'])
            ->name('dashboard');
        
        // Update Business Status (Available, Open, Closed, Full)
        Route::patch('/status', [BusinessDashboardController::class, 'updateStatus'])
            ->name('update-status');
        
        // Update Business Information
        Route::patch('/update', [BusinessDashboardController::class, 'update'])
            ->name('update');
        

            Route::resource('products', ProductController::class);
            Route::patch('/products/{product}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');

        Route::get('/inventory', [BusinessInventoryController::class, 'index'])->name('inventory');
        Route::get('/orders', [BusinessOrderController::class, 'index'])->name('orders');
        Route::get('/reviews', [BusinessReviewController::class, 'index'])->name('reviews');
        Route::get('/analytics', [BusinessAnalyticsController::class, 'index'])->name('analytics');
    });
});

require __DIR__.'/auth.php';