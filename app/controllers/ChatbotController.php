<?php
// ==============================================================================
// Smart Gram - Chatbot Controller
// Interactive AI & Rule-Based Assistant for Grievance Handling & Village Info
// ==============================================================================

namespace App\Controllers;

use App\Core\Session;
use App\Core\Csrf;
use App\Core\RateLimiter;
use App\Models\Grievance;
use App\Services\AiAdvisoryService;
use Exception;

class ChatbotController {

    /**
     * Handle incoming chatbot messages via AJAX / JSON API
     */
    public function message(): void {
        header('Content-Type: application/json; charset=utf-8');

        // Parse JSON body or standard POST request
        $rawInput = file_get_contents('php://input');
        $jsonInput = json_decode($rawInput, true) ?? [];

        $submittedToken = $jsonInput['_csrf_token'] 
            ?? $jsonInput['csrf_token'] 
            ?? $_POST['_csrf_token'] 
            ?? $_POST['csrf_token'] 
            ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        if (!Csrf::validate($submittedToken)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'reply' => current_lang() === 'te' 
                    ? 'చెల్లని సెషన్ భద్రతా టోకెన్ (CSRF). దయచేసి పేజీని రీఫ్రెష్ చేయండి.' 
                    : 'Invalid security token (CSRF). Please refresh the page.',
                'error' => 'CSRF_INVALID'
            ]);
            return;
        }

        // Rate limiting (60 chatbot messages per minute per IP)
        $ip = RateLimiter::clientIp();
        $rateKey = "chatbot:{$ip}";
        if (RateLimiter::tooManyAttempts($rateKey, 60)) {
            $seconds = RateLimiter::availableIn($rateKey);
            http_response_code(429);
            echo json_encode([
                'success' => false,
                'reply' => current_lang() === 'te'
                    ? "మీరు చాలా సందేశాలను పంపారు. దయచేసి {$seconds} సెకన్ల తర్వాత ప్రయత్నించండి."
                    : "Too many messages sent. Please wait {$seconds} seconds.",
                'error' => 'RATE_LIMIT_EXCEEDED'
            ]);
            return;
        }
        RateLimiter::hit($rateKey, 60);

        $userMessage = trim($jsonInput['message'] ?? $_POST['message'] ?? '');
        $action = trim($jsonInput['action'] ?? $_POST['action'] ?? '');
        $lang = Session::get('lang', 'te');

        if (empty($userMessage) && empty($action)) {
            echo json_encode([
                'success' => true,
                'reply' => $lang === 'te' 
                    ? 'నమస్కారం! నేను స్మార్ట్ గ్రామ్ సహాయకుడిని. మీకు ఏ సహాయం కావాలో క్రింది ఎంపికలను ఎంచుకోండి లేదా మీ సమస్యను టైప్ చేయండి.'
                    : 'Hello! I am your Smart Gram Assistant. Choose an option below or describe your grievance/query.',
                'quick_replies' => self::getQuickReplies($lang)
            ]);
            return;
        }

        // Process query and build dynamic response
        $response = self::processQuery($userMessage, $action, $lang);
        echo json_encode($response);
    }

    /**
     * Core NLP & Action routing logic
     */
    private static function processQuery(string $message, string $action, string $lang): array {
        $msgLower = mb_strtolower($message, 'UTF-8');

        // Action 1: Track Grievance Status Prompt / Specific Tracking ID Lookup
        if ($action === 'track_prompt' || str_contains($msgLower, 'track') || str_contains($msgLower, 'ఫిర్యాదు స్థితి') || str_contains($msgLower, 'స్టేటస్')) {
            // Check if user included a tracking ID in the message (e.g. GRV-2026-00001 or SG-2026-00001 or pattern with digits)
            preg_match('/(GRV|SG)[-\s]?\d{4}[-\s]?\d{4,5}/i', $message, $matches);
            if (!empty($matches[0])) {
                $cleanId = strtoupper(str_replace(' ', '', $matches[0]));
                return self::lookupGrievance($cleanId, $lang);
            }

            return [
                'success' => true,
                'reply' => $lang === 'te'
                    ? '🔍 మీ **ఫిర్యాదు ట్రాకింగ్ ఐడీ** ని నమోదు చేయండి (ఉదాహరణ: `GRV-2026-00001`). <br>లేదా నేరుగా ట్రాకింగ్ పేజీని సందర్శించండి:'
                    : '🔍 Please enter your **Grievance Tracking ID** (e.g., `GRV-2026-00001`). <br>Or visit the tracking portal directly:',
                'quick_replies' => [
                    ['label' => $lang === 'te' ? '🌐 ట్రాకింగ్ పేజీకి వెళ్లండి' : '🌐 Go to Tracking Page', 'action' => 'url', 'url' => '/track'],
                    ['label' => $lang === 'te' ? '📝 క్రొత్త ఫిర్యాదు ఇవ్వండి' : '📝 Submit New Grievance', 'action' => 'url', 'url' => '/submit']
                ]
            ];
        }

        // If message direct match with Tracking ID pattern
        preg_match('/(GRV|SG)[-\s]?\d{4}[-\s]?\d{4,5}/i', $message, $matches);
        if (!empty($matches[0])) {
            $cleanId = strtoupper(str_replace(' ', '', $matches[0]));
            return self::lookupGrievance($cleanId, $lang);
        }

        // Action 2: Panchayat Helpline & Emergency Contacts
        if ($action === 'helpline' || str_contains($msgLower, 'helpline') || str_contains($msgLower, 'contact') || str_contains($msgLower, 'phone') || str_contains($msgLower, 'నంబర్') || str_contains($msgLower, 'ఫోన్') || str_contains($msgLower, 'అధికారులు')) {
            $replyTe = '📞 **స్మార్ట్ గ్రామ్ పంచాయతీ ముఖ్యాంశాలు & అత్యవసర కాంటాక్ట్‌లు:**<br><br>' .
                '• 🏛️ **పంచాయతీ కార్యాలయం:** 0863-2345678 (ఉదయం 9:00 - సాయంత్రం 5:00)<br>' .
                '• 👤 **పంచాయతీ కార్యదర్శి:** +91 94400 12345<br>' .
                '• ⚡ **విద్యుత్ విభాగాధిపతి (AE):** +91 94400 54321<br>' .
                '• 🚰 **తాగునీటి లీకేజీ హెల్ప్‌లైన్:** +91 94400 98765<br>' .
                '• 🚨 **అత్యవసర హెల్ప్‌లైన్:** 112 / 108';

            $replyEn = '📞 **Smart Gram Panchayat Key Contacts & Emergency Lines:**<br><br>' .
                '• 🏛️ **Panchayat Office:** 0863-2345678 (9:00 AM - 5:00 PM)<br>' .
                '• 👤 **Panchayat Secretary:** +91 94400 12345<br>' .
                '• ⚡ **Electricity Dept (AE):** +91 94400 54321<br>' .
                '• 🚰 **Water Supply Helpline:** +91 94400 98765<br>' .
                '• 🚨 **Emergency Services:** 112 / 108';

            return [
                'success' => true,
                'reply' => $lang === 'te' ? $replyTe : $replyEn,
                'quick_replies' => self::getQuickReplies($lang)
            ];
        }

        // Action 3: How to Submit / Process
        if ($action === 'how_to_submit' || str_contains($msgLower, 'how to') || str_contains($msgLower, 'submit') || str_contains($msgLower, 'నమోదు') || str_contains($msgLower, 'ఎలా') || str_contains($msgLower, 'చేయాలి')) {
            $replyTe = '📝 **ఫిర్యాదు సులభంగా నమోదు చేసే విధానం:**<br><br>' .
                '1️⃣ **[ఫిర్యాదు పేజీకి వెళ్లండి](/submit)**<br>' .
                '2️⃣ మీ గ్రామం & వార్డు సంఖ్యను ఎంచుకోండి.<br>' .
                '3️⃣ సమస్య కేటగిరీ (నీరు, విద్యుత్, రోడ్లు, చెత్త) ఎంచుకోండి.<br>' .
                '4️⃣ మీ సమస్యను వివరంగా రాసి ఫోటో (ఉంటే) అప్‌లోడ్ చేయండి.<br>' .
                '5️⃣ **సబ్‌మిట్** చేయగానే మీకు ఒక **ట్రాకింగ్ ఐడీ (GRV-...)** వస్తుంది.';

            $replyEn = '📝 **How to Submit a Grievance:**<br><br>' .
                '1️⃣ **[Go to Grievance Form](/submit)**<br>' .
                '2️⃣ Select your Village & Ward number.<br>' .
                '3️⃣ Choose the Category (Water, Electricity, Sanitation, Roads).<br>' .
                '4️⃣ Describe your issue and attach photo (optional).<br>' .
                '5️⃣ Click **Submit** to instantly receive your **Tracking ID (GRV-...)**.';

            return [
                'success' => true,
                'reply' => $lang === 'te' ? $replyTe : $replyEn,
                'quick_replies' => [
                    ['label' => $lang === 'te' ? '📝 ఇప్పుడు ఫిర్యాదు ఇవ్వండి' : '📝 Submit Grievance Now', 'action' => 'url', 'url' => '/submit'],
                    ['label' => $lang === 'te' ? '🔍 ఫిర్యాదు స్థితి చూడండి' : '🔍 Track Status', 'action' => 'track_prompt']
                ]
            ];
        }

        // Action 4: Office Working Hours
        if ($action === 'working_hours' || str_contains($msgLower, 'hour') || str_contains($msgLower, 'timing') || str_contains($msgLower, 'సమయం') || str_contains($msgLower, 'వేళలు')) {
            $replyTe = '⏰ **పంచాయతీ కార్యాలయ పని వేళలు:**<br><br>' .
                '• **సోమవారం - శనివారం:** ఉదయం 9:00 నుండి సాయంత్రం 5:00 వరకు<br>' .
                '• **ఆదివారం & ప్రభుత్వ సెలవులు:** సెలవు రోజు<br>' .
                '• 🌐 **ఆన్‌లైన్ ఫిర్యాదుల స్వీకరణ:** 24/7 అందుబాటులో ఉంటుంది!';

            $replyEn = '⏰ **Panchayat Office Working Hours:**<br><br>' .
                '• **Monday - Saturday:** 9:00 AM to 5:00 PM<br>' .
                '• **Sundays & Public Holidays:** Closed<br>' .
                '• 🌐 **Online Grievance Portal:** Available 24/7!';

            return [
                'success' => true,
                'reply' => $lang === 'te' ? $replyTe : $replyEn,
                'quick_replies' => self::getQuickReplies($lang)
            ];
        }

        // Action 5: Greetings / Hi / Hello
        if (in_array($msgLower, ['hi', 'hello', 'hey', 'హలో', 'నమస్తే', 'నమస్కారం', 'namaste'])) {
            $replyTe = 'నమస్కారం! 🙏 నేను స్మార్ట్ గ్రామ్ AI సహాయకుడిని. గ్రామ సమస్యలు, తాగునీరు, విద్యుత్, చెత్త సమస్యలపై ఫిర్యాదు చేయడానికి లేదా మీ ఫిర్యాదు స్థితిని తెలుసుకోవడానికి నేను మీకు సహాయం చేస్తాను.';
            $replyEn = 'Hello! 🙏 Welcome to Smart Gram AI Assistant. I can help you report issues regarding drinking water, electricity, sanitation, roads, or track your submitted complaints.';

            return [
                'success' => true,
                'reply' => $lang === 'te' ? $replyTe : $replyEn,
                'quick_replies' => self::getQuickReplies($lang)
            ];
        }

        // Action 6: Smart Heuristic AI Classification on Complaint Description
        if (mb_strlen($message, 'UTF-8') >= 5) {
            $analysis = AiAdvisoryService::analyzeWithHeuristicNlp($message);

            $categoryName = $lang === 'te' ? $analysis['category_name_te'] : $analysis['category_name_en'];
            $priority = strtoupper($analysis['suggested_priority']);
            $urgencyReason = $lang === 'te' ? $analysis['urgency_reason_te'] : $analysis['urgency_reason_en'];

            $priorityBadge = match($analysis['suggested_priority']) {
                'critical' => '<span class="badge bg-danger">CRITICAL / అత్యవసరం</span>',
                'high' => '<span class="badge bg-warning text-dark">HIGH / అధికం</span>',
                default => '<span class="badge bg-info text-dark">MEDIUM / సాధారణం</span>'
            };

            $replyTe = "🤖 **AI విశ్లేషణ పూర్తయింది:**<br><br>" .
                "• **సూచించిన వర్గం:** <b>{$categoryName}</b><br>" .
                "• **ప్రాధాన్యత:** {$priorityBadge}<br>" .
                "• **విశ్లేషణ:** {$urgencyReason}<br><br>" .
                "మీ సమస్యను పరిశీలించాము! సంబంధిత విభాగానికి నేరుగా నమోదు చేయడానికి క్రింది బటన్ క్లిక్ చేయండి:";

            $replyEn = "🤖 **AI Issue Analysis:**<br><br>" .
                "• **Suggested Category:** <b>{$categoryName}</b><br>" .
                "• **Estimated Priority:** {$priorityBadge}<br>" .
                "• **Rationale:** {$urgencyReason}<br><br>" .
                "Click below to file this issue under the pre-selected category:";

            return [
                'success' => true,
                'reply' => $lang === 'te' ? $replyTe : $replyEn,
                'analysis' => $analysis,
                'quick_replies' => [
                    [
                        'label' => $lang === 'te' ? "📝 '{$categoryName}' నమోదు చేయండి" : "📝 File under '{$categoryName}'",
                        'action' => 'url',
                        'url' => '/submit?category_id=' . $analysis['suggested_category_id']
                    ],
                    [
                        'label' => $lang === 'te' ? '🔍 ఇతర సేవలు' : '🔍 Other Services',
                        'action' => 'quick',
                        'value' => 'hi'
                    ]
                ]
            ];
        }

        // Fallback Default Response
        $fallbackTe = 'క్షమించండి, మీ అభ్యర్థన పూర్తిగా అర్థం కాలేదు. దయచేసి క్రింది సాధారణ విషయాల్లో ఒకటి ఎంచుకోండి లేదా మీ ఫిర్యాదును వివరంగా తెలపండి:';
        $fallbackEn = 'I could not fully understand your query. Please choose one of the quick options below or describe your grievance in detail:';

        return [
            'success' => true,
            'reply' => $lang === 'te' ? $fallbackTe : $fallbackEn,
            'quick_replies' => self::getQuickReplies($lang)
        ];
    }

    /**
     * Search Grievance by Tracking ID and format pretty response
     */
    private static function lookupGrievance(string $trackingId, string $lang): array {
        $grievance = Grievance::findByTrackingId($trackingId);

        if (!$grievance) {
            $msgTe = "❌ **ట్రాకింగ్ ఐడీ '{$trackingId}' కనుగొనబడలేదు.**<br>దయచేసి మీరు నమోదు చేసిన ఐడీ సరిగ్గా ఉందో లేదో తనిఖీ చేయండి (ఉదా: GRV-2026-00001).";
            $msgEn = "❌ **Tracking ID '{$trackingId}' not found.**<br>Please verify your tracking ID format (e.g., GRV-2026-00001).";

            return [
                'success' => true,
                'reply' => $lang === 'te' ? $msgTe : $msgEn,
                'quick_replies' => [
                    ['label' => $lang === 'te' ? '🔍 మళ్లీ శోధించండి' : '🔍 Try Again', 'action' => 'track_prompt'],
                    ['label' => $lang === 'te' ? '🌐 వివరమైన శోధన పేజీ' : '🌐 Detailed Search Page', 'action' => 'url', 'url' => '/track']
                ]
            ];
        }

        $status = $grievance['status'];
        $statusBadge = match($status) {
            'submitted' => '<span class="badge bg-secondary">Submitted / నమొదైంది</span>',
            'verified' => '<span class="badge bg-info text-dark">Verified / పరిశీలనలో</span>',
            'assigned' => '<span class="badge bg-primary">Assigned / కేటాయించబడింది</span>',
            'in_progress' => '<span class="badge bg-warning text-dark">In Progress / పని జరుగుతోంది</span>',
            'resolved' => '<span class="badge bg-success">Resolved / పరిష్కరించబడింది</span>',
            'rejected' => '<span class="badge bg-danger">Rejected / తిరస్కరించబడింది</span>',
            default => '<span class="badge bg-dark">' . ucfirst($status) . '</span>'
        };

        $category = $lang === 'te' ? ($grievance['category_name_te'] ?? $grievance['category_name_en']) : $grievance['category_name_en'];
        $dept = $lang === 'te' ? ($grievance['dept_name_te'] ?? $grievance['dept_name_en']) : $grievance['dept_name_en'];
        $created = date('d M Y, h:i A', strtotime($grievance['created_at']));
        $officer = !empty($grievance['assigned_officer_name']) ? e($grievance['assigned_officer_name']) : ($lang === 'te' ? 'కేటాయించబడుతోంది' : 'Pending Assignment');

        $replyTe = "📋 **ఫిర్యాదు వివరాలు ({$grievance['tracking_id']}):**<br><br>" .
            "• **స్థితి:** {$statusBadge}<br>" .
            "• **సమస్య రకం:** {$category}<br>" .
            "• **విభాగం:** {$dept}<br>" .
            "• **బాధ్యతాయుత అధికారి:** {$officer}<br>" .
            "• **నమోదైన తేదీ:** {$created}<br><br>" .
            "పూర్తి సమాచారం, కాలక్రమం (Timeline) మరియు ఫోటోలను చూడటానికి క్రింది బటన్ క్లిక్ చేయండి:";

        $replyEn = "📋 **Grievance Details ({$grievance['tracking_id']}):**<br><br>" .
            "• **Status:** {$statusBadge}<br>" .
            "• **Category:** {$category}<br>" .
            "• **Department:** {$dept}<br>" .
            "• **Assigned Officer:** {$officer}<br>" .
            "• **Submitted On:** {$created}<br><br>" .
            "Click below to view complete timeline, attachments, and officer updates:";

        return [
            'success' => true,
            'reply' => $lang === 'te' ? $replyTe : $replyEn,
            'quick_replies' => [
                [
                    'label' => $lang === 'te' ? '👁️ పూర్తి వివరణ చూడండి' : '👁️ View Full Timeline & Details',
                    'action' => 'url',
                    'url' => '/track?tracking_id=' . urlencode($grievance['tracking_id'])
                ],
                [
                    'label' => $lang === 'te' ? '🔍 మరొక ఐడీ చూడండి' : '🔍 Track Another Grievance',
                    'action' => 'track_prompt'
                ]
            ]
        ];
    }

    /**
     * Standard Quick Replies Chips
     */
    private static function getQuickReplies(string $lang): array {
        if ($lang === 'te') {
            return [
                ['label' => '🔍 ఫిర్యాదు స్థితి చూడండి', 'action' => 'track_prompt'],
                ['label' => '📝 క్రొత్త ఫిర్యాదు ఎలా ఇవ్వాలి?', 'action' => 'how_to_submit'],
                ['label' => '📞 అత్యవసర కాంటాక్ట్‌లు', 'action' => 'helpline'],
                ['label' => '⏰ కార్యాలయ పని వేళలు', 'action' => 'working_hours']
            ];
        }

        return [
            ['label' => '🔍 Track Grievance Status', 'action' => 'track_prompt'],
            ['label' => '📝 How to Submit Grievance?', 'action' => 'how_to_submit'],
            ['label' => '📞 Emergency Contacts', 'action' => 'helpline'],
            ['label' => '⏰ Panchayat Working Hours', 'action' => 'working_hours']
        ];
    }
}
