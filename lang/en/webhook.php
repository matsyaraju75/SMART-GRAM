<?php
// ==============================================================================
// Smart Gram - English Webhook & Automation Translations (Phase 8)
// ==============================================================================

return [
    'panel_title' => 'n8n Automation & Webhook Engine',
    'badge_status_active' => 'Automation Active',
    'badge_status_offline' => 'Simulation / Offline Mode',
    'badge_status_disabled' => 'Automation Disabled',
    
    // Status Card Metrics
    'total_dispatches' => 'Total Dispatches (24h)',
    'delivered' => 'Delivered (200 OK)',
    'failed' => 'Failed Deliveries',
    'simulated' => 'Simulated Dispatches',
    'target_endpoint' => 'Target Webhook Endpoint',
    
    // Actions & Buttons
    'btn_test_ping' => 'Send Test Webhook Ping',
    'btn_scan_sla' => 'Trigger SLA Escalation Scan',
    'msg_ping_success' => 'Test webhook ping dispatched successfully!',
    'msg_ping_simulated' => 'Test webhook ping simulated and logged locally.',
    'msg_ping_failed' => 'Webhook ping delivery failed: ',
    'msg_sla_scan_complete' => 'SLA breach scan completed. Breached complaints dispatched: ',
    
    // Table Columns
    'col_event' => 'Event Trigger',
    'col_entity' => 'Entity',
    'col_http_status' => 'HTTP Status',
    'col_status' => 'Delivery State',
    'col_time' => 'Latency (ms)',
    'col_created' => 'Timestamp',
    'no_logs_found' => 'No webhook dispatch logs recorded yet.',
    
    // Message Notification Templates
    'template_submitted' => 'Dear {citizen_name}, your village grievance has been registered on Smart Gram. Grievance ID: {tracking_id}. Department: {department_name}. Track status anytime at: {tracking_url}',
    'template_assigned' => 'Dear {citizen_name}, your grievance ({tracking_id}) has been assigned to Field Officer {officer_name} ({officer_phone}). Work will begin shortly.',
    'template_resolved' => 'Dear {citizen_name}, your grievance ({tracking_id}) has been resolved with on-site photographic evidence! Please review and verify: {tracking_url}',
    'template_reopened' => 'ALERT: Grievance {tracking_id} in Ward {ward_number} has been reopened by citizen with reason: "{reopen_reason}". Immediate re-inspection required.',
    'template_sla_breach' => 'SLA BREACH ALERT: Grievance {tracking_id} ({category_name}) in Ward {ward_number} has exceeded its SLA threshold! Action required immediately.',
    'template_officer_alert' => 'NEW WORK ORDER: You have been assigned grievance {tracking_id} in Ward {ward_number}. Priority: {priority}. Citizen: {citizen_name} ({citizen_phone}). Location: {landmark}.'
];
