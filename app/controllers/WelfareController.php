<?php
// ==============================================================================
// Smart Gram - Welfare Schemes & Eligibility Controller
// ==============================================================================

namespace App\Controllers;

class WelfareController {

    /**
     * Display the Welfare Schemes & Eligibility page
     */
    public function index(): void {
        $schemes = [
            [
                'id' => 'caste-cert',
                'icon' => 'bi-file-earmark-person',
                'color' => 'primary',
                'title_key' => 'welfare_scheme_caste_title',
                'desc_key' => 'welfare_scheme_caste_desc',
                'eligibility' => ['All residents of the village', 'Valid Aadhaar card required', 'Existing Ration Card'],
                'documents' => ['Aadhaar Card', 'Ration Card', 'Father/Husband Caste Certificate', 'Passport Size Photo'],
                'processing_days' => 7,
            ],
            [
                'id' => 'income-cert',
                'icon' => 'bi-currency-rupee',
                'color' => 'success',
                'title_key' => 'welfare_scheme_income_title',
                'desc_key' => 'welfare_scheme_income_desc',
                'eligibility' => ['Annual income below ₹2,50,000', 'Valid address proof', 'Income source details'],
                'documents' => ['Aadhaar Card', 'Ration Card', 'Salary Slip / Self-Declaration', 'Bank Passbook'],
                'processing_days' => 5,
            ],
            [
                'id' => 'residence-cert',
                'icon' => 'bi-house-check',
                'color' => 'info',
                'title_key' => 'welfare_scheme_residence_title',
                'desc_key' => 'welfare_scheme_residence_desc',
                'eligibility' => ['Must be residing in village for 1+ year', 'Valid Aadhaar linked to address', 'Electricity / Water bill as proof'],
                'documents' => ['Aadhaar Card', 'Ration Card', 'Electricity Bill', 'Voter ID / Passport'],
                'processing_days' => 5,
            ],
            [
                'id' => 'integrated-cert',
                'icon' => 'bi-file-earmark-check',
                'color' => 'warning',
                'title_key' => 'welfare_scheme_integrated_title',
                'desc_key' => 'welfare_scheme_integrated_desc',
                'eligibility' => ['Covers Caste + Income + Residence', 'Single application for all three', 'Faster processing via MeeSeva'],
                'documents' => ['Aadhaar Card', 'Ration Card', 'Father/Husband Caste Certificate', 'Income Proof', 'Address Proof'],
                'processing_days' => 10,
            ],
            [
                'id' => 'pension-old-age',
                'icon' => 'bi-person-wheelchair',
                'color' => 'danger',
                'title_key' => 'welfare_scheme_pension_old_title',
                'desc_key' => 'welfare_scheme_pension_old_desc',
                'eligibility' => ['Age 60 years or above', 'BPL / Antyodaya Ration Card holder', 'Annual income below ₹1,50,000'],
                'documents' => ['Aadhaar Card', 'Age Proof (SSC / Birth Certificate)', 'BPL Ration Card', 'Bank Passbook', 'Passport Photo'],
                'processing_days' => 15,
            ],
            [
                'id' => 'pension-widow',
                'icon' => 'bi-person-hearts',
                'color' => 'secondary',
                'title_key' => 'welfare_scheme_pension_widow_title',
                'desc_key' => 'welfare_scheme_pension_widow_desc',
                'eligibility' => ['Widow women aged 18+', 'Husband death certificate required', 'Annual income below ₹1,50,000'],
                'documents' => ['Aadhaar Card', 'Death Certificate of Husband', 'Ration Card', 'Bank Passbook', 'Passport Photo'],
                'processing_days' => 15,
            ],
            [
                'id' => 'pension-disabled',
                'icon' => 'bi-universal-access-circle',
                'color' => 'dark',
                'title_key' => 'welfare_scheme_pension_disabled_title',
                'desc_key' => 'welfare_scheme_pension_disabled_desc',
                'eligibility' => ['Disability of 40% or more', 'SADAREM certificate required', 'Annual income below ₹1,50,000'],
                'documents' => ['Aadhaar Card', 'SADAREM / Medical Board Certificate', 'Ration Card', 'Bank Passbook', 'Passport Photo'],
                'processing_days' => 15,
            ],
        ];

        view('citizen/welfare', [
            'title' => __('citizen.welfare_page_title') . ' — ' . __('common.app_name'),
            'schemes' => $schemes,
        ]);
    }

