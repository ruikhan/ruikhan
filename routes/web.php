<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketPriceController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DocumentRequestController;
use App\Models\DocumentRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Foundation\Application;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ConcernController;
use Inertia\Inertia;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\BillPaymentController;
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
use App\Http\Controllers\NotificationController;

use App\Http\Middleware\AdminMiddleware; 
use App\Http\Middleware\EnsureUserIsBusiness;

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
    Route::get('/bpo-coach', function () {
        return Inertia::render('BPOCoach');
    })->name('bpo.coach');
// ============================================================================
// ✅ ROLE-BASED DASHBOARDS (Must be before generic /dashboard)
// ============================================================================

// ✅ ADMIN DASHBOARD & ROUTES
Route::middleware(['auth', 'verified', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Command Center
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Document Management
    Route::get('/documents', [AdminDocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{id}', [AdminDocumentController::class, 'show'])->name('documents.show');
    Route::patch('/documents/{id}', [AdminDocumentController::class, 'update'])->name('documents.update');
    
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
});

// ✅ BUSINESS OWNER DASHBOARD & ROUTES
Route::middleware(['auth', 'verified', EnsureUserIsBusiness::class])->prefix('business')->name('business.')->group(function () {
    
    // Business Dashboard
    Route::get('/dashboard', [BusinessDashboardController::class, 'index'])->name('dashboard');
    Route::patch('/status', [BusinessDashboardController::class, 'updateStatus'])->name('update-status');
    Route::patch('/update', [BusinessDashboardController::class, 'update'])->name('update');
    
    // Product Management
    Route::resource('products', ProductController::class);
    Route::patch('/products/{product}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
    
    // Job Postings (Business Owners Only)
    Route::get('/jobs/create', [JobPostingController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobPostingController::class, 'store'])->name('jobs.store');
});

// ✅ RESIDENT DASHBOARD (Default for regular users)
Route::middleware(['auth', 'verified'])->group(function () {
    // Resident Dashboard (This is the default dashboard for residents)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ============================================================================
// ✅ ONBOARDING (shown first after login for new users)
// ============================================================================
Route::get('/onboarding', function () {
    return Inertia::render('Documentation/ProjectBlueprint'); 
})->middleware(['auth', 'verified'])->name('onboarding');

// ============================================================================
// ✅ AUTHENTICATED ROUTES (Available to ALL authenticated users)
// ============================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ============================================================================
    // ✅ NOTIFICATION ROUTES
    // ============================================================================
    
    // Web Route - Full notifications page
    Route::get('/notifications', [NotificationController::class, 'page'])->name('notifications.index');
    
    // API Routes - AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });
    
    // Profile Check API Routes
    Route::prefix('api/profile')->name('api.profile.')->group(function () {
        Route::get('/check', function () {
            $profileService = app(\App\Services\ProfileService::class);
            return response()->json([
                'has_profile' => $profileService->hasProfile(auth()->id()),
                'profile_status' => $profileService->needsAttention(auth()->id()),
            ]);
        })->name('check');
        
        Route::get('/summary', function () {
            $profileService = app(\App\Services\ProfileService::class);
            return response()->json([
                'summary' => $profileService->getProfileSummary(auth()->id()),
                'statistics' => $profileService->getStatistics(auth()->id()),
            ]);
        })->name('summary');
    });
    
    // ============================================================================
    // ✅ PROFILE MANAGEMENT
    // ============================================================================
    Route::prefix('profile')->name('profile.')->group(function () {
        // User Profile
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        
        // Barangay Profile Management
        Route::prefix('barangay')->name('barangay.')->group(function () {
            Route::get('/', [DocumentRequestController::class, 'showProfile'])->name('show');
            Route::post('/update', [DocumentRequestController::class, 'updateProfile'])->name('update');
            Route::delete('/delete', [DocumentRequestController::class, 'deleteProfile'])->name('delete');
        });
    });
    
    // ============================================================================
    // ✅ EMERGENCY
    // ============================================================================
    Route::get('/emergency', [EmergencyController::class, 'index'])->name('emergency.index'); 
    Route::post('/emergency', [EmergencyController::class, 'store'])->name('emergency.store');

    // ============================================================================
    // ✅ POLLS
    // ============================================================================
    Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
    Route::post('/polls/vote', [PollController::class, 'vote'])->name('polls.vote');
    Route::post('/polls/create', [PollController::class, 'store'])->name('polls.store');

    // ============================================================================
    // ✅ ISSUES
    // ============================================================================
    Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');

    // ============================================================================
    // ✅ HEALTH
    // ============================================================================
    Route::get('/health', [HealthController::class, 'index'])->name('health.index');
    Route::post('/health/book', [HealthController::class, 'store'])->name('health.store');
    
    // ============================================================================
    // ✅ DOCUMENTATION
    // ============================================================================
    Route::get('/system-documentation', function () { return Inertia::render('System/SystemVisualization'); })->name('system.docs');
    Route::get('/system-architecture', function () { return Inertia::render('Documentation/SystemArchitecture'); })->name('system.architecture');

    // ============================================================================
    // ✅ MARKET PRICES
    // ============================================================================
    Route::get('/market-prices', [MarketPriceController::class, 'index'])->name('market.index');
    Route::post('/market-prices', [MarketPriceController::class, 'store'])->name('market.store');
    Route::put('/market-prices/{id}', [MarketPriceController::class, 'update'])->name('market.update');

    // ============================================================================
    // ✅ ESTABLISHMENTS
    // ============================================================================
    Route::get('/establishments', [EstablishmentController::class, 'index'])->name('establishments.index');
    Route::post('/establishments', [EstablishmentController::class, 'store'])->name('establishments.store');

    // ============================================================================
    // ✅ SMART LGU SERVICES (Dynamic Forms)
    // ============================================================================
    Route::get('/services', function() { return Inertia::render('Services/Landing'); })->name('services.landing');
    Route::get('/services/history', [DocumentRequestController::class, 'index'])->name('services.index');
    Route::get('/services/apply/{department}', [DocumentRequestController::class, 'create'])->name('services.create');
    Route::post('/services/submit', [DocumentRequestController::class, 'store'])->name('services.store');
    Route::resource('request', DocumentRequestController::class);

    Route::get('/my-request/{id}/journey', [DocumentRequestController::class, 'storyboard'])->name('request.story');
    
    // ============================================================================
    // ✅ PROPOSAL / SIMULATION
    // ============================================================================
    Route::get('/simulation', function () {
        return Inertia::render('WorkflowSimulator');
    })->name('simulation');
    
    Route::get('/proposal', function () {
        return Inertia::render('Proposal');
    })->name('proposal');
    
    Route::get('/training', function () {
        return Inertia::render('TrainingAssessment');
    })->name('training');
    
    Route::get('/prototype', function () {
        return Inertia::render('Prototype');
    })->name('prototype');

    // ============================================================================
    // ✅ JOBS (View jobs - available to all)
    // ============================================================================
    Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');

    // ============================================================================
    // ✅ ENVIRONMENT (Green Guard)
    // ============================================================================
    Route::get('/environment', [EnvironmentController::class, 'index'])->name('environment.index');
    Route::post('/environment/report', [EnvironmentController::class, 'store'])->name('environment.store');

    // ============================================================================
    // ✅ BOOKINGS
    // ============================================================================
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // ============================================================================
    // ✅ SOCIAL SERVICES
    // ============================================================================
    Route::get('/social-services', [SocialServiceController::class, 'index'])->name('social.index');
    Route::post('/social-services/apply', [SocialServiceController::class, 'store'])->name('social.store');


// Add this rout

    // ============================================================================
    // ✅ BILLS & PAYMENTS
    // ============================================================================
    Route::get('/bills/pay', [BillPaymentController::class, 'create'])->name('bills.create');
    Route::post('/bills', [BillPaymentController::class, 'store'])->name('bills.store');
    Route::get('/bills/history', [BillPaymentController::class, 'index'])->name('bills.index');

    // ============================================================================
    // ✅ MARKETPLACE
    // ============================================================================
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/marketplace/{id}', [MarketplaceController::class, 'show'])->name('marketplace.show');
    Route::post('/marketplace/{id}/like', [MarketplaceController::class, 'toggleLike'])->name('marketplace.like');
    Route::post('/marketplace/{id}/review', [MarketplaceController::class, 'submitReview'])->name('marketplace.review');

    // ============================================================================
    // ✅ BUSINESS REGISTRATION (For users who want to become business owners)
    // ============================================================================
    Route::middleware('can.register.business')->group(function () {
        Route::get('/business/register', [BusinessRegistrationController::class, 'create'])->name('business.register');
        Route::post('/business/register', [BusinessRegistrationController::class, 'store'])->name('business.store');
    });
});

// ============================================================================
// ✅ PUBLIC VERIFICATION ROUTE (No auth required)
// ============================================================================
Route::get('/documents/verify/{id}', function ($id) {
    $document = DocumentRequest::with('user')->findOrFail($id);
    
    if($document->status !== 'completed') {
        abort(404, 'Document not valid or not yet issued.');
    }

    return Inertia::render('Documents/Verify', [
        'document' => $document,
    ]);
})->name('documents.verify');

require __DIR__.'/auth.php';