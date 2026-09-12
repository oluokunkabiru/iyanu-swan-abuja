# Site navigation map

Every URL a human can visit in this app, grouped by who can see it. Frontend
paths are relative to `VITE_API_URL`'s sibling frontend origin (e.g.
`http://localhost:5179`); admin paths are relative to the backend's
`APP_URL` (e.g. `http://localhost:8000`).

## Public site (no login required)

| Path | Page | Notes |
|---|---|---|
| `/` | Home | |
| `/about` | About | |
| `/governance` | Governance | |
| `/constitution` | Constitution | |
| `/committees` | Committees | |
| `/committees/:slug` | Committee detail | |
| `/faqs` | FAQs | |
| `/contact` | Contact | |
| `/membership` | Membership overview | Shows dues "from" the cheapest active membership level |
| `/membership/register` | Membership registration form | Posts to `/api/register`; free — no payment happens here. Collects name, email, password, ICAN membership number, ICAN status (ACA/FCA), WhatsApp telephone, residential address, place of work, and optional date of birth |
| `/mentorship` | Mentorship | |
| `/students` | Students | |
| `/cpd` | CPD overview | |
| `/cpd/trainings` | Trainings | |
| `/practice` | Practice | |
| `/jobs` | Job listings | |
| `/events` | Events | |
| `/events/:slug` | Event detail | |
| `/news` | News | |
| `/news/:slug` | News detail | |
| `/gallery` | Gallery | |
| `/announcements` | Announcements | |
| `/directory` | Member directory | |
| `/directory/firms` | Firm directory | |
| `/publications` | Publications | |
| `/resources` | Resources | |
| `/login` | Sign in | |
| `/register` | — | Redirects to `/membership/register` |
| `/dashboard` | — | Redirects to `/members` |
| `/payments/callback` | Payment result | Gateway (Paystack/Flutterwave) redirects here after checkout; reads `?reference=`/`?tx_ref=`/`?trxref=` |
| `/email/verify/:id/:hash` | Email verification | The link in the verification email itself — points here (frontend), not the backend. On load, this page calls the backend's signed `verification.verify` API endpoint (passing the `expires`/`signature` query params through) to do the actual verifying, then shows the result |
| `/tickets/verify/:reference` | Event ticket verification | The link/QR code in an event ticket confirmation email. No login required — calls `GET /api/tickets/{reference}/verify`, which marks the ticket checked in on its first successful scan. Meant for door staff scanning on a phone |
| `*` (anything else) | 404 | |

## Member area (`/members/*`, requires sign-in)

Wrapped in `ProtectedRoute`, which bounces signed-out visitors to `/login`.

| Path | Page | Notes |
|---|---|---|
| `/members` | Dashboard overview | Shows the "confirm your email" banner until the registered email is verified, and the "dues are open" banner until paid |
| `/members/cpd` | CPD record | |
| `/members/subscription` | Dues / subscription | Choose a membership level, then pay online (Paystack/Flutterwave) or submit a bank transfer with evidence for admin review |
| `/members/tickets` | Event tickets | |
| `/members/profile` | Profile | Contact details, plus a "Notification emails" section (locked until the registered email is verified) for adding a personal/official email and choosing where notices go |

## Admin panel (Filament, `/admin/*`, requires an admin-role account)

| Path | Purpose |
|---|---|
| `/admin/login` | Admin sign-in |
| `/admin/logout` | Admin sign-out |
| `/admin` | Dashboard |
| `/admin/users` | Members & admins — list, view (verification badge, personal/official email, notification preference, CPD/subscriptions/tickets tabs), create, edit |
| `/admin/subscriptions` | Membership dues records — level, method, evidence link, and Approve/Reject actions for bank transfers pending review |
| `/admin/membership-levels` | Membership levels — the priced tiers (subscription + welfare amount) members choose when paying dues |
| `/admin/events` | Events |
| `/admin/news-posts` | News posts |
| `/admin/announcements` | Announcements |
| `/admin/sliders` | Homepage sliders |
| `/admin/gallery-images` | Gallery |
| `/admin/committees` | Committees |
| `/admin/executive-members` | Executive members / past chairpersons |
| `/admin/member-spotlights` | Member spotlights |
| `/admin/cpd-records` | CPD records |
| `/admin/faqs` | FAQs |
| `/admin/firms` | Firm directory entries |
| `/admin/job-listings` | Job board |
| `/admin/trainings` | Trainings |
| `/admin/programme-entries` | Programme entries |
| `/admin/publications` | Publications |
| `/admin/resource-items` | Resources library |
| `/admin/partners` | Partners |
| `/admin/core-values` | Core values |
| `/admin/contact-messages` | Contact form submissions |
| `/admin/manage-site-settings` | Site settings (active payment gateway, copy, etc. — membership fees live under Membership Levels, not here) |
| `/admin/manage-notification-settings` | Notification settings — channel toggles, per-type routing, and the site-wide default for which member email (registered/personal/official/all) notices go to |
| `/admin/send-broadcast` | Compose and send an ad-hoc message to all members, active members only, or pending/expired members only (see `NOTIFICATIONS.md`) |
| `/admin/shield/roles` | Build admin panel roles — a role is a checked set of permissions (view/create/update/delete/...) per resource, page, and widget. Assign a role to an admin from their edit page under "Admin panel roles". The `role` field elsewhere (Admin/Member) only gates entry to the panel; a role built here decides what an admin who's in can actually do — a new admin can sign in but do nothing until a `super_admin` assigns one |

Resource paths above are the index route; each also has `/create` and
`/{record}/edit` (and `/{record}` for the Users resource's view page).

## Backend API

All member/public-facing frontend and admin actions are backed by
`POST`/`GET` routes under `/api/*` (session-authenticated via Sanctum for
member endpoints) — run `php artisan route:list --path=api` from `backend/`
for the full, current list. Two routes worth knowing by name since they're
reached from outside the SPA (email links, payment gateways) rather than by
clicking through the UI:

- `GET /api/email/verify/{id}/{hash}` — the actual signed verification
  check. The email link itself points at the frontend's
  `/email/verify/:id/:hash` page (not this URL directly); that page calls
  this endpoint with the same `expires`/`signature` query params to verify,
  then shows the result. Returns JSON, not a redirect.
- `GET /api/payments/verify/{reference}` and the `/api/payments/webhooks/*`
  routes — payment gateway callbacks.
- `GET /api/membership-levels` — public list of active membership levels and
  their prices, used by both the registration page and the member dues flow.
- `POST /api/me/subscriptions/{year}/bank-transfer` — a member's manual
  payment evidence submission; puts that year's subscription into
  `pending_review` until an admin approves or rejects it from
  `/admin/subscriptions`.
