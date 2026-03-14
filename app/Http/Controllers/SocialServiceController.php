<?php
// ════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/SocialServiceController.php  (REPLACE)
// ════════════════════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Models\SocialAidApplication;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SocialServiceController extends Controller
{
    // Programs catalog
    private function programs(): array
    {
        return [
            [
                'id'          => 1,
                'type'        => 'Student Assistance',
                'title'       => 'Academic Scholarship 2025',
                'amount'      => '₱10,000 / Semester',
                'status'      => 'Open',
                'description' => 'Financial assistance for college and senior high school students from indigent families.',
                'icon'        => '🎓',
                'color'       => 'blue',
                'requirements'=> ['School Registration Form', 'Grade Slip (previous semester)', 'Barangay Indigency Certificate', 'Applicant Valid ID'],
                'fields'      => [
                    ['name' => 'full_name',        'label' => 'Full Name',              'type' => 'text'],
                    ['name' => 'school_name',       'label' => 'School / University',    'type' => 'text'],
                    ['name' => 'course',            'label' => 'Course / Strand',        'type' => 'text'],
                    ['name' => 'year_level',        'label' => 'Year Level',             'type' => 'select', 'options' => ['1st Year', '2nd Year', '3rd Year', '4th Year', 'Grade 11', 'Grade 12']],
                    ['name' => 'gpa',               'label' => 'General Average (Last Sem)', 'type' => 'number'],
                    ['name' => 'monthly_income',    'label' => 'Household Monthly Income', 'type' => 'number'],
                    ['name' => 'household_members', 'label' => 'Number of Household Members', 'type' => 'number'],
                    ['name' => 'contact_number',    'label' => 'Contact Number',         'type' => 'text'],
                    ['name' => 'address',           'label' => 'Complete Address',       'type' => 'textarea'],
                    ['name' => 'reason',            'label' => 'Why do you need this assistance?', 'type' => 'textarea'],
                ],
            ],
            [
                'id'          => 2,
                'type'        => 'Senior Citizen',
                'title'       => 'Senior Citizen Monthly Pension',
                'amount'      => '₱1,500 / Month',
                'status'      => 'Open',
                'description' => 'Monthly financial assistance for senior citizens aged 60 and above.',
                'icon'        => '👴',
                'color'       => 'emerald',
                'requirements'=> ['Senior Citizen ID', 'Birth Certificate', 'Barangay Clearance', '1x1 ID Photo'],
                'fields'      => [
                    ['name' => 'full_name',    'label' => 'Full Name',          'type' => 'text'],
                    ['name' => 'date_of_birth','label' => 'Date of Birth',      'type' => 'date'],
                    ['name' => 'age',          'label' => 'Age',                'type' => 'number'],
                    ['name' => 'sex',          'label' => 'Sex',                'type' => 'select', 'options' => ['Male', 'Female']],
                    ['name' => 'civil_status', 'label' => 'Civil Status',       'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated']],
                    ['name' => 'sc_id_number', 'label' => 'Senior Citizen ID No.', 'type' => 'text'],
                    ['name' => 'contact_number','label' => 'Contact Number',    'type' => 'text'],
                    ['name' => 'address',      'label' => 'Complete Address',   'type' => 'textarea'],
                    ['name' => 'contact_person','label' => 'Emergency Contact Person', 'type' => 'text'],
                ],
            ],
            [
                'id'          => 3,
                'type'        => 'PWD Assistance',
                'title'       => 'PWD Financial Assistance',
                'amount'      => '₱5,000 One-time',
                'status'      => 'Open',
                'description' => 'One-time financial assistance for Persons with Disability (PWD).',
                'icon'        => '♿',
                'color'       => 'purple',
                'requirements'=> ['PWD ID', 'Medical Certificate', 'Barangay Clearance', 'Proof of Income'],
                'fields'      => [
                    ['name' => 'full_name',       'label' => 'Full Name',          'type' => 'text'],
                    ['name' => 'date_of_birth',   'label' => 'Date of Birth',      'type' => 'date'],
                    ['name' => 'disability_type', 'label' => 'Type of Disability', 'type' => 'select',
                        'options' => ['Physical', 'Visual', 'Hearing', 'Intellectual', 'Psychosocial', 'Learning', 'Multiple']],
                    ['name' => 'pwd_id_number',   'label' => 'PWD ID Number',      'type' => 'text'],
                    ['name' => 'monthly_income',  'label' => 'Monthly Income',     'type' => 'number'],
                    ['name' => 'contact_number',  'label' => 'Contact Number',     'type' => 'text'],
                    ['name' => 'address',         'label' => 'Complete Address',   'type' => 'textarea'],
                    ['name' => 'purpose',         'label' => 'Purpose of Assistance', 'type' => 'textarea'],
                ],
            ],
            [
                'id'          => 4,
                'type'        => 'Solo Parent',
                'title'       => 'Solo Parent Welfare Assistance',
                'amount'      => '₱3,000 / Quarter',
                'status'      => 'Open',
                'description' => 'Quarterly financial support for registered solo parents.',
                'icon'        => '👩‍👧',
                'color'       => 'rose',
                'requirements'=> ['Solo Parent ID', 'Birth Certificate of Child/ren', 'Barangay Clearance', 'Proof of Income'],
                'fields'      => [
                    ['name' => 'full_name',      'label' => 'Full Name',            'type' => 'text'],
                    ['name' => 'sp_id_number',   'label' => 'Solo Parent ID No.',   'type' => 'text'],
                    ['name' => 'number_children','label' => 'Number of Children',   'type' => 'number'],
                    ['name' => 'reason_solo',    'label' => 'Reason for Being Solo Parent',
                        'type' => 'select', 'options' => ['Abandoned', 'Separated', 'Widowed', 'Unmarried']],
                    ['name' => 'monthly_income', 'label' => 'Monthly Income',       'type' => 'number'],
                    ['name' => 'contact_number', 'label' => 'Contact Number',       'type' => 'text'],
                    ['name' => 'address',        'label' => 'Complete Address',     'type' => 'textarea'],
                ],
            ],
            [
                'id'          => 5,
                'type'        => 'Calamity Aid',
                'title'       => 'Emergency Disaster Relief',
                'amount'      => 'Based on Assessment',
                'status'      => 'Open',
                'description' => 'Emergency financial and material assistance for disaster-affected families.',
                'icon'        => '🆘',
                'color'       => 'orange',
                'requirements'=> ['Proof of Address', 'Photo of Damaged Property (if applicable)', 'Barangay Certification'],
                'fields'      => [
                    ['name' => 'full_name',         'label' => 'Full Name',                   'type' => 'text'],
                    ['name' => 'disaster_type',     'label' => 'Type of Disaster Experienced', 'type' => 'select',
                        'options' => ['Flood', 'Typhoon', 'Fire', 'Landslide', 'Earthquake', 'Other']],
                    ['name' => 'incident_date',     'label' => 'Date of Incident',             'type' => 'date'],
                    ['name' => 'damage_description','label' => 'Description of Damages',       'type' => 'textarea'],
                    ['name' => 'household_members', 'label' => 'Number of Affected Household Members', 'type' => 'number'],
                    ['name' => 'contact_number',    'label' => 'Contact Number',               'type' => 'text'],
                    ['name' => 'address',           'label' => 'Affected Address',             'type' => 'textarea'],
                ],
            ],
        ];
    }

    public function index()
    {
        $myApplications = auth()->check()
            ? SocialAidApplication::where('user_id', auth()->id())
                ->latest()->get()
                ->map(fn($a) => [
                    'id'            => $a->id,
                    'tracking_code' => $a->tracking_code,
                    'program_title' => $a->program_title,
                    'program_type'  => $a->program_type,
                    'status'        => $a->status,
                    'approved_amount'=> $a->approved_amount,
                    'release_date'  => $a->release_date?->format('F j, Y'),
                    'release_venue' => $a->release_venue,
                    'created_at'    => $a->created_at?->diffForHumans(),
                ])
            : [];

        return Inertia::render('Social/Index', [
            'programs'       => $this->programs(),
            'myApplications' => $myApplications,
        ]);
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'program_type'    => 'required|string',
            'program_title'   => 'required|string',
            'application_data'=> 'required|array',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'nullable|file|max:5120',
        ]);

        // Save attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store("social-aid/{$validated['program_type']}", 'public');
            }
        }

        SocialAidApplication::create([
            'user_id'          => auth()->id(),
            'program_type'     => $validated['program_type'],
            'program_title'    => $validated['program_title'],
            'application_data' => $validated['application_data'],
            'attachments'      => $attachmentPaths ?: null,
            'status'           => 'pending',
        ]);

        return redirect()->route('social.index')
            ->with('success', '✅ Application submitted! You will be notified once reviewed.');
    }
}