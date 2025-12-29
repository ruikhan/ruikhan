<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Events\NewDocumentRequest; 
use Illuminate\Support\Facades\Storage;

class DocumentRequestController extends Controller
{
    // =========================================================================
    // 1. RESIDENT VIEW: My History & Dashboard
    // =========================================================================
    public function index()
    {
        $myRequests = DocumentRequest::where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return Inertia::render('Services/History', [
            'requests' => $myRequests
        ]);
    }

    /** * ✅ ADDED: THE STORYBOARD VIEWER
     * Visualizes the request journey (The "Graphic Novel" view)
     */
    public function storyboard($id)
    {
        // 1. Find the request or fail
        $request = DocumentRequest::findOrFail($id);

        // 2. Security Check: Prevent users from seeing others' requests
        if ($request->user_id !== auth()->id()) {
            abort(403, 'ACCESS DENIED: You do not have permission to view this dossier.');
        }

        // 3. Render the Cinematic Page
        return Inertia::render('User/RequestStory', [
            'docRequest' => $request
        ]);
    }

    // =========================================================================
    // 2. THE ENGINE: Dynamic Form Configuration
    // =========================================================================
    public function create($department = null)
    {
        if (!$department) {
            return redirect()->route('services.index'); 
        }

        // This Configuration Map defines the "Shape" of every request.
        // It tells the Frontend what fields to render.
        $specs = $this->getServiceSpecifications();

        // Retrieve config or use a Default Fallback if department not found
        $config = $specs[$department] ?? [
            'title' => $department . ' Request',
            'description' => 'General request form.',
            'icon' => '📄',
            'types' => ['General Request'],
            'fields' => [
                ['name' => 'details', 'label' => 'Request Details', 'type' => 'textarea']
            ]
        ];

        return Inertia::render('Services/Create', [
            'departmentKey' => $department,
            'config' => $config
        ]); 
    }

    // =========================================================================
    // 3. THE HANDLER: Storing Dynamic Data
    // =========================================================================
    public function store(Request $request)
    {
        // 1. Validate the Base Fields
        $request->validate([
            'department' => 'required|string',
            'document_type' => 'required|string',
            'data' => 'required|array',
            'remarks' => 'nullable|string',
            'attachments' => 'nullable|file|max:10240',
        ]);

        $user = $request->user();
        $attachmentsPath = null;

        // 2. Handle File Upload
        if ($request->hasFile('attachments')) {
            $attachmentsPath = $request->file('attachments')->store('documents/' . $user->id, 'public');
        }

        // 3. Generate Smart Tracking Code
        $deptPrefix = $this->getDepartmentPrefix($request->department);
        $trackingCode = $deptPrefix . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        // 4. Create the Record
        $newRequest = DocumentRequest::create([
            'user_id' => $user->id,
            'tracking_code' => $trackingCode,
            'department' => $request->department,
            'document_type' => $request->document_type, 
            'data' => $request->data,
            'attachments' => $attachmentsPath, 
            'status' => 'pending',
            'user_remarks' => $request->remarks, 
        ]);

        // 5. Fire Event (Sends Email/Notification to Admin)
        // event(new NewDocumentRequest($newRequest));

        // 6. Redirect with Success
        return redirect()->route('request.story', $newRequest->id)
            ->with('success', 'Application submitted! Tracking initialized.');
    }

    // =========================================================================
    // 4. HELPERS
    // =========================================================================

    private function getDepartmentPrefix($dept) {
        $map = [
            'Municipal Civil Registrar' => 'MCR',
            'Municipal Health Office' => 'MHO',
            'Business Permits (BPLO)' => 'BPLO',
            'Engineering Office' => 'ENG',
            'Agriculture Office' => 'AGRI',
            'Social Welfare (MSWDO)' => 'DSWD',
            'Treasurer’s Office' => 'TREAS',
            'Assessor’s Office' => 'ASSR',
            'MPDO' => 'MPDO',
            'MDRRMO' => 'DRRM',
            'Barangay Certifications' => 'BRGY',
        ];
        return $map[$dept] ?? 'REQ';
    }