    /**
     * Simple JSON API — Instant Family Eligibility Checker
     */
    public function checkEligibility(): void {
        header('Content-Type: application/json; charset=utf-8');

        $rationCard = trim($_POST['ration_card'] ?? '');
        $annualIncome = (int) ($_POST['annual_income'] ?? 0);
        $casteCategory = trim($_POST['caste_category'] ?? '');
        $familyMembers = (int) ($_POST['family_members'] ?? 1);

        $eligible = [];

        // Caste / Income / Residence / Integrated certificates — open to all
        $eligible[] = [
            'scheme' => 'Caste Certificate',
            'eligible' => true,
            'reason' => 'All village residents are eligible to apply.',
            'color' => 'primary',
        ];
        $eligible[] = [
            'scheme' => 'Income Certificate',
            'eligible' => $annualIncome <= 250000,
            'reason' => $annualIncome <= 250000
                ? 'Your annual income qualifies (below ₹2,50,000).'
                : 'Annual income exceeds ₹2,50,000 limit.',
            'color' => 'success',
        ];
        $eligible[] = [
            'scheme' => 'Residence Certificate',
            'eligible' => true,
            'reason' => 'All residents with 1+ year stay are eligible.',
            'color' => 'info',
        ];
        $eligible[] = [
            'scheme' => 'Integrated Certificate',
            'eligible' => true,
            'reason' => 'Combined Caste + Income + Residence — single application.',
            'color' => 'warning',
        ];

        // Old Age Pension
        $bplCards = ['BPL', 'AAY'];
        $isBpl = in_array(strtoupper($rationCard), $bplCards, true);
        $eligible[] = [
            'scheme' => 'Old Age Pension (Aasara)',
            'eligible' => $isBpl && $annualIncome <= 150000,
            'reason' => ($isBpl && $annualIncome <= 150000)
                ? 'BPL/AAY card holder with income below ₹1,50,000. Eligible if age ≥ 60.'
                : 'Requires BPL/AAY ration card and income below ₹1,50,000.',
            'color' => 'danger',
        ];

        // Widow Pension
        $eligible[] = [
            'scheme' => 'Widow Pension (Aasara)',
            'eligible' => $annualIncome <= 150000,
            'reason' => $annualIncome <= 150000
                ? 'Income below ₹1,50,000. Eligible if applicant is a widow with death certificate.'
                : 'Annual income exceeds ₹1,50,000 limit.',
            'color' => 'secondary',
        ];

        // Disabled Pension
        $eligible[] = [
            'scheme' => 'Disabled Pension (Aasara)',
            'eligible' => $annualIncome <= 150000,
            'reason' => $annualIncome <= 150000
                ? 'Income below ₹1,50,000. Eligible if disability ≥ 40% with SADAREM certificate.'
                : 'Annual income exceeds ₹1,50,000 limit.',
            'color' => 'dark',
        ];

        // Ration Card benefit
        if ($familyMembers >= 5 && in_array(strtoupper($rationCard), ['BPL', 'AAY', 'PHH'], true)) {
            $eligible[] = [
                'scheme' => 'Annapurna Food Security (Large Family)',
                'eligible' => true,
                'reason' => 'Families with 5+ members under BPL/AAY/PHH cards qualify for additional rice quota.',
                'color' => 'success',
            ];
        }

        echo json_encode(['success' => true, 'results' => $eligible]);
        exit;
    }
}
