# Master Technical Reference: Migration Execution Roadmap

This document serves as the **Single Source of Truth (SSOT)** for migrating the Alidebo v2 project to a highly scalable Laravel 12 (API) and Next.js 15 (App Router) architecture. It is designed to handle **10,000+ concurrent users**, programmatic SEO, and enterprise-grade security.

---

## Phase 1: Infrastructure & Core Security (Laravel API)
**Goal:** Establish the backend environment with Read/Write splitting, Redis caching, and strict SPA security.

| Micro-Step | Target Files / Dirs | Architectural Pattern | Verification / Success Criteria |
| :--- | :--- | :--- | :--- |
| **1.1 Database R/W Split** | `config/database.php` | Configure `read` and `write` array connections pointing to primary/replica hosts. | Artisan Tinker `DB::connection()->select()` hits replica; `insert()` hits primary. |
| **1.2 Redis Configuration** | `config/cache.php`, `.env` | Set `CACHE_DRIVER=redis`, `SESSION_DRIVER=redis`. Configure Predis/PhpRedis for cluster support. | Running `Cache::put()` successfully stores and retrieves from the Redis instance. |
| **1.3 Sanctum SPA Auth** | `config/sanctum.php`, `config/cors.php` | Configure `stateful` domains (Next.js production/local). Set `supports_credentials` to true in CORS. | Next.js local server can hit `/sanctum/csrf-cookie` and receive `XSRF-TOKEN` header. |
| **1.4 Global Throttling** | `app/Providers/AppServiceProvider.php` (or `bootstrap/app.php`) | Implement `RateLimiter::for('api')` using Redis backend. 60 req/min for API, 5 req/min for Auth. | Postman load test returns HTTP 429 Too Many Requests after 60 rapid hits. |
| **1.5 Security Headers** | `app/Http/Middleware/SecurityHeaders.php` | Add `X-Frame-Options`, `X-Content-Type-Options`, `Strict-Transport-Security` to API responses. | Browsers explicitly block iframe embedding; curl shows headers. |

---

## Phase 2: Domain-Driven Data Layer & High-Concurrency Read (Laravel API)
**Goal:** Build the business logic using strict DDD patterns, avoiding Eloquent N+1 issues and heavily caching reads.

| Micro-Step | Target Files / Dirs | Architectural Pattern | Verification / Success Criteria |
| :--- | :--- | :--- | :--- |
| **2.1 DTO & FormRequests** | `app/Domains/Business/DTOs/`, `app/Http/Requests/Business/` | Create strictly typed DTOs (Data Transfer Objects). FormRequests validate and instantiate DTOs. | Sending invalid payload returns 422 Unprocessable Entity. Controller receives exact typed DTO. |
| **2.2 Tagged Redis Caching** | `app/Domains/Business/Services/BusinessReadService.php` | Wrap Eloquent `get()` queries in `Cache::tags(['businesses'])->remember(...)` for directory listings. | Query logs show 0 DB hits on subsequent requests. Redis memory increases. |
| **2.3 API JsonResources** | `app/Http/Resources/BusinessProfileResource.php` | Map Eloquent models to minimal JSON arrays. Never use `->makeHidden()`. | Payload size is strictly < 5KB per profile. No hidden password or pivot data leaks. |
| **2.4 Cache Invalidation Events** | `app/Domains/Business/Events/`, `app/Domains/Business/Listeners/` | When Business profile updates (Write DB), fire Event to `Cache::tags(['businesses'])->flush()`. | Modifying a business profile instantly reflects on the public API endpoint via cache purge. |
| **2.5 DB Indexing** | `database/migrations/` | Ensure `slug`, `category_id`, `city_id` have B-Tree indexes for fast `WHERE` clauses. | `EXPLAIN` SQL query confirms usage of indexes instead of full table scans. |

---

## Phase 3: High-Performance Frontend & Programmatic SEO (Next.js 15)
**Goal:** Leverage React Server Components (RSC) and Incremental Static Regeneration (ISR) to bypass Laravel completely for public traffic.

