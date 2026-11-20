<?php
// ==============================================================================
// Smart Gram - AI Advisory Service (Human-in-the-Loop)
// Automated category suggestions, priority assessment, summary & duplicate detection.
// Decoupled architecture with resilient offline heuristic NLP engine.
// ==============================================================================

namespace App\Services;

use App\Core\Database;
use App\Models\AuditLog;
use App\Models\Grievance;
use Exception;
use PDO;

class AiAdvisoryService {

    /**
     * Perform comprehensive AI advisory analysis on a grievance description
     *
     * @param string $description The citizen's grievance text
     * @param int|null $wardId Village ward identifier
     * @param int|null $currentGrievanceId Exclude current grievance from duplicate checks
     * @return array Analytical insights and recommendations
     */
    public static function analyzeGrievance(string $description, ?int $wardId = null, ?int $currentGrievanceId = null): array {
        $descriptionTrimmed = trim($description);

        // 1. Attempt External Gemini LLM API if key is configured
        $apiKey = config('ai')['gemini_api_key'] ?? '';
        $aiResult = null;

        if (!empty($apiKey) && !empty($descriptionTrimmed)) {
            $aiResult = self::callGeminiApi($descriptionTrimmed, $apiKey);
        }

        // 2. Fall back to Native Heuristic NLP Engine (Guaranteed zero-failure offline operation)
        if (!$aiResult) {
            $aiResult = self::analyzeWithHeuristicNlp($descriptionTrimmed);
        }

        // 3. Duplicate / Similar Complaint Detection in the same ward
        $duplicates = self::detectDuplicates($descriptionTrimmed, $wardId, $currentGrievanceId, $aiResult['suggested_category_id']);
        $aiResult['potential_duplicates'] = $duplicates;

        return $aiResult;
    }

