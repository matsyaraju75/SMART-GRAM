<?php
// ==============================================================================
// Smart Gram - Analytics & Realistic Grievance Fixtures Seeder
// Seeds diverse grievances across all categories, wards, and SLA states
// ==============================================================================

require_once __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';

try {
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Check count of grievances
    $count = (int)$pdo->query("SELECT COUNT(*) FROM grievances")->fetchColumn();
    if ($count >= 10) {
        echo "Database already has $count grievances. Seeding skipped.\n";
        exit(0);
    }

    echo "Seeding realistic analytics grievances dataset...\n";

    $sampleGrievances = [
        [
            'tracking_id' => 'GRV-2026-00010',
            'village_id' => 1,
            'ward_id' => 1,
            'category_id' => 1, // Pipeline Leakage
            'department_id' => 1,
            'citizen_name' => 'K. Venkata Rao',
            'citizen_phone' => '9848011223',
            'description' => 'Main drinking water pipeline broke near community hall. Water gushing onto road for 2 days.',
            'landmark' => 'Near Community Hall, Gandhi Nagar',
            'priority' => 'critical',
            'status' => 'submitted',
            'assigned_to_user_id' => null,
            'sla_hours_offset' => -10, // overdue by 10 hours!
            'created_hours_ago' => 34,
        ],
        [
            'tracking_id' => 'GRV-2026-00011',
            'village_id' => 1,
            'ward_id' => 1,
            'category_id' => 4, // Street Light Not Working
            'department_id' => 2,
            'citizen_name' => 'B. Lakshmi Devi',
            'citizen_phone' => '9848022334',
            'description' => 'Two street light bulbs fused near primary school street. Very dark at night.',
            'landmark' => 'Opposite Primary School',
            'priority' => 'medium',
            'status' => 'in_progress',
            'assigned_to_user_id' => 4, // Electrical officer
            'sla_hours_offset' => 18, // 18 hours remaining
            'created_hours_ago' => 30,
        ],
        [
            'tracking_id' => 'GRV-2026-00012',
            'village_id' => 1,
            'ward_id' => 2,
            'category_id' => 8, // Drainage Overflow
            'department_id' => 4,
            'citizen_name' => 'M. Subba Rao',
            'citizen_phone' => '9848033445',
            'description' => 'Open drainage canal is blocked with silt and plastic. Heavy foul smell and mosquito breeding.',
            'landmark' => 'Near Shivalayam Temple Arch',
            'priority' => 'high',
            'status' => 'assigned',
            'assigned_to_user_id' => 5, // Sanitation officer
            'sla_hours_offset' => -4, // overdue by 4 hours
            'created_hours_ago' => 28,
        ],
        [
            'tracking_id' => 'GRV-2026-00013',
            'village_id' => 1,
            'ward_id' => 2,
            'category_id' => 2, // No Water Supply
            'department_id' => 1,
            'citizen_name' => 'P. Anjaneyulu',
            'citizen_phone' => '9848044556',
            'description' => 'Public tap has had no water supply since yesterday morning. Families unable to fetch water.',
            'landmark' => 'Water Tank Junction',
            'priority' => 'critical',
            'status' => 'in_progress',
            'assigned_to_user_id' => 3, // Water officer
            'sla_hours_offset' => 2, // due in 2 hours!
            'created_hours_ago' => 10,
        ],
        [
            'tracking_id' => 'GRV-2026-00014',
            'village_id' => 1,
            'ward_id' => 3,
            'category_id' => 6, // Garbage Heap
            'department_id' => 3,
            'citizen_name' => 'S. Rama Devi',
            'citizen_phone' => '9848055667',
            'description' => 'Garbage heap has accumulated for over 4 days beside Anganwadi centre. Stray cattle scattering waste.',
            'landmark' => 'Beside Anganwadi Centre',
            'priority' => 'high',
            'status' => 'resolved',
            'assigned_to_user_id' => 5,
            'sla_hours_offset' => 8,
            'created_hours_ago' => 16,
        ],
        [
            'tracking_id' => 'GRV-2026-00015',
            'village_id' => 1,
            'ward_id' => 4,
            'category_id' => 10, // Potholes
            'department_id' => 5,
            'citizen_name' => 'Ch. Srinivasa Rao',
            'citizen_phone' => '9848066778',
            'description' => 'Large trench dug across road for pipeline repair was left unpaved. Dangerous for two-wheelers.',
            'landmark' => 'Main Road, BC Colony Entrance',
            'priority' => 'medium',
            'status' => 'under_review',
            'assigned_to_user_id' => null,
            'sla_hours_offset' => 40,
            'created_hours_ago' => 32,
        ],
        [
            'tracking_id' => 'GRV-2026-00016',
            'village_id' => 1,
            'ward_id' => 4,
            'category_id' => 5, // Loose Electric Wire
            'department_id' => 2,
            'citizen_name' => 'D. Nageswara Rao',
            'citizen_phone' => '9848077889',
            'description' => 'Low hanging live electric cable near village transformer. Poses electrocution danger.',
            'landmark' => 'Transformer Pole #12',
            'priority' => 'critical',
            'status' => 'resolved',
            'assigned_to_user_id' => 4,
            'sla_hours_offset' => 1,
            'created_hours_ago' => 5,
        ],
        [
            'tracking_id' => 'GRV-2026-00017',
            'village_id' => 1,
            'ward_id' => 5,
            'category_id' => 7, // Public Toilet
            'department_id' => 3,
            'citizen_name' => 'T. Kumari',
            'citizen_phone' => '9848088990',
            'description' => 'Rythu Bazaar public conveniences have water tap leakage and require thorough sanitization.',
            'landmark' => 'Rythu Bazaar Shed #2',
            'priority' => 'medium',
            'status' => 'closed',
            'assigned_to_user_id' => 5,
            'sla_hours_offset' => 12,
            'created_hours_ago' => 48,
            'feedback' => [
                'rating' => 5,
                'satisfied' => 1,
                'comment' => 'Sanitation staff arrived within 3 hours and thoroughly cleaned. Very satisfied!'
            ]
        ],
        [
            'tracking_id' => 'GRV-2026-00018',
            'village_id' => 1,
            'ward_id' => 1,
            'category_id' => 3, // Contaminated water
            'department_id' => 1,
            'citizen_name' => 'Y. Venkatesh',
            'citizen_phone' => '9848099001',
            'description' => 'Drinking water from borewell is turbid with mud residue. Villagers getting stomach ache.',
            'landmark' => 'Near Old Well, Gandhi Nagar',
            'priority' => 'critical',
            'status' => 'reopened',
            'assigned_to_user_id' => 3,
            'sla_hours_offset' => -8, // overdue
            'created_hours_ago' => 50,
            'reopen_reason' => 'Technician flushed pipeline once but mud returned next morning. Need filter replacement.'
        ],
        [
            'tracking_id' => 'GRV-2026-00019',
            'village_id' => 1,
            'ward_id' => 5,
            'category_id' => 9, // Drain Blockage
            'department_id' => 4,
            'citizen_name' => 'G. Krishna',
            'citizen_phone' => '9848100112',
            'description' => 'Covered culvert drain is jammed near vegetable stalls.',
            'landmark' => 'Vegetable Market Crossroad',
            'priority' => 'medium',
            'status' => 'closed',
            'assigned_to_user_id' => 5,
            'sla_hours_offset' => 20,
            'created_hours_ago' => 72,
            'feedback' => [
                'rating' => 4,
                'satisfied' => 1,
                'comment' => 'Panchayat laborers cleaned the desilting cover. Thank you.'
            ]
        ]
    ];

    $insertStmt = $pdo->prepare("
        INSERT INTO grievances 
        (tracking_id, village_id, ward_id, category_id, department_id, citizen_name, citizen_phone, description, landmark, priority, status, assigned_to_user_id, sla_due_at, created_at)
        VALUES 
        (:tracking_id, :village_id, :ward_id, :category_id, :department_id, :citizen_name, :citizen_phone, :description, :landmark, :priority, :status, :assigned_to_user_id, :sla_due_at, :created_at)
    ");

    $updateStmt = $pdo->prepare("
        INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
        VALUES (:grievance_id, :user_id, :action, :from_status, :to_status, :remarks, :created_at)
    ");

    $feedbackStmt = $pdo->prepare("
        INSERT INTO feedback (grievance_id, is_resolved_satisfied, rating, citizen_comment, created_at)
        VALUES (:grievance_id, :is_resolved_satisfied, :rating, :citizen_comment, :created_at)
    ");

    foreach ($sampleGrievances as $g) {
        // Calculate timestamps
        $createdAt = date('Y-m-d H:i:s', strtotime("-{$g['created_hours_ago']} hours"));
        $slaDueAt = date('Y-m-d H:i:s', strtotime("+{$g['sla_hours_offset']} hours"));

        $insertStmt->execute([
            ':tracking_id' => $g['tracking_id'],
            ':village_id' => $g['village_id'],
            ':ward_id' => $g['ward_id'],
            ':category_id' => $g['category_id'],
            ':department_id' => $g['department_id'],
            ':citizen_name' => $g['citizen_name'],
            ':citizen_phone' => $g['citizen_phone'],
            ':description' => $g['description'],
            ':landmark' => $g['landmark'],
            ':priority' => $g['priority'],
            ':status' => $g['status'],
            ':assigned_to_user_id' => $g['assigned_to_user_id'],
            ':sla_due_at' => $slaDueAt,
            ':created_at' => $createdAt,
        ]);

        $grievanceId = (int)$pdo->lastInsertId();

        // Add initial update
        $updateStmt->execute([
            ':grievance_id' => $grievanceId,
            ':user_id' => null,
            ':action' => 'citizen_submitted',
            ':from_status' => null,
            ':to_status' => 'submitted',
            ':remarks' => 'Grievance submitted by citizen',
            ':created_at' => $createdAt
        ]);

        // Add subsequent updates if assigned/resolved/closed
        if ($g['assigned_to_user_id']) {
            $assignTime = date('Y-m-d H:i:s', strtotime($createdAt . " +2 hours"));
            $updateStmt->execute([
                ':grievance_id' => $grievanceId,
                ':user_id' => 2, // Staff
                ':action' => 'staff_assigned',
                ':from_status' => 'submitted',
                ':to_status' => 'assigned',
                ':remarks' => 'Assigned to field officer for site inspection',
                ':created_at' => $assignTime
            ]);
        }

        if (in_array($g['status'], ['resolved', 'closed', 'reopened'])) {
            $resolveTime = date('Y-m-d H:i:s', strtotime($createdAt . " +6 hours"));
            $updateStmt->execute([
                ':grievance_id' => $grievanceId,
                ':user_id' => $g['assigned_to_user_id'] ?? 3,
                ':action' => 'officer_resolved',
                ':from_status' => 'in_progress',
                ':to_status' => 'resolved',
                ':remarks' => 'Field repair completed on site. Awaiting citizen confirmation.',
                ':created_at' => $resolveTime
            ]);
        }

        if (!empty($g['feedback'])) {
            $feedbackTime = date('Y-m-d H:i:s', strtotime($createdAt . " +8 hours"));
            $feedbackStmt->execute([
                ':grievance_id' => $grievanceId,
                ':is_resolved_satisfied' => $g['feedback']['satisfied'],
                ':rating' => $g['feedback']['rating'],
                ':citizen_comment' => $g['feedback']['comment'],
                ':created_at' => $feedbackTime
            ]);
            $updateStmt->execute([
                ':grievance_id' => $grievanceId,
                ':user_id' => null,
                ':action' => 'citizen_feedback_closed',
                ':from_status' => 'resolved',
                ':to_status' => 'closed',
                ':remarks' => "Citizen closed grievance with {$g['feedback']['rating']}-star rating: {$g['feedback']['comment']}",
                ':created_at' => $feedbackTime
            ]);
        }

        if (!empty($g['reopen_reason'])) {
            $reopenTime = date('Y-m-d H:i:s', strtotime($createdAt . " +10 hours"));
            $updateStmt->execute([
                ':grievance_id' => $grievanceId,
                ':user_id' => null,
                ':action' => 'citizen_reopened',
                ':from_status' => 'resolved',
                ':to_status' => 'reopened',
                ':remarks' => "Citizen reopened issue: " . $g['reopen_reason'],
                ':created_at' => $reopenTime
            ]);
        }
    }

    echo "Seeded " . count($sampleGrievances) . " realistic analytics grievances successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