    // Helper to get common barangay fields (used across all certificate types)
    private function getCommonBarangayFields() {
        return [
            // Request Level
            ['name' => 'request_level', 'label' => 'Request Level', 'type' => 'select', 'options' => ['Municipal LGU', 'Barangay']],
            
            // Personal Information
            ['name' => 'applicant_last_name', 'label' => 'Last Name', 'type' => 'text'],
            ['name' => 'applicant_first_name', 'label' => 'First Name', 'type' => 'text'],
            ['name' => 'applicant_middle_name', 'label' => 'Middle Name / Initial', 'type' => 'text'],
            ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'],
            ['name' => 'age', 'label' => 'Age', 'type' => 'number'],
            ['name' => 'sex', 'label' => 'Sex', 'type' => 'select', 'options' => ['Male', 'Female']],
            ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated']],
            
            // Complete Address
            ['name' => 'purok_street', 'label' => 'Purok / Street', 'type' => 'text'],
            ['name' => 'barangay', 'label' => 'Barangay', 'type' => 'text'],
            ['name' => 'municipality', 'label' => 'City/Municipality', 'type' => 'text'],
            ['name' => 'province', 'label' => 'Province', 'type' => 'text'],
        ];
    }

    // Extracted the massive array to a function to keep the controller clean
    private function getServiceSpecifications() {
        return [
            // 1. CIVIL REGISTRAR
            'Municipal Civil Registrar' => [
                'title' => 'Civil Registrar Services',
                'description' => 'Request Birth, Marriage, or Death Certificates.',
                'icon' => '📜',
                'types' => ['Birth Certificate', 'Marriage Certificate', 'Death Certificate', 'CENOMAR'],
                'fields' => [
                    ['name' => 'subject_last_name', 'label' => 'Last Name (of Subject)', 'type' => 'text'],
                    ['name' => 'subject_first_name', 'label' => 'First Name (of Subject)', 'type' => 'text'],
                    ['name' => 'subject_middle_name', 'label' => 'Middle Name (of Subject)', 'type' => 'text'],
                    ['name' => 'date_of_event', 'label' => 'Date of Birth / Marriage / Death', 'type' => 'date'],
                    ['name' => 'place_of_event', 'label' => 'Place of Birth / Marriage / Death', 'type' => 'text'],
                    ['name' => 'mother_maiden_name', 'label' => 'Mother’s Maiden Name', 'type' => 'text'],
                    ['name' => 'father_name', 'label' => 'Father’s Full Name', 'type' => 'text'],
                    ['name' => 'relationship', 'label' => 'Relationship to Subject', 'type' => 'text'],
                    ['name' => 'copies', 'label' => 'Number of Copies', 'type' => 'number'],
                    ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'select', 'options' => ['Passport', 'School', 'Employment', 'Claims', 'Other']]
                ]
            ],

            // 2. ASSESSOR'S OFFICE
            'Assessor’s Office' => [
                'title' => 'Assessor’s Office',
                'description' => 'Community Tax Certificate (Cedula) and Assessment.',
                'icon' => '🏡',
                'types' => ['Cedula (Individual)', 'Cedula (Business)', 'Tax Payment', 'Property Assessment'],
                'fields' => [
                    ['name' => 'birth_place', 'label' => 'Place of Birth', 'type' => 'text'],
                    ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated']],
                    ['name' => 'citizenship', 'label' => 'Citizenship', 'type' => 'text'],
                    ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
                    ['name' => 'income', 'label' => 'Monthly Income (Individual)', 'type' => 'number'],
                    ['name' => 'gross_receipts', 'label' => 'Gross Receipts (Business)', 'type' => 'number'],
                    ['name' => 'tin', 'label' => 'TIN (if applicable)', 'type' => 'text'],
                    ['name' => 'purpose', 'label' => 'Purpose of Cedula', 'type' => 'text'],
                ]
            ],

            // 3. MPDO (Planning)
            'MPDO' => [
                'title' => 'Planning & Development (MPDO)',
                'description' => 'Zoning Clearances and Development Planning.',
                'icon' => '🗺️',
                'types' => ['Zoning Clearance', 'Development Permit', 'Locational Clearance'],
                'fields' => [
                    ['name' => 'project_location', 'label' => 'Project Location / Address', 'type' => 'text'],
                    ['name' => 'lot_number', 'label' => 'Lot / Parcel Number', 'type' => 'text'],
                    ['name' => 'development_type', 'label' => 'Type of Development', 'type' => 'select', 'options' => ['Residential', 'Commercial', 'Institutional', 'Industrial', 'Agricultural']],
                    ['name' => 'purpose', 'label' => 'Purpose of Clearance', 'type' => 'text'],
                ]
            ],

            // 4. HEALTH OFFICE
            'Municipal Health Office' => [
                'title' => 'Health & Sanitation',
                'description' => 'Health Certificates and Medical Services.',
                'icon' => '🩺',
                'types' => ['Health Certificate', 'Medical Certificate', 'Sanitary Permit'],
                'fields' => [
                    ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
                    ['name' => 'employer', 'label' => 'Employer / Business Name', 'type' => 'text'],
                    ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated']],
                    ['name' => 'purpose', 'label' => 'Purpose of Certificate', 'type' => 'text'],
                ]
            ],

            // 5. MSWDO (Social Welfare)
            'Social Welfare (MSWDO)' => [
                'title' => 'Social Welfare Services',
                'description' => 'Indigency Certificates and Social Assistance.',
                'icon' => '🤝',
                'types' => ['Certificate of Indigency', 'Financial Assistance', 'Solo Parent ID', 'Senior Citizen ID'],
                'fields' => [
                    ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated']],
                    ['name' => 'dependents', 'label' => 'Number of Dependents', 'type' => 'number'],
                    ['name' => 'income_source', 'label' => 'Source of Income / Monthly Income', 'type' => 'text'],
                    ['name' => 'purpose', 'label' => 'Purpose of Request', 'type' => 'text'],
                ]
            ],

            // 6. BPLO (Business Permits)
            'Business Permits (BPLO)' => [
                'title' => 'Business Licensing Office',
                'description' => 'Business Permits and Mayor’s Permits.',
                'icon' => '💼',
                'types' => ['New Business Permit', 'Business Permit Renewal', 'Mayor’s Permit'],
                'fields' => [
                    ['name' => 'business_name', 'label' => 'Business Trade Name', 'type' => 'text'],
                    ['name' => 'business_address', 'label' => 'Business Address', 'type' => 'text'],
                    ['name' => 'owner_name', 'label' => 'Owner’s Name', 'type' => 'text'],
                    ['name' => 'tin', 'label' => 'Tax Identification Number (TIN)', 'type' => 'text'],
                    ['name' => 'business_type', 'label' => 'Type of Business', 'type' => 'select', 'options' => ['Sole Proprietorship', 'Partnership', 'Corporation', 'Cooperative']],
                    ['name' => 'line_of_business', 'label' => 'Nature/Line of Business', 'type' => 'text'],
                    ['name' => 'capital', 'label' => 'Capitalization (for New Business)', 'type' => 'number'],
                    ['name' => 'gross_sales', 'label' => 'Gross Sales (for Renewals)', 'type' => 'number'],
                    ['name' => 'employees', 'label' => 'Number of Employees', 'type' => 'number'],
                    ['name' => 'barangay_clearance_no', 'label' => 'Barangay Clearance No.', 'type' => 'text'],
                ]
            ],

            // 7. ENGINEERING
            'Engineering Office' => [
                'title' => 'Engineering Office',
                'description' => 'Building Permits and Infrastructure Supervision.',
                'icon' => '🏗️',
                'types' => ['Building Permit', 'Occupancy Permit', 'Electrical Permit', 'Fencing Permit'],
                'fields' => [
                    ['name' => 'project_location', 'label' => 'Project Location', 'type' => 'text'],
                    ['name' => 'lot_number', 'label' => 'Lot / Parcel Number', 'type' => 'text'],
                    ['name' => 'structure_type', 'label' => 'Type of Structure', 'type' => 'select', 'options' => ['Residential', 'Commercial', 'Industrial', 'Institutional']],
                    ['name' => 'floor_area', 'label' => 'Total Floor Area (sq. meters)', 'type' => 'number'],
                    ['name' => 'estimated_cost', 'label' => 'Estimated Cost of Construction', 'type' => 'number'],
                    ['name' => 'contractor', 'label' => 'Contractor Name & License No.', 'type' => 'text'],
                ]
            ],

            // 8. AGRICULTURE
            'Agriculture Office' => [
                'title' => 'Municipal Agriculture Office',
                'description' => 'Farm Support, Seeds, and Input Distribution.',
                'icon' => '🌾',
                'types' => ['Seed Distribution', 'Fertilizer Assistance', 'Livestock Assistance', 'Fishery Support'],
                'fields' => [
                    ['name' => 'barangay', 'label' => 'Farm Address / Barangay', 'type' => 'text'],
                    ['name' => 'farming_type', 'label' => 'Type of Farming', 'type' => 'select', 'options' => ['Crop / Rice / Corn', 'Livestock / Poultry', 'Fishery', 'High Value Crops']],
                    ['name' => 'farm_size', 'label' => 'Farm Size (Hectares/sqm)', 'type' => 'text'],
                    ['name' => 'assistance_needed', 'label' => 'Type of Assistance Requested', 'type' => 'text'],
                    ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'text'],
                ]
            ],