    /**
     * Resilient Heuristic NLP engine for Telugu and English rural complaint analysis
     */
    public static function analyzeWithHeuristicNlp(string $text): array {
        $textLower = mb_strtolower($text, 'UTF-8');

        // Category Dictionary: Category ID => [Keywords in Telugu & English, Department ID, Category Name EN, Category Name TE]
        $categories = [
            // Water Supply (Dept 1)
            1 => [ // Pipeline Leakage
                'keywords' => ['లీకేజీ', 'పైపులైన్', 'పగిలిన', 'నీరు కారుతోంది', 'leak', 'pipeline', 'burst', 'gushing', 'pipe broken'],
                'dept_id' => 1,
                'name_en' => 'Pipeline Leakage / Damage',
                'name_te' => 'పైపులైన్ లీకేజీ / పగిలిన పైపు',
                'base_priority' => 'high'
            ],
            2 => [ // No Water Supply in Tap
                'keywords' => ['నీరు రాకపోవడం', 'కుళాయి', 'మంచినీరు లేదు', 'బోరు పాడైంది', 'no water', 'tap dry', 'water supply stopped', 'borewell'],
                'dept_id' => 1,
                'name_en' => 'No Water Supply in Tap',
                'name_te' => 'కుళాయిల్లో నీరు రాకపోవడం',
                'base_priority' => 'critical'
            ],
            3 => [ // Contaminated / Muddy Water
                'keywords' => ['కలుషిత', 'మురుగు నీరు', 'వాసన', 'మట్టి నీరు', 'రోగం', 'contaminated', 'muddy water', 'dirty water', 'smell', 'sick', 'vomiting'],
                'dept_id' => 1,
                'name_en' => 'Contaminated / Muddy Water',
                'name_te' => 'కలుషిత నీరు / మురుగు వాసన',
                'base_priority' => 'critical'
            ],
            // Electricity & Street Lights (Dept 2)
            4 => [ // Street Light Not Working
                'keywords' => ['వీధి దీపం', 'దీపం వెలగడం లేదు', 'చీకటి', 'బల్బు', 'street light', 'light off', 'darkness', 'bulb fused', 'lamp post'],
                'dept_id' => 2,
                'name_en' => 'Street Light Not Working',
                'name_te' => 'వీధి దీపం వెలగడం లేదు',
                'base_priority' => 'medium'
            ],
            5 => [ // Loose / Hanging Electric Wire
                'keywords' => ['తీగలు', 'వేలాడుతున్న', 'కరెంట్ తీగ', 'షాక్', 'స్పార్క్', 'ట్రాన్స్‌ఫార్మర్', 'wire', 'hanging wire', 'electric wire', 'spark', 'shock', 'transformer'],
                'dept_id' => 2,
                'name_en' => 'Loose / Hanging Electric Wire',
                'name_te' => 'వేలాడుతున్న విద్యుత్ తీగలు',
                'base_priority' => 'critical'
            ],
            // Sanitation & Waste Management (Dept 3)
            6 => [ // Garbage Heap
                'keywords' => ['చెత్త', 'కుప్పలు', 'చెత్త పేరుకుపోవడం', 'చెత్త కుండి', 'garbage', 'trash', 'waste', 'dump', 'garbage heap', 'plastic'],
                'dept_id' => 3,
                'name_en' => 'Garbage Heap Not Cleaned',
                'name_te' => 'చెత్త కుప్పలు పేరుకుపోవడం',
                'base_priority' => 'medium'
            ],
            7 => [ // Public Toilet
                'keywords' => ['మరుగుదొడ్ల', 'టాయిలెట్', 'ప్రజా మరుగుదొడ్డి', 'toilet', 'public toilet', 'urinal', 'restroom'],
                'dept_id' => 3,
                'name_en' => 'Public Toilet Cleaning Required',
                'name_te' => 'ప్రజా మరుగుదొడ్ల పరిశుభ్రత',
                'base_priority' => 'high'
            ],
            // Drainage & Sewage (Dept 4)
            8 => [ // Drainage Overflow
                'keywords' => ['మురుగు కాల్వ', 'రోడ్డుపైకి మురుగు', 'పొంగడం', 'డ్రైనేజీ', 'drainage', 'drain overflow', 'sewage', 'canal overflow'],
                'dept_id' => 4,
                'name_en' => 'Drainage Overflow on Road',
                'name_te' => 'రోడ్డుపైకి మురుగునీరు పొంగడం',
                'base_priority' => 'high'
            ],
            9 => [ // Drain Blockage / Desilting
                'keywords' => ['పూడిక', 'కాల్వల్లో పూడిక', 'జామ్', 'చెత్త పేరుకుపోయింది', 'desilting', 'drain blocked', 'silt', 'choked drain'],
                'dept_id' => 4,
                'name_en' => 'Drain Blockage / Desilting',
                'name_te' => 'కాల్వల్లో పూడిక పేరుకుపోవడం',
                'base_priority' => 'medium'
            ],
            // Roads & Infrastructure (Dept 5)
            10 => [ // Potholes / Road Broken
                'keywords' => ['రోడ్డు గుంతలు', 'పాడైన రోడ్డు', 'సిమెంట్ రోడ్డు', 'వంతెన', 'కల్వర్టు', 'pothole', 'broken road', 'damaged road', 'pavement', 'trench'],
                'dept_id' => 5,
                'name_en' => 'Potholes / Road Broken',
                'name_te' => 'రోడ్డు గుంతలు / పాడైన రోడ్డు',
                'base_priority' => 'medium'
            ],
        ];

        // Score matches across categories
        $bestMatchId = 1;
        $highestScore = 0;

        foreach ($categories as $catId => $data) {
            $score = 0;
            foreach ($data['keywords'] as $kw) {
                if (str_contains($textLower, mb_strtolower($kw, 'UTF-8'))) {
                    $score += 2;
                }
            }
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatchId = $catId;
            }
        }

        $matched = $categories[$bestMatchId];
        $confidence = $highestScore > 0 ? min(0.96, 0.65 + ($highestScore * 0.08)) : 0.50;

        // Evaluate priority adjustments based on severe hazard indicators
        $criticalKeywords = ['షాక్', 'shock', 'తీగ', 'wire', 'కలుషిత', 'contaminated', 'మరణం', 'danger', 'చాలా ప్రమాదం', 'hazard', 'urgency', 'urgent'];
        $isCritical = false;
        foreach ($criticalKeywords as $ck) {
            if (str_contains($textLower, mb_strtolower($ck, 'UTF-8'))) {
                $isCritical = true;
                break;
            }
        }

        $suggestedPriority = $isCritical ? 'critical' : $matched['base_priority'];

        $urgencyReasonEn = match($suggestedPriority) {
            'critical' => 'Presents immediate safety or public health hazard; requires emergency intervention.',
            'high'     => 'Significant disruption to village daily life; recommended for same-day dispatch.',
            'medium'   => 'Standard operational concern with established 24-48 hour resolution timeframe.',
            default    => 'Routine infrastructure maintenance issue.'
        };