| Micro-Step | Target Files / Dirs | Architectural Pattern | Verification / Success Criteria |
| :--- | :--- | :--- | :--- |
| **3.1 ISR Business Profiles** | `src/app/(public)/companies/[slug]/page.tsx` | Fetch from Laravel API using `fetch(url, { next: { revalidate: 3600 } })`. Output RSC. | Vercel deployment logs show `HIT` on edge cache. Laravel receives 0 requests from 10k users. |
| **3.2 Dynamic Metadata** | `src/app/(public)/companies/[slug]/page.tsx` | Implement `export async function generateMetadata({ params })` fetching basic profile data. | View Source on page shows correct `<title>`, `<meta name="description">`, and OG tags. |
| **3.3 JSON-LD Structured Data** | `src/components/seo/LocalBusinessJsonLd.tsx` | Render `<script type="application/ld+json">` conforming to schema.org/LocalBusiness. | Google Rich Results Test parses the business address, name, and rating successfully. |
| **3.4 Sitemap Chunking** | `src/app/sitemap.ts`, `src/app/sitemap/[id]/route.ts` | Next.js native sitemap generation with pagination (fetching 50k slugs at a time from Laravel). | Visiting `/sitemap.xml` generates standard sitemap index pointing to sub-sitemaps. |

---

## Phase 4: Secure User Dashboard (Next.js & Laravel)
**Goal:** Build the interactive, highly secure client-side authenticated dashboard.

| Micro-Step | Target Files / Dirs | Architectural Pattern | Verification / Success Criteria |
| :--- | :--- | :--- | :--- |
| **4.1 Auth Context / Axios** | `src/lib/axios.ts`, `src/providers/AuthProvider.tsx` | Pre-configure Axios with `withCredentials: true`. Handle 401 intercepts for auto-logout. | Successful login stores HttpOnly cookie; browser Developer Tools show cookie attached to API hits. |
| **4.2 Server Actions Mutations** | `src/features/business/actions/update-profile.ts` | Use Next.js Server Actions to safely relay mutation requests to the Laravel API from the client UI. | Form submission updates data; Network tab shows no direct API calls from the browser to Laravel. |
| **4.3 Optimistic UI Updates** | `src/features/leads/components/LeadList.tsx` | Use React `useOptimistic` hook to update the UI instantly when marking a lead as 'read' or 'deleted'. | UI instantly reacts to clicks; reverts automatically if the underlying API call fails. |

---

## Phase 5: Admin Panel & Queue Workers (High Concurrency Backend)
**Goal:** Handle massive administrative tasks (like Excel imports) asynchronously without crashing the web servers.

| Micro-Step | Target Files / Dirs | Architectural Pattern | Verification / Success Criteria |
| :--- | :--- | :--- | :--- |
| **5.1 Job Batching** | `app/Jobs/ImportBusinessRow.php` | Laravel Job Batching. Instead of 1 massive import, dispatch 10,000 individual Jobs to Redis. | Laravel Horizon shows batch progressing. Web UI returns "Import started" instantly. |
| **5.2 Queue Worker Isolation** | `config/horizon.php` | Isolate worker processes. e.g., `emails-queue`, `imports-queue`, `seo-queue`. | Long-running imports do not block critical password reset emails from sending. |
| **5.3 Admin API Endpoints** | `app/Http/Controllers/Api/Admin/` | Protect with `middleware(['auth:sanctum', 'role:super-admin'])`. | Standard user attempting to fetch admin stats receives HTTP 403 Forbidden. |

---

## Phase 6: Edge Optimization & Load Testing
**Goal:** Prove the system can handle 10,000+ Concurrent Users (CCU) before production launch.

| Micro-Step | Target Files / Dirs | Architectural Pattern | Verification / Success Criteria |
| :--- | :--- | :--- | :--- |
| **6.1 Edge Network Caching** | Next.js Config (Vercel/Cloudflare) | Ensure all public routes are heavily Edge cached. Monitor cache hit ratios. | Cache hit ratio on `/companies/*` routes is > 95%. |
| **6.2 Database Stress Test** | N/A (Load Testing Environment) | Use Artillery or JMeter to simulate 1,000 requests/sec searching for businesses on the API. | API latency remains < 200ms. DB Read Replica CPU usage stays below 80%. |
| **6.3 Next.js Load Test** | N/A (Load Testing Environment) | Use Artillery to hit Next.js public routes with 10k CCU. | Next.js returns HTML in < 50ms with 0 errors, completely absorbing the load via Edge CDN. |
