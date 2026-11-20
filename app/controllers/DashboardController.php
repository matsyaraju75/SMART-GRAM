<?php
// ==============================================================================
// Smart Gram - Internal Role Dashboards Controller (Phase 5)
// ==============================================================================

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Session;
use App\Models\AuditLog;
use App\Models\Grievance;
use App\Models\Location;
use App\Models\User;
use App\Services\WebhookService;
use PDO;

class DashboardController {
    /**
     * Dispatch user to their role-specific dashboard
     */
    public function index(): void {
        $user = auth();

        match($user['role'] ?? '') {
            'admin'   => $this->admin(),
            'staff'   => $this->staff(),
            'officer' => $this->officer(),
            default   => redirect('login')
        };
    }

    /**
     * Administrator Dashboard with Analytics & Comprehensive Filters
     */
    public function admin(): void {
        $user = auth();

        // 1. Core Analytics Summary & KPIs
        $analytics = Grievance::getAnalyticsSummary();
        $categoryBreakdown = Grievance::getCategoryBreakdown();
        $wardBreakdown = Grievance::getWardBreakdown();
        $workload = Grievance::getWorkloadDistribution();
        $overdueAlerts = Grievance::getOverdueGrievances(null, 5);

        // 2. High-level system counts
        $db = Database::getConnection();
        $systemStats = [
            'total_users' => (int)$db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn(),
            'villages'    => (int)$db->query("SELECT COUNT(*) FROM villages WHERE is_active = 1")->fetchColumn(),
            'departments' => (int)$db->query("SELECT COUNT(*) FROM departments WHERE is_active = 1")->fetchColumn(),
            'categories'  => (int)$db->query("SELECT COUNT(*) FROM categories WHERE is_active = 1")->fetchColumn(),
        ];

        // 3. Parse Geographic and Operational Filter Parameters from Query String
        $filters = [
            'state_id'          => $_GET['state_id'] ?? 'all',
            'district_id'       => $_GET['district_id'] ?? 'all',
            'mandal_id'         => $_GET['mandal_id'] ?? 'all',
            'gram_panchayat_id' => $_GET['gram_panchayat_id'] ?? 'all',
            'village_id'        => $_GET['village_id'] ?? 'all',
            'ward_id'           => $_GET['ward_id'] ?? 'all',
            'department_id'     => $_GET['department_id'] ?? 'all',
            'status'            => $_GET['status'] ?? 'all',
            'category_id'       => $_GET['category_id'] ?? 'all',
            'priority'          => $_GET['priority'] ?? 'all',
            'sla_status'        => $_GET['sla_status'] ?? 'all',
            'search'            => trim($_GET['search'] ?? ''),
            'date_from'         => $_GET['date_from'] ?? '',
            'date_to'           => $_GET['date_to'] ?? '',
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $grievanceData = Grievance::getFilteredGrievances($filters, $limit, $offset);

        // 4. Master dropdown lists for filter form
        $states = Location::getStates(true);
        $districts = Location::getDistricts(null, true);
        $mandals = Location::getMandals(null, true);
        $panchayats = Location::getGramPanchayats(null, true);
        $villages = Location::getVillages(null, true);
        $wards = Location::getWards(null, null, true);
        $categories = Grievance::getCategories();
        $departments = Grievance::getAllDepartments();
        $recentLogs = AuditLog::getRecent(6);

        // 5. Phase 8 Webhook & Automation Data
        $webhookStats = WebhookService::getStats(24);
        $webhookLogs = WebhookService::getRecentLogs(8);
        $webhookConfig = config('webhook') ?? [];

        view('dashboard/admin', [
            'title'             => 'Admin Dashboard & Analytics - Smart Gram',
            'user'              => $user,
            'analytics'         => $analytics,
            'systemStats'       => $systemStats,
            'categoryBreakdown' => $categoryBreakdown,
            'wardBreakdown'     => $wardBreakdown,
            'workload'          => $workload,
            'overdueAlerts'     => $overdueAlerts,
            'grievances'        => $grievanceData['items'],
            'totalGrievances'   => $grievanceData['total'],
            'totalPages'        => $grievanceData['pages'],
            'currentPage'       => $grievanceData['current_page'],
            'filters'           => $filters,
            'states'            => $states,
            'districts'         => $districts,
            'mandals'           => $mandals,
            'panchayats'        => $panchayats,
            'villages'          => $villages,
            'wards'             => $wards,
            'categories'        => $categories,
            'departments'       => $departments,
            'recentLogs'        => $recentLogs,
            'webhookStats'      => $webhookStats,
            'webhookLogs'       => $webhookLogs,
            'webhookConfig'     => $webhookConfig
        ]);
    }

    /**
     * Panchayat Staff Dashboard with Location-Scoped Access Control
     */
    public function staff(): void {
        $user = auth();
        $scopeFilters = User::getGeographicScopeFilters($user);

        // KPI analytics strictly scoped to user's assigned jurisdiction
        $analytics = Grievance::getAnalyticsSummary($scopeFilters);
        $wardBreakdown = Grievance::getWardBreakdown($scopeFilters);
        $categoryBreakdown = Grievance::getCategoryBreakdown($scopeFilters);
        $overdueAlerts = Grievance::getOverdueGrievances($scopeFilters, 5);

        // Operational queue filters: user's geographic boundary cannot be circumvented
        $filters = array_merge([
            'status'      => $_GET['status'] ?? 'all',
            'ward_id'     => $_GET['ward_id'] ?? 'all',
            'category_id' => $_GET['category_id'] ?? 'all',
            'priority'    => $_GET['priority'] ?? 'all',
            'sla_status'  => $_GET['sla_status'] ?? 'all',
            'search'      => trim($_GET['search'] ?? ''),
        ], $scopeFilters);

        // Staff can optionally drill down to a specific village within their GP/jurisdiction
        if (!empty($_GET['village_id']) && $_GET['village_id'] !== 'all') {
            $filters['village_id'] = (int)$_GET['village_id'];
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $grievanceData = Grievance::getFilteredGrievances($filters, $limit, $offset);

        // Populate dropdown options based on user's geographic jurisdiction
        $villageId = !empty($filters['village_id']) && $filters['village_id'] !== 'all' ? (int)$filters['village_id'] : (int)($user['village_id'] ?? 1);
        $panchayatId = !empty($scopeFilters['gram_panchayat_id']) ? (int)$scopeFilters['gram_panchayat_id'] : null;
        $wards = Location::getWards($villageId, $panchayatId, true);
        $villages = $panchayatId ? Location::getVillages($panchayatId, true) : Location::getVillages(null, true);
        $categories = Grievance::getCategories();
        $lang = current_lang();
        $scopeBadge = User::getScopeBadge($user, $lang);

        view('dashboard/staff', [
            'title'             => 'Panchayat Staff Dashboard - Smart Gram',
            'user'              => $user,
            'scopeBadge'        => $scopeBadge,
            'analytics'         => $analytics,
            'wardBreakdown'     => $wardBreakdown,
            'categoryBreakdown' => $categoryBreakdown,
            'overdueAlerts'     => $overdueAlerts,
            'grievances'        => $grievanceData['items'],
            'totalGrievances'   => $grievanceData['total'],
            'totalPages'        => $grievanceData['pages'],
            'currentPage'       => $grievanceData['current_page'],
            'filters'           => $filters,
            'wards'             => $wards,
            'villages'          => $villages,
            'categories'        => $categories
        ]);
    }

    /**
     * Field Officer Dashboard
     */
    public function officer(): void {
        $user = auth();
        $userId = (int)$user['id'];
        $scopeFilters = User::getGeographicScopeFilters($user);

        // Filter assigned grievances for this specific officer
        $filters = array_merge([
            'assigned_to' => $userId,
            'status'      => $_GET['status'] ?? 'all',
            'priority'    => $_GET['priority'] ?? 'all',
            'sla_status'  => $_GET['sla_status'] ?? 'all',
            'search'      => trim($_GET['search'] ?? ''),
        ], $scopeFilters);

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $grievanceData = Grievance::getFilteredGrievances($filters, $limit, $offset);

        // Compute officer-specific stats
        $db = Database::getConnection();
        $stats = [
            'assigned'    => (int)$db->query("SELECT COUNT(*) FROM grievances WHERE assigned_to_user_id = $userId AND status = 'assigned'")->fetchColumn(),
            'in_progress' => (int)$db->query("SELECT COUNT(*) FROM grievances WHERE assigned_to_user_id = $userId AND status = 'in_progress'")->fetchColumn(),
            'resolved'    => (int)$db->query("SELECT COUNT(*) FROM grievances WHERE assigned_to_user_id = $userId AND status IN ('resolved', 'closed')")->fetchColumn(),
            'overdue'     => (int)$db->query("SELECT COUNT(*) FROM grievances WHERE assigned_to_user_id = $userId AND sla_due_at < NOW() AND status IN ('assigned', 'in_progress')")->fetchColumn(),
        ];

        $lang = current_lang();
        $scopeBadge = User::getScopeBadge($user, $lang);

        view('dashboard/officer', [
            'title'           => 'Officer Field Workbench - Smart Gram',
            'user'            => $user,
            'scopeBadge'      => $scopeBadge,
            'stats'           => $stats,
            'grievances'      => $grievanceData['items'],
            'totalGrievances' => $grievanceData['total'],
            'totalPages'      => $grievanceData['pages'],
            'currentPage'     => $grievanceData['current_page'],
            'filters'         => $filters
        ]);
    }

    /**
     * Export Filtered Grievances as CSV Report
     */
    public function export(): void {
        $user = auth();
        if (!$user) {
            redirect('login');
        }

        $scopeFilters = User::getGeographicScopeFilters($user);

        // Parse filters from GET with user's mandatory geographic scope boundary applied
        $filters = array_merge([
            'state_id'          => $_GET['state_id'] ?? 'all',
            'district_id'       => $_GET['district_id'] ?? 'all',
            'mandal_id'         => $_GET['mandal_id'] ?? 'all',
            'gram_panchayat_id' => $_GET['gram_panchayat_id'] ?? 'all',
            'village_id'        => $_GET['village_id'] ?? 'all',
            'ward_id'           => $_GET['ward_id'] ?? 'all',
            'department_id'     => $_GET['department_id'] ?? 'all',
            'status'            => $_GET['status'] ?? 'all',
            'category_id'       => $_GET['category_id'] ?? 'all',
            'priority'          => $_GET['priority'] ?? 'all',
            'sla_status'        => $_GET['sla_status'] ?? 'all',
            'search'            => trim($_GET['search'] ?? ''),
            'date_from'         => $_GET['date_from'] ?? '',
            'date_to'           => $_GET['date_to'] ?? '',
        ], $scopeFilters);

        if ($user['role'] === 'officer') {
            $filters['assigned_to'] = (int)$user['id'];
        }

        $items = Grievance::getAllGrievancesForExport($filters);

        // Generate CSV file download
        $filename = 'smart_gram_grievance_report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM for Microsoft Excel compatibility
        fputs($out, "\xEF\xBB\xBF");

        // Header Row
        fputcsv($out, [
            'Grievance ID',
            'Citizen Name',
            'Citizen Phone',
            'State',
            'District',
            'Mandal',
            'Gram Panchayat',
            'Village',
            'Ward',
            'Category (Telugu)',
            'Category (English)',
            'Department',
            'Priority',
            'Status',
            'Assigned Officer',
            'SLA Due Date',
            'Submitted At',
            'Citizen Rating',
            'Description'
        ]);

        foreach ($items as $row) {
            fputcsv($out, [
                $row['tracking_id'],
                $row['citizen_name'],
                $row['citizen_phone'],
                $row['state_name_en'] ?? 'N/A',
                $row['district_name_en'] ?? 'N/A',
                $row['mandal_name_en'] ?? 'N/A',
                $row['panchayat_name_en'] ?? 'N/A',
                $row['village_name_en'] ?? 'N/A',
                $row['ward_number'] ? ($row['ward_number'] . ' - ' . $row['ward_name']) : 'N/A',
                $row['category_name_te'],
                $row['category_name_en'],
                $row['dept_name_en'],
                ucfirst($row['priority']),
                ucfirst(str_replace('_', ' ', $row['status'])),
                $row['assigned_officer_name'] ?: 'Unassigned',
                $row['sla_due_at'] ?: 'N/A',
                $row['created_at'],
                $row['citizen_rating'] ? ($row['citizen_rating'] . ' / 5') : 'Not Rated',
                $row['description']
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * Admin Action: Send Test Webhook Ping to n8n (Phase 8)
     */
    public function testWebhook(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || $user['role'] !== 'admin') {
            redirect('login');
        }

        $res = WebhookService::dispatch('test_webhook_ping', [
            'message'    => 'Manual test ping from Smart Gram admin dashboard',
            'admin_id'   => $user['id'],
            'admin_name' => $user['full_name'],
            'time'       => date('Y-m-d H:i:s')
        ], 'system', null);

        if ($res['status'] === 'success') {
            Session::setFlash('success', __('webhook.msg_ping_success'));
        } elseif ($res['status'] === 'simulated') {
            Session::setFlash('success', __('webhook.msg_ping_simulated'));
        } else {
            Session::setFlash('error', __('webhook.msg_ping_failed') . ($res['error'] ?? 'Unknown delivery failure'));
        }

        redirect('admin#webhook-panel');
    }

    /**
     * Admin Action: Trigger Manual SLA Escalation Scan (Phase 8)
     */
    public function scanSlaBreaches(): void {
        Csrf::validate();
        $user = auth();
        if (!$user || $user['role'] !== 'admin') {
            redirect('login');
        }

        $scan = WebhookService::triggerSlaEscalationCheck();
        Session::setFlash('success', __('webhook.msg_sla_scan_complete') . "{$scan['dispatched_count']} / {$scan['breached_count']}");

        redirect('admin#webhook-panel');
    }
}
