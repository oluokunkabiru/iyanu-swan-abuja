# Connecting a backend

Every page imports its content from `@/data`. Nothing fetches. To go live you replace
the data layer and the auth context — the ~30 page components stay as they are.

## The two seams

**1. Content — `src/data/*.ts`**

Each file exports plain typed arrays. Replace the export with a fetch, or keep the
static file as a fallback and load over it. The types in `src/types/index.ts` are the
contract: if your API returns those shapes, nothing else changes.

**2. Auth — `src/context/AuthContext.tsx`**

Three methods to implement: `signIn`, `signUp`, `signOut`. It currently signs any
credentials into the demo record in `src/data/account.ts` and keeps the session in
`localStorage` under `swan-session`. Swap the bodies for real calls and the rest of the
app follows, including the member-rate logic on event pages, which keys off
`user.membershipStatus === 'active'`.

## Suggested endpoints

| Endpoint | Returns | Used by |
| --- | --- | --- |
| `GET /settings` | `SiteSettings` | Header, footer, membership, home |
| `GET /executives` | `ExecutiveMember[]` | Governance, home |
| `GET /committees` | `Committee[]` | Committees, CPD |
| `GET /committees/:slug` | `Committee` | Committee detail |
| `GET /events?when=upcoming\|past` | `ChapterEvent[]` | Events, home |
| `GET /events/:slug` | `ChapterEvent` | Event detail |
| `POST /events/:slug/register` | ticket | Event detail |
| `GET /news` | `NewsPost[]` | News, home |
| `GET /news/:slug` | `NewsPost` | Article |
| `GET /announcements` | `Announcement[]` | Noticeboard, announcements |
| `GET /programme` | `ProgrammeEntry[]` | Noticeboard, announcements |
| `GET /trainings` | `Training[]` | CPD, training calendar |
| `GET /publications` | `Publication[]` | Publications, home |
| `GET /resources` | `ResourceItem[]` | Forms and downloads, practice |
| `GET /directory/members?q=&sector=` | `DirectoryMember[]` | Members directory |
| `GET /directory/firms?q=` | `Firm[]` | Firms directory, practice |
| `GET /jobs?level=` | `JobListing[]` | Job centre |
| `GET /gallery` | `GalleryImage[]` | Gallery |
| `GET /faqs` | `Faq[]` | FAQs |
| `GET /partners` | `Partner[]` | Home, about |
| `POST /contact` | — | Contact form |

Member-only, behind the session:

| Endpoint | Returns | Used by |
| --- | --- | --- |
| `GET /me` | `AuthUser` | Members area |
| `GET /me/cpd` | `CpdRecord[]` | CPD record |
| `POST /me/cpd` | `CpdRecord` | "Log an activity" |
| `GET /me/subscriptions` | `SubscriptionRecord[]` | Subscription and welfare |
| `POST /me/subscriptions/:year/pay` | payment intent | Pay dues |
| `GET /me/tickets` | `TicketRecord[]` | Event tickets |
| `PATCH /me` | `AuthUser` | Profile |

## Search and filtering

Directory, firms, jobs, trainings, publications, resources, FAQs, news and gallery all
filter client-side over the full array. That is fine at chapter scale. If a list grows
past a few hundred rows, move the filter into the query string — the state is already
held in the page component, so it is a local change.

## Notes

- Prices are stored as whole naira integers and rendered with `formatNaira` in
  `src/lib/format.ts`.
- Dates are ISO strings. Events carry a timezone offset (`+01:00`); plain dates do not.
- Slugs are the route keys for events, news and committees. Keep them stable.
- The four event ticket tiers live in `standardTiers` in `src/data/events.ts` and are
  shared across events. If tiers become per-event, populate `ChapterEvent.ticketTiers`
  from the API and delete the shared constant.