            // 9. MDRRMO (Disaster)
            'MDRRMO' => [
                'title' => 'Disaster Risk Reduction (MDRRMO)',
                'description' => 'Emergency Response and Incident Reporting.',
                'icon' => '🚨',
                'types' => ['Incident Report', 'Request for Assistance', 'Cert of Disaster Effect'],
                'fields' => [
                    ['name' => 'incident_type', 'label' => 'Type of Incident', 'type' => 'select', 'options' => ['Fire', 'Flood', 'Earthquake', 'Vehicular Accident', 'Others']],
                    ['name' => 'incident_datetime', 'label' => 'Date & Time of Incident', 'type' => 'datetime-local'],
                    ['name' => 'damage_description', 'label' => 'Description of Damage / Situation', 'type' => 'text'],
                    ['name' => 'assistance_needed', 'label' => 'Nature of Assistance Requested', 'type' => 'text'],
                ]
            ],
            
            // 10. TREASURER 
            'Treasurer’s Office' => [
                 'title' => 'Municipal Treasurer',
                 'description' => 'Tax Payments and Clearances.',
                 'icon' => '💰',
                 'types' => ['Tax Clearance', 'Cedula Payment', 'Business Tax Payment'],
                 'fields' => [
                    ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated']],
                    ['name' => 'citizenship', 'label' => 'Citizenship', 'type' => 'text'],
                    ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
                    ['name' => 'income', 'label' => 'Monthly Income / Receipts', 'type' => 'number'],
                    ['name' => 'tin', 'label' => 'TIN', 'type' => 'text'],
                    ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'text'],
                 ]
            ],

            // 11. BARANGAY CERTIFICATIONS - TYPE-SPECIFIC CONFIGURATIONS
            'Barangay Certifications' => [
                'title' => 'Barangay Certifications',
                'description' => 'Residency, Indigency, Clearances & Employment Certificates.',
                'icon' => '🏘️',
                'types' => [
                    'Certificate of Residency',
                    'Certificate of Indigency', 
                    'Barangay Clearance',
                    'Business Clearance',
                    'Certificate of Employment (Unemployed)',
                    'First-Time Jobseeker Certificate (RA 11261)',
                    'Tenant/Farmer Certification',
                    'Certificate of Good Moral Character',
                    'Certificate of No Pending Case'
                ],
                // Dynamic fields based on certificate type
                'type_specific_fields' => [
                    'Certificate of Residency' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text'],
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'purpose', 'label' => 'Purpose of Certificate', 'type' => 'select', 'options' => [
                            'Job Application', 'School Enrollment', 'Bank Transaction', 'Government ID', 'Other'
                        ]],
                        ['name' => 'purpose_other', 'label' => 'If Other, please specify', 'type' => 'text'],
                        ['name' => 'recipient_office', 'label' => 'Recipient Office/Organization', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'Driver’s License', 'Passport', 'Voter’s ID', 'SSS/GSIS ID', 'PRC ID', 'Postal ID', 'Other']],
                        ['name' => 'cedula_number', 'label' => 'Cedula/CTC Number', 'type' => 'text'],
                    ]),

                    'Certificate of Indigency' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text'],
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'occupation', 'label' => 'Occupation (if any)', 'type' => 'text'],
                        ['name' => 'income_status', 'label' => 'Income Status', 'type' => 'select', 'options' => ['No Income', 'Low Income', 'Below Poverty Threshold']],
                        ['name' => 'monthly_income', 'label' => 'Monthly Household Income (₱)', 'type' => 'number'],
                        ['name' => 'number_of_dependents', 'label' => 'Number of Dependents', 'type' => 'number'],
                        ['name' => 'purpose', 'label' => 'Purpose of Certificate', 'type' => 'select', 'options' => [
                            'Medical Assistance', 'Financial Assistance', 'Educational Assistance', 'Burial Assistance', 'Legal Aid', 'Other'
                        ]],
                        ['name' => 'purpose_other', 'label' => 'If Other, please specify', 'type' => 'text'],
                        ['name' => 'recipient_office', 'label' => 'Recipient Office/Organization', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'Driver’s License', 'Passport', 'Voter’s ID', 'SSS/GSIS ID', 'Barangay ID', 'Other']],
                    ]),

                    'Barangay Clearance' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text'],
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
                        ['name' => 'community_standing', 'label' => 'Community Standing', 'type' => 'select', 'options' => ['Good Standing, Law-Abiding Citizen', 'With Derogatory Record']],
                        ['name' => 'criminal_record', 'label' => 'Criminal Record Status', 'type' => 'select', 'options' => ['No criminal offense on record', 'Has pending case']],
                        ['name' => 'purpose', 'label' => 'Purpose of Clearance', 'type' => 'select', 'options' => [
                            'Local Employment', 'Police Clearance', 'NBI Clearance', 'Business Permit', 'Loan Application', 'Travel/Visa', 'Other'
                        ]],
                        ['name' => 'purpose_other', 'label' => 'If Other, please specify', 'type' => 'text'],
                        ['name' => 'recipient_office', 'label' => 'Recipient Office/Organization', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'Driver’s License', 'Passport', 'Voter’s ID', 'SSS/GSIS ID', 'PRC ID', 'Other']],
                        ['name' => 'cedula_number', 'label' => 'Cedula/CTC Number', 'type' => 'text'],
                    ]),

                    'Business Clearance' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'business_name', 'label' => 'Business Name/Trade Name', 'type' => 'text'],
                        ['name' => 'business_address', 'label' => 'Business Address (Complete)', 'type' => 'text'],
                        ['name' => 'business_nature', 'label' => 'Nature/Type of Business', 'type' => 'text'],
                        ['name' => 'business_owner', 'label' => 'Business Owner Name', 'type' => 'text'],
                        ['name' => 'tin', 'label' => 'TIN (if applicable)', 'type' => 'text'],
                        ['name' => 'dti_registration', 'label' => 'DTI Registration Number (if applicable)', 'type' => 'text'],
                        ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'select', 'options' => ['New Business Permit', 'Renewal', 'Mayor’s Permit', 'Other']],
                        ['name' => 'recipient_office', 'label' => 'Recipient Office', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'Driver’s License', 'Passport', 'Voter’s ID', 'SSS/GSIS ID', 'Other']],
                        ['name' => 'cedula_number', 'label' => 'Cedula/CTC Number', 'type' => 'text'],
                    ]),

                    'Certificate of Employment (Unemployed)' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text'],
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'educational_attainment', 'label' => 'Educational Attainment', 'type' => 'select', 'options' => [
                            'Elementary Graduate', 'High School Graduate', 'Senior High Graduate', 'College Level', 'College Graduate', 'Vocational Graduate', 'Post Graduate'
                        ]],
                        ['name' => 'course_taken', 'label' => 'Course Taken (if applicable)', 'type' => 'text'],
                        ['name' => 'employment_status', 'label' => 'Employment Status', 'type' => 'select', 'options' => ['Unemployed', 'Self-Employed', 'Part-Time', 'Full-Time']],
                        ['name' => 'purpose', 'label' => 'Purpose of Certificate', 'type' => 'select', 'options' => [
                            'Job Application', 'Scholarship', 'Financial Assistance', 'Government Benefits', 'Other'
                        ]],
                        ['name' => 'purpose_other', 'label' => 'If Other, please specify', 'type' => 'text'],
                        ['name' => 'recipient_office', 'label' => 'Recipient Office/Organization', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'Driver’s License', 'Passport', 'Voter’s ID', 'School ID', 'Barangay ID', 'Other']],
                    ]),

                    'First-Time Jobseeker Certificate (RA 11261)' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text'],
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'educational_attainment', 'label' => 'Educational Attainment', 'type' => 'select', 'options' => [
                            'High School Graduate', 'Senior High Graduate', 'College Level', 'College Graduate', 'Vocational Graduate'
                        ]],
                        ['name' => 'course_taken', 'label' => 'Course/Strand Taken', 'type' => 'text'],
                        ['name' => 'year_graduated', 'label' => 'Year Graduated', 'type' => 'number'],
                        ['name' => 'oath_acknowledgement', 'label' => 'Oath of Undertaking (RA 11261)', 'type' => 'select', 'options' => ['I acknowledge and will sign the oath']],
                        ['name' => 'witness_name', 'label' => 'Witness Name (Parent/Guardian)', 'type' => 'text'],
                        ['name' => 'witness_relationship', 'label' => 'Relationship of Witness', 'type' => 'text'],
                        ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'select', 'options' => ['Job Application', 'Internship', 'OJT', 'Other']],
                        ['name' => 'recipient_office', 'label' => 'Company/Organization Applying To', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'School ID', 'Birth Certificate', 'Voter’s ID', 'Other']],
                    ]),

                    'Tenant/Farmer Certification' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text'],
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'land_area', 'label' => 'Total Land Area Cultivated (in hectares)', 'type' => 'text'],
                        ['name' => 'land_location', 'label' => 'Location/Sitio of Farm', 'type' => 'text'],
                        ['name' => 'land_boundaries', 'label' => 'Land Boundaries (North, South, East, West)', 'type' => 'textarea'],
                        ['name' => 'land_classification', 'label' => 'Type of Cultivation/Crop', 'type' => 'select', 'options' => [
                            'Rice Land', 'Corn Land', 'Mixed Crops', 'Coconut', 'Vegetables', 'Root Crops', 'Other'
                        ]],
                        ['name' => 'land_owner', 'label' => 'Land Owner Name (if tenant)', 'type' => 'text'],
                        ['name' => 'tenancy_duration', 'label' => 'Duration as Tenant/Farmer (Years)', 'type' => 'text'],
                        ['name' => 'purpose', 'label' => 'Purpose of Certification', 'type' => 'select', 'options' => [
                            'Agricultural Loan', 'Farm Assistance', 'DAR Registration', 'Crop Insurance', 'Other'
                        ]],
                        ['name' => 'purpose_other', 'label' => 'If Other, please specify', 'type' => 'text'],
                        ['name' => 'recipient_office', 'label' => 'Recipient Office/Organization', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'Voter’s ID', 'Farmer’s ID', 'Barangay ID', 'Other']],
                    ]),

                    'Certificate of Good Moral Character' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text'],
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
                        ['name' => 'community_standing', 'label' => 'Community Standing', 'type' => 'select', 'options' => ['Good Standing, Law-Abiding Citizen']],
                        ['name' => 'community_involvement', 'label' => 'Community Involvement (if any)', 'type' => 'text'],
                        ['name' => 'purpose', 'label' => 'Purpose of Certificate', 'type' => 'select', 'options' => [
                            'Employment', 'Scholarship', 'Volunteer Work', 'Government Service', 'Adoption', 'Immigration', 'Other'
                        ]],
                        ['name' => 'purpose_other', 'label' => 'If Other, please specify', 'type' => 'text'],
                        ['name' => 'recipient_office', 'label' => 'Recipient Office/Organization', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'Driver’s License', 'Passport', 'Voter’s ID', 'SSS/GSIS ID', 'Other']],
                        ['name' => 'cedula_number', 'label' => 'Cedula/CTC Number', 'type' => 'text'],
                    ]),

                    'Certificate of No Pending Case' => array_merge($this->getCommonBarangayFields(), [
                        ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text'],
                        ['name' => 'residency_duration', 'label' => 'Duration of Residency (Years/Months)', 'type' => 'text'],
                        ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
                        ['name' => 'criminal_record', 'label' => 'Criminal Record Status', 'type' => 'select', 'options' => ['No criminal offense or pending case on record']],
                        ['name' => 'blotter_history', 'label' => 'Blotter/Case History', 'type' => 'select', 'options' => ['No record in barangay blotter', 'Has resolved cases']],
                        ['name' => 'purpose', 'label' => 'Purpose of Certificate', 'type' => 'select', 'options' => [
                            'Employment', 'NBI Clearance', 'Police Clearance', 'Travel/Visa', 'Government Application', 'Loan Application', 'Other'
                        ]],
                        ['name' => 'purpose_other', 'label' => 'If Other, please specify', 'type' => 'text'],
                        ['name' => 'recipient_office', 'label' => 'Recipient Office/Organization', 'type' => 'text'],
                        ['name' => 'valid_id_type', 'label' => 'Valid ID Presented', 'type' => 'select', 'options' => ['PhilID', 'Driver’s License', 'Passport', 'Voter’s ID', 'SSS/GSIS ID', 'Other']],
                        ['name' => 'cedula_number', 'label' => 'Cedula/CTC Number', 'type' => 'text'],
                    ]),
                ],
                // Default fields shown before type selection
                'fields' => $this->getCommonBarangayFields()
            ]
        ];
    }
}