<?php

namespace App\Services;

use App\Models\BarangayProfile;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    /**
     * Check if user has a barangay profile
     */
    public function hasProfile(int $userId): bool
    {
        return BarangayProfile::where('user_id', $userId)->exists();
    }

    /**
     * Get user's barangay profile
     */
    public function getProfile(int $userId): ?BarangayProfile
    {
        return BarangayProfile::where('user_id', $userId)->first();
    }

    /**
     * Get profile data formatted for form pre-filling
     */
    public function getProfileForForm(int $userId): ?array
    {
        $profile = $this->getProfile($userId);
        return $profile ? $profile->toFormData() : null;
    }

    /**
     * Create or update profile from document request data
     * 
     * @param int $userId
     * @param array $requestData - The form data from document request
     * @param bool $isQuickSubmit - If true, don't update profile (it's already using stored data)
     * @return BarangayProfile|null
     */
    public function createOrUpdateProfile(int $userId, array $requestData, bool $isQuickSubmit = false): ?BarangayProfile
    {
        // If it's a quick submit, don't update profile (user is using existing data)
        if ($isQuickSubmit) {
            $profile = $this->getProfile($userId);
            if ($profile) {
                $profile->markAsUsed();
            }
            return $profile;
        }

        // Extract profile fields from request data
        $profileData = BarangayProfile::extractProfileFields($requestData);

        // Only create/update if we have the minimum required fields
        if (empty($profileData['applicant_first_name']) || empty($profileData['applicant_last_name'])) {
            Log::warning("Insufficient data to create profile for user {$userId}");
            return null;
        }

        try {
            $profile = BarangayProfile::createOrUpdateFromRequest($userId, $requestData);
            $profile->markAsUsed();
            
            Log::info("Profile created/updated for user {$userId}");
            return $profile;
        } catch (\Exception $e) {
            Log::error("Failed to create/update profile for user {$userId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Merge profile data with request-specific data
     * Used for quick submit to combine stored profile with new document-specific fields
     */
    public function mergeProfileWithRequest(int $userId, array $requestData): array
    {
        $profile = $this->getProfile($userId);
        
        if (!$profile) {
            return $requestData; // No profile to merge, return original
        }

        // Get profile data
        $profileData = $profile->toFormData();

        // Merge: profile data as base, request data overwrites
        $merged = array_merge($profileData, $requestData);

        Log::info("Merged profile data for user {$userId}");
        return $merged;
    }

    /**
     * Check if profile needs attention (outdated or incomplete)
     */
    public function needsAttention(int $userId): array
    {
        $profile = $this->getProfile($userId);
        
        if (!$profile) {
            return [
                'needs_attention' => true,
                'reason' => 'no_profile',
                'message' => 'No profile found. Please complete your profile.',
                'action' => 'create'
            ];
        }

        $completeness = $profile->getCompletenessPercentage();
        
        if ($completeness < 70) {
            return [
                'needs_attention' => true,
                'reason' => 'incomplete',
                'message' => "Profile is only {$completeness}% complete. Please update your information.",
                'action' => 'update',
                'completeness' => $completeness
            ];
        }

        if ($profile->isOutdated()) {
            return [
                'needs_attention' => true,
                'reason' => 'outdated',
                'message' => 'Your profile was last updated over 6 months ago. Please verify your information is still accurate.',
                'action' => 'verify',
                'last_updated' => $profile->updated_at->diffForHumans()
            ];
        }

        return [
            'needs_attention' => false,
            'reason' => 'ok',
            'message' => 'Profile is up to date.',
            'completeness' => $completeness,
            'last_updated' => $profile->updated_at->diffForHumans()
        ];
    }

    /**
     * Get profile statistics for analytics
     */
    public function getStatistics(int $userId): array
    {
        $profile = $this->getProfile($userId);
        
        if (!$profile) {
            return [
                'has_profile' => false,
                'total_requests' => 0,
                'usage_count' => 0,
                'is_verified' => false
            ];
        }

        $totalRequests = DocumentRequest::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->count();

        return [
            'has_profile' => true,
            'total_requests' => $totalRequests,
            'usage_count' => $profile->usage_count,
            'is_verified' => $profile->is_verified,
            'completeness' => $profile->getCompletenessPercentage(),
            'last_used' => $profile->last_used_at?->diffForHumans(),
            'created_at' => $profile->created_at->diffForHumans(),
            'updated_at' => $profile->updated_at->diffForHumans()
        ];
    }

    /**
     * Verify profile (admin action)
     */
    public function verifyProfile(int $userId, int $verifierId): bool
    {
        $profile = $this->getProfile($userId);
        
        if (!$profile) {
            return false;
        }

        try {
            $profile->verify($verifierId);
            Log::info("Profile verified for user {$userId} by admin {$verifierId}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to verify profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete profile (GDPR compliance)
     */
    public function deleteProfile(int $userId): bool
    {
        try {
            $deleted = BarangayProfile::where('user_id', $userId)->delete();
            Log::info("Profile deleted for user {$userId}");
            return $deleted > 0;
        } catch (\Exception $e) {
            Log::error("Failed to delete profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get profile summary for display
     */
    public function getProfileSummary(int $userId): ?array
    {
        $profile = $this->getProfile($userId);
        
        if (!$profile) {
            return null;
        }

        return [
            'full_name' => $profile->full_name,
            'full_address' => $profile->full_address,
            'date_of_birth' => $profile->date_of_birth?->format('F d, Y'),
            'age' => $profile->age_from_birthdate,
            'sex' => $profile->sex,
            'civil_status' => $profile->civil_status,
            'is_verified' => $profile->is_verified,
            'completeness' => $profile->getCompletenessPercentage(),
            'last_updated' => $profile->updated_at->diffForHumans(),
        ];
    }
}