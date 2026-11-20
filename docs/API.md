# 🔌 Smart Gram — API Reference & Integration Specifications

> **System:** Smart Gram (గ్రామ సమస్య పరిష్కార వేదిక)  
> **Protocol:** HTTP/1.1 & HTTP/2 with JSON payloads  
> **Security:** HMAC SHA-256 Signatures, Timing-Safe Comparison, Rate Limiting

---

## 📑 Table of Contents
1. [Overview & Standards](#1-overview--standards)
2. [Public API Endpoints](#2-public-api-endpoints)
   - 2.1 [System Health Check Probe (`GET /health`)](#21-system-health-check-probe-get-health)
   - 2.2 [Dynamic Wards Query (`GET /api/wards`)](#22-dynamic-wards-query-get-apiwards)
3. [n8n Webhook Integration Protocol](#3-n8n-webhook-integration-protocol)
   - 3.1 [HMAC SHA-256 Authentication](#31-hmac-sha-256-authentication)
   - 3.2 [Standard Envelope Schema](#32-standard-envelope-schema)
4. [Webhook Event Payloads](#4-webhook-event-payloads)
   - 4.1 [`grievance.submitted`](#41-grievancesubmitted)
   - 4.2 [`grievance.assigned`](#42-grievanceassigned)
   - 4.3 [`grievance.work_started`](#43-grievancework_started)
   - 4.4 [`grievance.resolved`](#44-grievanceresolved)
   - 4.5 [`grievance.reopened`](#45-grievancereopened)
   - 4.6 [`sla_breach_escalation`](#46-sla_breach_escalation)
5. [AI Advisory Schema](#5-ai-advisory-schema)
6. [Error Handling & HTTP Status Codes](#6-error-handling--http-status-codes)

---

## 1. Overview & Standards

Smart Gram provides a decoupled API architecture. Public citizen endpoints do not require API keys or citizen authentication. Automated webhook dispatches use cryptographic HMAC SHA-256 signatures for zero-trust webhook consumers (like n8n, WhatsApp Cloud API, and SMS gateways).

All timestamps are formatted according to ISO 8601 / RFC 3339 standard (e.g. `2026-09-30T10:00:00+05:30`).

---

## 2. Public API Endpoints

### 2.1 System Health Check Probe (`GET /health`)
Used by load balancers, uptime monitors (Pingdom, Uptime Kuma), and Kubernetes/Docker health probes.

- **URL:** `/health`
- **Method:** `GET`
- **Authentication:** None (Public)
- **Rate Limit:** 60 requests / minute / IP

#### Response (HTTP 200 OK):
```json
{
    "status": "healthy",
    "timestamp": "2026-09-30T09:28:42+00:00",
    "app": {
        "name": "Smart Gram",
        "env": "production",
        "php_version": "8.2.33"
    },
    "checks": {
        "database": {
            "status": "connected",
            "latency_ms": 1.45
        },
        "storage": {
            "uploads_writable": true,
            "path": "storage/uploads"
        },
        "extensions": {
            "loaded": true,
            "missing": []
        }
    }
}
```

#### Degraded Response (HTTP 503 Service Unavailable):
```json
{
    "status": "degraded",
    "timestamp": "2026-09-30T09:28:42+00:00",
    "checks": {
        "database": {
            "status": "disconnected",
            "latency_ms": 0
        }
    }
}
```

---

### 2.2 Dynamic Wards Query (`GET /api/wards`)
Used dynamically by the citizen submission form via AJAX when selecting a village.

- **URL:** `/api/wards?village_id={id}`
- **Method:** `GET`
- **Authentication:** None (Public)

#### Request Query Parameters:
| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `village_id` | Integer | Yes | The ID of the Gram Panchayat village |

#### Response (HTTP 200 OK):
```json
{
    "success": true,
    "village_id": 1,
    "wards": [
        {
            "id": 1,
            "village_id": 1,
            "ward_number": "Ward 1",
            "ward_name": "వార్డు నం. 1 (ఉత్తర వీధి)"
        },
        {
            "id": 2,
            "village_id": 1,
            "ward_number": "Ward 2",
            "ward_name": "వార్డు నం. 2 (దక్షిణ వీధి)"
        }
    ]
}
```

---

## 3. n8n Webhook Integration Protocol

### 3.1 HMAC SHA-256 Authentication
Every outgoing HTTP request sent by `WebhookService` to n8n includes cryptographic verification headers:

| Header Name | Description | Example |
| :--- | :--- | :--- |
| `X-SmartGram-Signature` | Hex-encoded HMAC-SHA256 of the raw JSON body using the shared secret | `a5f8b9e...64chars` |
| `X-SmartGram-Event` | The event descriptor | `grievance.submitted` |
| `X-SmartGram-Timestamp` | Unix timestamp of the dispatch | `1759224522` |
| `Content-Type` | MIME type | `application/json; charset=utf-8` |

#### Signature Verification (Node.js / n8n Function Node):
```javascript
const crypto = require('crypto');
const signature = $request.headers['x-smartgram-signature'];
const secret = 'your-n8n-webhook-secret';
const expectedSig = crypto
    .createHmac('sha256', secret)
    .update($request.rawBody)
    .digest('hex');

if (signature !== expectedSig) {
    throw new Error('401 Unauthorized: Invalid HMAC Signature');
}
```

---

### 3.2 Standard Envelope Schema
All webhook payloads share an identical top-level envelope:
```json
{
    "event": "string",
    "event_id": "string (UUID)",
    "timestamp": "ISO-8601 string",
    "app": "Smart Gram",
    "version": "1.0",
    "data": {}
}
```

---

## 4. Webhook Event Payloads

### 4.1 `grievance.submitted`
Dispatched when a rural citizen files a complaint without logging in.

```json
{
    "event": "grievance.submitted",
    "event_id": "evt_66f1234a56b78",
    "timestamp": "2026-09-30T14:40:00+05:30",
    "data": {
        "grievance_id": 42,
        "tracking_id": "GRV-2026-00042",
        "citizen_name": "లక్ష్మి నారాయణ",
        "citizen_phone": "9440123456",
        "village_name": "రామవరం (Ramavaram)",
        "ward_number": "Ward 2",
        "category_name": "మంచినీటి సరఫరా (Drinking Water)",
        "department_name": "గ్రామీణ నీటి సరఫరా (Rural Water Supply)",
        "description": "మంచినీటి పైప్‌లైన్ పగిలి రోడ్డుపై నీరు వృధాగా పోతోంది.",
        "has_photo": true,
        "photo_url": "https://smartgram.gov.in/storage/uploads/9b4f2c...jpg",
        "sla_hours": 24,
        "sla_due_at": "2026-10-01T14:40:00+05:30",
        "sms_template_te": "నమస్కారం లక్ష్మి నారాయణ గారు, మీ ఫిర్యాదు (ID: GRV-2026-00042) నమోదైంది. స్థితిని పరిశీలించడానికి: https://smartgram.gov.in/track?id=GRV-2026-00042",
        "sms_template_en": "Dear Lakshmi Narayana, your grievance GRV-2026-00042 has been registered. Track at: https://smartgram.gov.in/track?id=GRV-2026-00042"
    }
}
```

---

### 4.2 `grievance.assigned`
Dispatched when Panchayat Staff assigns a field officer to investigate the complaint.

```json
{
    "event": "grievance.assigned",
    "data": {
        "grievance_id": 42,
        "tracking_id": "GRV-2026-00042",
        "officer_name": "Water Works Field Officer",
        "officer_phone": "9876500003",
        "priority": "critical",
        "assignment_notes": "క్షేత్రస్థాయిలో సమస్య ధృవీకరించబడింది. తక్షణ మరమ్మతు అవసరం.",
        "assigned_at": "2026-09-30T15:00:00+05:30"
    }
}
```

---

### 4.3 `grievance.work_started`
Dispatched when the assigned field officer arrives on site and begins work.

```json
{
    "event": "grievance.work_started",
    "data": {
        "grievance_id": 42,
        "tracking_id": "GRV-2026-00042",
        "officer_name": "Water Works Field Officer",
        "progress_note": "మరమ్మతు పనులు ప్రారంభించబడ్డాయి. కొత్త పైప్‌లైన్ అమర్చుతున్నాము.",
        "status": "in_progress"
    }
}
```

---

### 4.4 `grievance.resolved`
Dispatched when the field officer completes work with mandatory photographic evidence.

```json
{
    "event": "grievance.resolved",
    "data": {
        "grievance_id": 42,
        "tracking_id": "GRV-2026-00042",
        "officer_name": "Water Works Field Officer",
        "resolution_notes": "పైప్‌లైన్ లీకేజీని అరికట్టి కొత్త వాల్వ్ విజయవంతంగా బిగించడమైనది.",
        "evidence_photo_url": "https://smartgram.gov.in/storage/uploads/c78d4e...jpg",
        "verification_url": "https://smartgram.gov.in/track?id=GRV-2026-00042&phone=9440123456"
    }
}
```

---

### 4.5 `grievance.reopened`
Dispatched if a citizen is dissatisfied with the resolution and reopens the grievance.

```json
{
    "event": "grievance.reopened",
    "data": {
        "grievance_id": 42,
        "tracking_id": "GRV-2026-00042",
        "citizen_phone": "9440123456",
        "reopen_reason": "వాల్వ్ బిగించినా ఇంకా లీక్ అవుతోంది. మళ్లీ పరిశీలించండి.",
        "previous_status": "resolved",
        "new_status": "reopened"
    }
}
```

---

### 4.6 `sla_breach_escalation`
Dispatched when the automated CLI scanner (`bin/cron_sla_scan.php`) detects a complaint that exceeded its SLA deadline.

```json
{
    "event": "sla_breach_escalation",
    "data": {
        "grievance_id": 42,
        "tracking_id": "GRV-2026-00042",
        "village_name": "రామవరం",
        "category_name": "మంచినీటి సరఫరా",
        "assigned_officer_name": "Water Works Field Officer",
        "sla_due_at": "2026-09-30T12:00:00+05:30",
        "hours_overdue": 3.5,
        "escalation_level": "Mandal Panchayat Officer (MPO) Escalation"
    }
}
```

---

## 5. AI Advisory Schema

The internal Heuristic Natural Language Processing engine analyzes grievance descriptions and returns structured suggestions for human staff:

```json
{
    "suggested_category_id": 1,
    "suggested_category_name": "మంచినీటి సరఫరా (Drinking Water)",
    "suggested_department_id": 1,
    "suggested_department_name": "గ్రామీణ నీటి సరఫరా విభాగం",
    "suggested_priority": "critical",
    "confidence_score": 0.96,
    "summary_te": "పాఠశాల సమీపంలో మంచినీటి పైప్‌లైన్ లీకేజీ సమస్య",
    "summary_en": "Drinking water pipeline leakage hazard near school",
    "duplicate_detection": {
        "is_duplicate_likely": true,
        "similarity_score": 0.68,
        "matching_tracking_id": "GRV-2026-00012",
        "same_ward": true
    }
}
```

---

## 6. Error Handling & HTTP Status Codes

| HTTP Status | Description | Cause |
| :--- | :--- | :--- |
| `200 OK` | Success | Normal successful API call or health probe |
| `400 Bad Request` | Validation Error | Missing parameters, invalid phone format |
| `403 Forbidden` | Access Denied | CSRF token mismatch, RBAC violation, or CLI-only script accessed via browser |
| `404 Not Found` | Resource Missing | Non-existent Grievance ID or unknown route |
| `429 Too Many Requests`| Rate Limited | Exceeded IP threshold (Login brute force, spam flooding, enumeration) |
| `503 Service Unavailable`| Degraded State | Database disconnected in `/health` check |

---

*Smart Gram API Reference Version 1.0.0.*
