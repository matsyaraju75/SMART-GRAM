<?php
// ==============================================================================
// Smart Gram - Village Projects & Gram Sabha Controller
// ==============================================================================

namespace App\Controllers;

class ProjectsController {
    /**
     * Display the Village Projects & Gram Sabha public page
     */
    public function index(): void {
        // Budget summary data (from Master Screenshot)
        $budget = [
            'total_allocated' => '1,45,00,000',
            'total_allocated_label' => '₹1.45 Cr',
            'amount_utilized' => '99,20,000',
            'amount_utilized_label' => '₹99.2 Lakhs',
            'balance_available' => '45,80,000',
            'balance_available_label' => '₹45.8 Lakhs',
            'utilization_pct' => 68.4,
        ];

        // Ward-wise project tracker data (from Master Screenshot)
        $projects = [
            [
                'name' => 'Construction of CC Concrete Road & Stormwater Drain',
                'ward' => 3,
                'contractor' => 'Sri Ramesh Constructions & Infrastructure Ltd.',
                'budget' => '₹18,50,000',
                'progress' => 75,
                'status' => 'In Progress',
                'start_date' => '2026-03-15',
                'end_date' => '2026-12-15',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d0fbb18086f6?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'Solar RO Drinking Water Purification Plant',
                'ward' => 1,
                'contractor' => 'AP State RWS Engineering Division',
                'budget' => '₹8,20,000',
                'progress' => 100,
                'status' => 'Completed',
                'start_date' => '2026-01-10',
                'end_date' => '2026-06-15',
                'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'High School Digital Smart Classrooms & Community Library',
                'ward' => 4,
                'contractor' => 'Sarva Shiksha Abhiyan AP',
                'budget' => '₹12,40,000',
                'progress' => 100,
                'status' => 'Completed',
                'start_date' => '2026-02-01',
                'end_date' => '2026-07-31',
                'image' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'LED Street Lighting Network Extension (7.5 km Sub-station)',
                'ward' => '1-6',
                'contractor' => 'Jyothi Electricals & EESL Consortium',
                'budget' => '₹9,50,000',
                'progress' => 40,
                'status' => 'In Progress',
                'start_date' => '2026-07-01',
                'end_date' => '2026-12-31',
                'image' => 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'Community Hall Renovation & Cyclone Shelter',
                'ward' => 2,
                'contractor' => 'Bhavani Rural Builders',
                'budget' => '₹6,75,000',
                'progress' => 70,
                'status' => 'In Progress',
                'start_date' => '2026-04-20',
                'end_date' => '2026-11-30',
                'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'Modern Anganwadi Nutrition Center Construction',
                'ward' => 6,
                'contractor' => 'Rural Development Corporation AP',
                'budget' => '₹4,80,000',
                'progress' => 15,
                'status' => 'Upcoming',
                'start_date' => '2026-12-01',
                'end_date' => '2027-06-30',
                'image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'Drainage System Ward 4',
                'ward' => 4,
                'contractor' => 'Infra Solutions AP',
                'budget' => '₹5,40,000',
                'progress' => 100,
                'status' => 'Completed',
                'start_date' => '2025-11-01',
                'end_date' => '2026-04-30',
                'image' => 'https://placehold.co/400x250/74c69d/ffffff?text=Drainage+System',
            ],
            [
                'name' => 'Anganwadi Centre Construction',
                'ward' => 6,
                'contractor' => 'Rural Dev. Corp.',
                'budget' => '₹4,80,000',
                'progress' => 15,
                'status' => 'Upcoming',
                'start_date' => '2026-12-01',
                'end_date' => '2027-06-30',
                'image' => 'https://placehold.co/400x250/95d5b2/ffffff?text=Anganwadi+Centre',
            ],
            [
                'name' => 'Solar Water Pump Installation',
                'ward' => 7,
                'contractor' => 'Green Energy Solutions',
                'budget' => '₹2,50,000',
                'progress' => 0,
                'status' => 'Upcoming',
                'start_date' => '2027-01-15',
                'end_date' => '2027-04-30',
                'image' => 'https://placehold.co/400x250/b7e4c7/333333?text=Solar+Pump',
            ],
            [
                'name' => 'Public Toilet Complex Ward 1',
                'ward' => 1,
                'contractor' => 'Swachh Nirman Ltd.',
                'budget' => '₹1,85,000',
                'progress' => 55,
                'status' => 'In Progress',
                'start_date' => '2026-06-10',
                'end_date' => '2026-10-15',
                'image' => 'https://placehold.co/400x250/d8f3dc/333333?text=Toilet+Complex',
            ],
        ];

        // Gram Sabha meetings data
        $meetings = [
            'next' => [
                'date' => '2026-12-15',
                'time' => '10:00 AM',
                'venue' => 'Gram Panchayat Community Hall, Ward 2',
                'agenda' => [
                    'Approval of Annual Budget 2027-28',
                    'Review of ongoing MGNREGA works',
                    'Drinking water supply improvement plan',
                    'Selection of beneficiaries under PM Awas Yojana',
                    'Public grievances and open discussion',
                ],
            ],
            'previous' => [
                [
                    'date' => '2026-10-02',
                    'title' => 'General Gram Sabha (Gandhi Jayanti Special)',
                    'attendance' => 142,
                    'resolutions' => 8,
                    'file' => '#',
                ],
                [
                    'date' => '2026-08-15',
                    'title' => 'Independence Day Special Gram Sabha',
                    'attendance' => 198,
                    'resolutions' => 12,
                    'file' => '#',
                ],
                [
                    'date' => '2026-06-26',
                    'title' => 'Quarterly Review Gram Sabha (Q2)',
                    'attendance' => 115,
                    'resolutions' => 6,
                    'file' => '#',
                ],
                [
                    'date' => '2026-04-14',
                    'title' => 'Ambedkar Jayanti Special Gram Sabha',
                    'attendance' => 167,
                    'resolutions' => 10,
                    'file' => '#',
                ],
            ],
        ];

        // Social Audit & RTI data
        $audits = [
            [
                'date' => '2026-09-15',
                'type' => 'MGNREGA Social Audit',
                'findings' => 'All works verified, minor documentation gaps',
                'status' => 'Completed',
                'file' => '#',
            ],
            [
                'date' => '2026-07-20',
                'type' => 'Financial Audit (FY 2025-26)',
                'findings' => 'No irregularities found',
                'status' => 'Completed',
                'file' => '#',
            ],
            [
                'date' => '2026-11-01',
                'type' => '15th FC Fund Utilization Audit',
                'findings' => 'Audit in progress',
                'status' => 'In Progress',
                'file' => '#',
            ],
            [
                'date' => '2026-05-10',
                'type' => 'SFC Grant Utilization Review',
                'findings' => '95% utilization achieved',
                'status' => 'Completed',
                'file' => '#',
            ],
        ];

        $disclosures = [
            ['title' => 'Annual Financial Statement 2025-26', 'date' => '2026-06-30', 'file' => '#'],
            ['title' => 'Monthly Expenditure Report — October 2026', 'date' => '2026-11-05', 'file' => '#'],
            ['title' => 'Citizen Charter (Updated)', 'date' => '2026-04-01', 'file' => '#'],
            ['title' => 'RTI Proactive Disclosure (Section 4)', 'date' => '2026-01-15', 'file' => '#'],
            ['title' => 'Public Notice — Road Widening Ward 3', 'date' => '2026-10-20', 'file' => '#'],
        ];

        view('citizen/projects', [
            'title' => 'Village Projects & Gram Sabha — Smart Gram',
            'budget' => $budget,
            'projects' => $projects,
            'meetings' => $meetings,
            'audits' => $audits,
            'disclosures' => $disclosures,
        ]);
    }
}