        $urgencyReasonTe = match($suggestedPriority) {
            'critical' => 'ప్రజల భద్రత లేదా ఆరోగ్యానికి తక్షణ ప్రమాదం ఉన్నందున అత్యవసర పరిష్కారం అవసరం.',
            'high'     => 'గ్రామస్తుల దైనందిన జీవనానికి తీవ్ర అంతరాయం కలుగుతోంది; అదే రోజు పరిష్కరించాలి.',
            'medium'   => 'సాధారణ సమస్య; 24-48 గంటల్లో పరిష్కరించేందుకు షెడ్యూల్ చేయవచ్చు.',
            default    => 'సాధారణ నిర్వహణ పనుల పరిధిలోకి వస్తుంది.'
        };

        // Synthesize 1-line executive summary
        $cleanDesc = preg_replace('/\s+/', ' ', $text);
        $summaryEn = mb_substr($cleanDesc, 0, 110) . (mb_strlen($cleanDesc) > 110 ? '...' : '');
        $summaryTe = $matched['name_te'] . ' — ' . (mb_substr($cleanDesc, 0, 70)) . '...';

        return [
            'suggested_category_id'   => $bestMatchId,
            'category_name_en'        => $matched['name_en'],
            'category_name_te'        => $matched['name_te'],
            'suggested_department_id' => $matched['dept_id'],
            'suggested_priority'      => $suggestedPriority,
            'urgency_reason_en'       => $urgencyReasonEn,
            'urgency_reason_te'       => $urgencyReasonTe,
            'summary_en'              => $summaryEn,
            'summary_te'              => $summaryTe,
            'confidence'              => round($confidence, 2),
            'is_ai_powered'           => false // Heuristic NLP fallback
        ];
    }

    /**
     * Optional Gemini API integration (HTTP direct call, zero SDK dependencies)
     */
    private static function callGeminiApi(string $prompt, string $apiKey): ?array {
        $model = config('ai')['model'] ?? 'gemini-1.5-flash';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $systemInstruction = "You are an AI assistant for a Gram Panchayat Village Grievance System. Analyze this rural grievance description and return a JSON object with: suggested_category_id (1-10), suggested_priority (low, medium, high, critical), summary_en (1 line), summary_te (1 line in Telugu), urgency_reason_en, urgency_reason_te, confidence (0.0 to 1.0). Return raw JSON only.";

        $postData = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemInstruction . "\n\nGrievance description:\n" . $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6); // Fast timeout to avoid blocking

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $rawResponse) {
            $json = json_decode($rawResponse, true);
            $candidateText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $parsed = json_decode($candidateText, true);

            if ($parsed && !empty($parsed['suggested_category_id'])) {
                $category = Grievance::getCategoryById((int)$parsed['suggested_category_id']);
                if ($category) {
                    return [
                        'suggested_category_id'   => (int)$category['id'],
                        'category_name_en'        => $category['name_en'],
                        'category_name_te'        => $category['name_te'],
                        'suggested_department_id' => (int)$category['department_id'],
                        'suggested_priority'      => $parsed['suggested_priority'] ?? 'medium',
                        'urgency_reason_en'       => $parsed['urgency_reason_en'] ?? '',
                        'urgency_reason_te'       => $parsed['urgency_reason_te'] ?? '',
                        'summary_en'              => $parsed['summary_en'] ?? '',
                        'summary_te'              => $parsed['summary_te'] ?? '',
                        'confidence'              => (float)($parsed['confidence'] ?? 0.90),
                        'is_ai_powered'           => true
                    ];
                }
            }
        }

        return null; // Graceful fallback to heuristic NLP
    }

    /**
     * Duplicate / Similar Complaint Detection within the same Ward
     */
    public static function detectDuplicates(string $description, ?int $wardId, ?int $currentGrievanceId = null, ?int $categoryId = null): array {
        if (empty($wardId) || empty($description)) {
            return [];
        }

        $db = Database::getConnection();
        $excludeSql = $currentGrievanceId ? "AND id != " . (int)$currentGrievanceId : "";

        // Find active grievances in the same ward within the last 14 days
        $stmt = $db->prepare("
            SELECT id, tracking_id, citizen_name, citizen_phone, category_id, description, status, created_at
            FROM grievances
            WHERE ward_id = :ward_id 
              AND status NOT IN ('closed', 'rejected')
              AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
              $excludeSql
            ORDER BY id DESC
            LIMIT 15
        ");
        $stmt->execute([':ward_id' => $wardId]);
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        $descLower = mb_strtolower(trim($description), 'UTF-8');
        $descWords = array_unique(preg_split('/\s+/', $descLower));

        foreach ($candidates as $cand) {
            $candLower = mb_strtolower(trim($cand['description']), 'UTF-8');
            $candWords = array_unique(preg_split('/\s+/', $candLower));

            // Jaccard similarity across word sets
            $intersection = count(array_intersect($descWords, $candWords));
            $union = count(array_unique(array_merge($descWords, $candWords)));
            $wordSim = ($union > 0) ? ($intersection / $union) : 0;

            // Category match bonus
            $categoryBonus = ($categoryId && (int)$cand['category_id'] === (int)$categoryId) ? 0.35 : 0.0;
            $totalSim = min(1.0, $wordSim + $categoryBonus);

            if ($totalSim >= 0.40) {
                $category = Grievance::getCategoryById((int)$cand['category_id']);
                $results[] = [
                    'id'               => (int)$cand['id'],
                    'tracking_id'      => $cand['tracking_id'],
                    'citizen_name'     => $cand['citizen_name'],
                    'status'           => $cand['status'],
                    'category_name_en' => $category['name_en'] ?? '',
                    'category_name_te' => $category['name_te'] ?? '',
                    'similarity_pct'   => round($totalSim * 100),
                    'created_at'       => $cand['created_at']
                ];
            }
        }

        // Sort by highest similarity
        usort($results, fn($a, $b) => $b['similarity_pct'] <=> $a['similarity_pct']);
        return array_slice($results, 0, 3);
    }

    /**
     * Human-in-the-Loop Action: Staff approves and applies AI recommendations
     */
    public static function applySuggestions(int $grievanceId, int $userId, array $params): void {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $checkStmt = $db->prepare("SELECT status, tracking_id FROM grievances WHERE id = :id");
            $checkStmt->execute([':id' => $grievanceId]);
            $current = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$current) {
                throw new Exception("Grievance not found.");
            }

            $sql = "UPDATE grievances SET updated_at = NOW()";
            $updateParams = [':id' => $grievanceId];

            if (!empty($params['category_id'])) {
                $category = Grievance::getCategoryById((int)$params['category_id']);
                if ($category) {
                    $sql .= ", category_id = :category_id, department_id = :dept_id";
                    $updateParams[':category_id'] = (int)$category['id'];
                    $updateParams[':dept_id'] = (int)$category['department_id'];
                }
            }

            if (!empty($params['priority']) && in_array($params['priority'], ['low', 'medium', 'high', 'critical'], true)) {
                $sql .= ", priority = :priority, ai_suggested_priority = :ai_priority";
                $updateParams[':priority'] = $params['priority'];
                $updateParams[':ai_priority'] = $params['priority'];
            }

            if (!empty($params['summary'])) {
                $sql .= ", ai_summary = :summary";
                $updateParams[':summary'] = trim($params['summary']);
            }

            if (!empty($params['confidence'])) {
                $sql .= ", ai_confidence = :confidence";
                $updateParams[':confidence'] = (float)$params['confidence'];
            }

            if (!empty($params['duplicate_of_id'])) {
                $sql .= ", ai_duplicate_of_id = :dup_id";
                $updateParams[':dup_id'] = (int)$params['duplicate_of_id'];
            }

            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute($updateParams);

            // Log event in timeline
            $timelineStmt = $db->prepare("
                INSERT INTO grievance_updates (grievance_id, user_id, action, from_status, to_status, remarks, created_at)
                VALUES (:gid, :uid, 'ai_accepted', :from_status, :to_status, :remarks, NOW())
            ");
            $remarks = "Panchayat Staff accepted AI Advisory: Updated category and priority to " . strtoupper($params['priority'] ?? 'MEDIUM') . ". Summary: " . ($params['summary'] ?? 'N/A');
            $timelineStmt->execute([
                ':gid'         => $grievanceId,
                ':uid'         => $userId,
                ':from_status' => $current['status'],
                ':to_status'   => $current['status'],
                ':remarks'     => $remarks
            ]);

            AuditLog::log('ai_accepted', 'grievances', $grievanceId, $userId, [
                'tracking_id' => $current['tracking_id'],
                'priority'    => $params['priority'] ?? null,
                'category_id' => $params['category_id'] ?? null
            ]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
