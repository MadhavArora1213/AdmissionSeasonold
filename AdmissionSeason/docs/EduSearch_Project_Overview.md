# EduSearch Platform - Project Overview & Blueprint

## Executive Summary
**EduSearch** is a next-generation education discovery and admissions platform designed to challenge incumbents like Collegedunia and Shiksha. It uniquely combines a trust-first approach with deep AI personalization (self-hosted Llama 3.1) and a complete end-to-end admissions workflow. 

**Key Differentiators:**
- **Zero Ongoing Cloud/API Costs:** Built entirely on open-source, self-hosted tools (Ollama, PostgreSQL, MeiliSearch, Redis) running on a single Hostinger VPS (₹648/mo).
- **Self-Hosted AI Counselor:** Replaces expensive generic chat interfaces with a highly contextualized AI counselor that guides students without relying on paid OpenAI/Anthropic APIs.
- **Direct Application:** Students complete their entire admission journey natively on the platform.
- **Verified Reviews & Spam-Free Intent:** Requires authentication and verification, generating high-trust reviews and highly qualified B2B leads.

## Target Audience
1. **Students (B2C):** Looking for UG/PG programs, study abroad options, and personalized college recommendations.
2. **Parents:** Tracking fees, scholarships, and ROI.
3. **Colleges & Institutions (B2B - Paying Clients):** Purchasing high-quality leads, verified applicant submissions, and featured listings.

---

## Technical Architecture & Infrastructure
The entire platform is self-hosted on a **Hostinger VPS** (2 vCPU, 8 GB RAM, 100 GB NVMe - Ubuntu 22.04) using **Docker & Docker Compose**.

### Network Flow:
`INTERNET → Cloudflare CDN/WAF → Nginx (Reverse Proxy + SSL)`
- `/` → Next.js (SSR/SSG Frontend)
- `/api` → Fastify (Backend APIs)
- `/admin` → NocoDB (Admin CMS)
- `/analytics` → Umami (Privacy-first Analytics)

### Backend Services (Internal Network):
- **Ollama (Llama 3.1 8B Q4):** AI Inference engine for counseling (Cached to save RAM/Compute).
- **PostgreSQL 16:** Core relational database.
- **MeiliSearch:** Typo-tolerant, instant full-text search engine for colleges.
- **Redis (Valkey):** Caching, sessions, rate-limiting, and LLM response caching.

### Full Tech Stack
* **Frontend:** Next.js 14 (App Router), shadcn/ui, Tailwind CSS, Zustand, React Query.
* **Backend:** Node.js, Fastify, Prisma ORM.
* **Auth:** Better Auth (Self-hosted OAuth/Magic links).
* **Storage:** Cloudflare R2 (10GB Free S3-compatible for images/PDFs).
* **Communication:** Brevo (Email/SMS Free Tier), WhatsApp Baileys Bot.
* **Payments:** Razorpay (Zero fixed fees, 2% transaction fee).
* **CI/CD & Monitoring:** GitHub Actions, Sentry (Free), UptimeRobot, Grafana + Prometheus.

---

## Core Product Modules
### 1. Student-Facing Output (B2C)
- **Smart Search & Discovery:** Powered by MeiliSearch with extensive filters (fees, NAAC, ranking, exams).
- **College Profiles:** Detailed tabs (Overview, Fees, Admission, Placements, Gallery, Reviews).
- **Exam Hub:** 350+ exam profiles, exam calendar, cutoffs, and mock tests.
- **AI Counseling Assistant:** Chat UI powered by Ollama. Connects student profiles (marks, budget, location) with database queries injected into the Llama prompt to fetch the top 3-5 hyper-personalized recommendations.
- **Unified Admissions Tracker:** Document vault, application workflow, Razorpay fee payments.
- **Value-Added Tools:** Rank predictor, ROI calculator, Scholarship finder, Study abroad tracker.

### 2. College Portal (B2B)
- **Dashboard:** Self-serve onboarding, profile management, and gallery uploads.
- **Lead Inbox:** Filterable, score-based lead tracking with real-time Brevo SMS alerts.
- **Application Management:** Review direct applications, schedule interviews, and update statuses.
- **Analytics:** Traffic impressions, clicks, lead conversion funnels.

### 3. Admin Panel (Operations)
- **CMS:** NocoDB-backed PostgreSQL management for the 30,000+ college records.
- **Moderation:** Review and approve student reviews/documents.
- **Billing/Invoicing:** Track CPL (Cost Per Lead) revenue and active subscriptions.

---

## Revenue Model
EduSearch embraces a multi-tiered monetization strategy that starts on Day 1:
1. **Cost Per Lead (CPL):** Primary revenue. Colleges pay ₹300-₹1500 per verified student lead.
2. **Sponsored Listings:** Colleges pay ₹15K-₹1.5L/month to rank higher in specific searches.
3. **Application Commissions:** 15% platform cut on application fees processed via Razorpay.
4. **B2B Analytics Subscription:** Premium dashboards showing market/competitor insights.
5. **Loan & Study Abroad Referrals:** Affiliate commissions for successful placements/disbursements.

---

## 30-Day Launch Timeline
The roadmap is aggressive but feasible with a small focused team.
* **Week 1 (Days 1-7) - Foundation:** VPS, Docker, DB Schema, Auth, Search Engine, Next.js/Fastify baseline, Ollama inference testing. Seed first 2,000 colleges.
* **Week 2 (Days 8-14) - Core B2C Features:** College profiles, search UI, AI Counselor chat UI, exam pages, student dashboard, lead capture flows.
* **Week 3 (Days 15-21) - B2B & Revenue Tools:** College registration portal, lead inbox, Razorpay integration, rank predictor, blog/CMS, study abroad/career modules.
* **Week 4 (Days 22-30) - Scale & Polish:** Scrape to 8,000+ colleges, launch 95,000+ programmatic SEO pages, load testing, caching implementation (Redis), pre-launch B2B sales, public beta.

---

## SEO & Go-To-Market Strategy
- **Programmatic SEO (The Moat):** Auto-generate over 95,000 pages for long-tail keywords (e.g., `Best Engineering Colleges in Pune under 2 Lakhs`, `IIT Bombay Placements 2026`).
- **Community First:** Build WhatsApp communities, leverage Reddit (r/Indian_Academia), and work with YouTube influencers.
- **Pre-Launch B2B:** Close 5-10 paying college clients before Day 30 offering "guaranteed leads or don't pay" to prime the revenue engine.

---
*Generated by AI based on the EduSearch Bootstrap Blueprint (Feb 2026).*
