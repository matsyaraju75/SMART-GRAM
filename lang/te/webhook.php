<?php
// ==============================================================================
// Smart Gram - Telugu Webhook & Automation Translations (ఆటోమేషన్ & నోటిఫికేషన్లు)
// ==============================================================================

return [
    'panel_title' => 'n8n ఆటోమేషన్ & వెబ్‌హుక్ ఇంజిన్ (Automation Engine)',
    'badge_status_active' => 'ఆటోమేషన్ చురుకుగా ఉంది (Active)',
    'badge_status_offline' => 'సిమ్యులేషన్ మోడ్ (Simulation / Offline)',
    'badge_status_disabled' => 'ఆటోమేషన్ నిలిపివేయబడింది',
    
    // Status Card Metrics
    'total_dispatches' => 'మొత్తం పంపిన సందేశాలు (24 గంటలు)',
    'delivered' => 'విజయవంతంగా చేరినవి (Delivered)',
    'failed' => 'విఫలమైనవి (Failed)',
    'simulated' => 'సిమ్యులేట్ చేసినవి (Simulated)',
    'target_endpoint' => 'లక్ష్య వెబ్‌హుక్ చిరునామా (Target Endpoint)',
    
    // Actions & Buttons
    'btn_test_ping' => 'పరీక్షా వెబ్‌హుక్ పంపండి (Test Ping)',
    'btn_scan_sla' => 'గడువు దాటిన సమస్యల తనిఖీ (SLA Scan)',
    'msg_ping_success' => 'పరీక్షా వెబ్‌హుక్ విజయవంతంగా పంపబడింది!',
    'msg_ping_simulated' => 'పరీక్షా వెబ్‌హుక్ సిమ్యులేట్ చేయబడి స్థానికంగా రికార్డ్ అయింది.',
    'msg_ping_failed' => 'వెబ్‌హుక్ పంపడం విఫలమైంది: ',
    'msg_sla_scan_complete' => 'గడువు దాటిన సమస్యల స్కానింగ్ పూర్తయింది. పంపిన హెచ్చరికలు: ',
    
    // Table Columns
    'col_event' => 'ఈవెంట్ రకం (Event Trigger)',
    'col_entity' => 'విభాగం / రికార్డు',
    'col_http_status' => 'HTTP స్థితి కోడ్',
    'col_status' => 'డెలివరీ స్థితి',
    'col_time' => 'వ్యవధి (ms)',
    'col_created' => 'సమయం',
    'no_logs_found' => 'ఇప్పటివరకు ఎటువంటి వెబ్‌హుక్ లాగ్‌లు నమోదు కాలేదు.',
    
    // Message Notification Templates
    'template_submitted' => 'నమస్కారం {citizen_name}, మీ గ్రామ సమస్య స్మార్ట్ గ్రామ్ పోర్టల్‌లో నమోదైంది. ఫిర్యాదు సంఖ్య: {tracking_id}. సంబంధిత విభాగం: {department_name}. మీ సమస్య స్థితిని ఎప్పుడైనా ఇక్కడ చూడవచ్చు: {tracking_url}',
    'template_assigned' => 'నమస్కారం {citizen_name}, మీ ఫిర్యాదు ({tracking_id}) ఫీల్డ్ అధికారి {officer_name} గారికి ({officer_phone}) కేటాయించబడింది. త్వరలోనే పనులు ప్రారంభమవుతాయి.',
    'template_resolved' => 'నమస్కారం {citizen_name}, మీ సమస్య ({tracking_id}) విజయవంతంగా పరిష్కరించబడింది! పని పూర్తయిన ఫోటోను పరిశీలించి మీ అభిప్రాయాన్ని తెలియజేయండి: {tracking_url}',
    'template_reopened' => 'అత్యవసర హెచ్చరిక: వార్డు {ward_number} లో ఫిర్యాదు {tracking_id} పౌరులచే తిరిగి తెరవబడింది. కారణం: "{reopen_reason}". తక్షణ పునఃపరిశీలన అవసరం.',
    'template_sla_breach' => 'గడువు ముగింపు హెచ్చరిక (SLA Breach): వార్డు {ward_number} లో ఫిర్యాదు {tracking_id} ({category_name}) నిర్ణీత గడువు దాటిపోయింది! తక్షణ చర్య అవసరం.',
    'template_officer_alert' => 'నూతన పని కేటాయింపు: వార్డు {ward_number} లో ఫిర్యాదు {tracking_id} మీకు కేటాయించబడింది. ప్రాధాన్యత: {priority}. పౌరుని పేరు: {citizen_name} ({citizen_phone}). ల్యాండ్‌మార్క్: {landmark}.'
];
