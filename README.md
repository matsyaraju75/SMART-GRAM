# Smart Gram — గ్రామ సమస్య పరిష్కార వేదిక
> **Bilingual Panchayat / Village Grievance Management System**  
> *“మీ సమస్యను సులభంగా తెలియజేయండి” (Report your problem easily)*

---

## 📌 Project Overview
**Smart Gram** is a lightweight, accessible, bilingual web application designed specifically for rural citizens in Gram Panchayats. It enables citizens to report village-level issues (such as drinking water, street lights, road damage, sanitation, and drainage) directly to Panchayat authorities without unnecessary bureaucratic hurdles.

### 🌟 Core Architectural Principles
1. **Zero Citizen Login:** Rural citizens are **never** required to create an account, remember usernames/passwords, or verify emails. Submitting, tracking, and giving feedback are 100% login-free.
2. **Telugu-First & English:** Built natively with accessible, simple rural Telugu (`గ్రామీణ తెలుగు`) as the default language, with a 1-click toggle to English (`తెలుగు | English`).
3. **Internal Role-Based Access Control:** Panchayat Staff, Officers, and Admins have secure session-based portals to verify, assign, investigate, resolve, and monitor grievances.
4. **Decoupled Architecture:** Core PHP 8+ and MySQL 8+ functionality runs independently without hard dependencies on external AI or n8n services.
5. **Human-in-the-Loop:** AI features (in Phase 7) provide advisory suggestions only; authorized human staff always make final operational decisions.

---

## 🗂️ Project Directory Layout

```
smart-gram/
├── docs/                   # Architecture, database schema, API, and setup documentation
│   ├── ARCHITECTURE.md     # System design & lifecycle specifications
│   ├── DATABASE.md         # Database schema & ER diagram
│   └── SETUP.md            # Local environment & XAMPP guide
├── public/                 # Public web root (accessible via browser)
│   ├── index.php           # Front controller (Phase 1)
│   ├── .htaccess           # Security headers & URL rewrite rules
│   └── assets/             # CSS stylesheets, JavaScript, and logos/icons
├── app/                    # Application source code (Phase 1)
│   ├── core/               # Router, Database (PDO), Session, CSRF
│   ├── controllers/        # Request controllers (Public & Protected)
│   ├── models/             # Database access models
│   ├── services/           # Business services (File upload, GrievanceService)
│   └── helpers/            # Bilingual translator, input sanitizers
├── config/                 # Configuration files (Database, App settings)
├── lang/                   # Bilingual dictionary files (te/ and en/)
├── views/                  # View templates (Citizen, Staff, Officer, Admin)
├── database/               # SQL migration and seed scripts
├── storage/                # Protected file uploads & application logs
├── .env.example            # Environment configuration template
├── .gitignore              # Git ignore rules
└── README.md               # Project overview
```

---

## 🚦 Phase Roadmap Tracker

- [x] **Phase 0: Architecture & Project Planning** *(Completed)*
- [x] **Phase 1: Project Setup, Database (PDO) & Internal Auth (Staff/Officer/Admin)** *(Completed)*
- [x] **Phase 2: Citizen Public Interface (Telugu-First + English, No Login)** *(Completed)*
- [x] **Phase 3: Citizen Grievance Submission & Grievance ID Generation** *(Completed)*
- [x] **Phase 4: Grievance Tracking, Timeline & Citizen Feedback / Reopening** *(Completed)*
- [x] **Phase 5: Admin & Panchayat Dashboard with Analytics** *(Completed)*
- [x] **Phase 6: Staff & Officer Workflow (Verification, Assignment, Evidence)** *(Completed)*
- [x] **Phase 7: AI Advisory Integration (Human-in-the-Loop)** *(Completed)*
- [x] **Phase 8: n8n Workflow Automation & Notifications** *(Completed)*
- [x] **Phase 9: Security Hardening & End-to-End Testing** *(Completed — 115/115 Automated Tests Passing)*
- [x] **Phase 10: Production Deployment & Documentation** *(Completed — 128/128 Total Tests Passing)*

---

## 📖 Documentation Index
- [Architecture & Design Specifications](file:///c:/Users/poitd/OneDrive/Desktop/Smart%20Gram/docs/ARCHITECTURE.md)
- [Database Schema & Data Dictionary](file:///c:/Users/poitd/OneDrive/Desktop/Smart%20Gram/docs/DATABASE.md)
- [Production Deployment Guide (Apache, Nginx, Docker, SSL, Cron)](file:///c:/Users/poitd/OneDrive/Desktop/Smart%20Gram/docs/DEPLOYMENT.md)
- [Bilingual User Manual (Citizen, Staff, Officer, Admin)](file:///c:/Users/poitd/OneDrive/Desktop/Smart%20Gram/docs/USER_MANUAL.md)
- [API Reference & Webhook Integration](file:///c:/Users/poitd/OneDrive/Desktop/Smart%20Gram/docs/API.md)
- [Local Development Setup Guide](file:///c:/Users/poitd/OneDrive/Desktop/Smart%20Gram/docs/SETUP.md)
