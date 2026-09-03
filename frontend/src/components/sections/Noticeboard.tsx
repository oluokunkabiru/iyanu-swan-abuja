import { Link } from 'react-router-dom'
import { AnnouncementRow } from '@/components/common/Cards'
import { announcements, news, programme } from '@/data'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { formatShortDate } from '@/lib/format'

/**
 * The chapter noticeboard. ICAN's most useful home module is its tabbed panel
 * of notices, news and forthcoming programme, so the same idea is kept here
 * with the chapter's own content.
 */
export function Noticeboard() {
  return (
    <Tabs defaultValue="notices" className="gap-0">
      <TabsList className="h-auto w-full justify-start rounded-none border-b border-rule bg-transparent p-0">
        <TabsTrigger
          value="notices"
          className="rounded-none border-b-2 border-transparent px-4 py-2.5 text-[0.9rem] data-[state=active]:border-gold-500 data-[state=active]:bg-transparent data-[state=active]:shadow-none"
        >
          Notices
        </TabsTrigger>
        <TabsTrigger
          value="news"
          className="rounded-none border-b-2 border-transparent px-4 py-2.5 text-[0.9rem] data-[state=active]:border-gold-500 data-[state=active]:bg-transparent data-[state=active]:shadow-none"
        >
          Latest news
        </TabsTrigger>
        <TabsTrigger
          value="programme"
          className="rounded-none border-b-2 border-transparent px-4 py-2.5 text-[0.9rem] data-[state=active]:border-gold-500 data-[state=active]:bg-transparent data-[state=active]:shadow-none"
        >
          Programme
        </TabsTrigger>
      </TabsList>

      <TabsContent value="notices" className="mt-0">
        <ul>
          {announcements.slice(0, 6).map((a) => (
            <AnnouncementRow key={a.id} {...a} />
          ))}
        </ul>
        <Link
          to="/announcements"
          className="mt-4 inline-block border-b border-plum-700 pb-0.5 text-[0.85rem] font-semibold text-plum-700 dark:border-primary dark:text-primary"
        >
          All notices
        </Link>
      </TabsContent>

      <TabsContent value="news" className="mt-0">
        <ul>
          {news.slice(0, 5).map((post) => (
            <li key={post.id} className="border-b border-border last:border-0">
              <Link to={`/news/${post.slug}`} className="group block py-3.5">
                <span className="block text-[0.92rem] leading-snug group-hover:text-plum-700 dark:group-hover:text-primary">
                  {post.title}
                </span>
                <span className="mt-1 block text-[0.78rem] text-muted-foreground">
                  {formatShortDate(post.publishedAt)} · {post.author}
                </span>
              </Link>
            </li>
          ))}
        </ul>
        <Link
          to="/news"
          className="mt-4 inline-block border-b border-plum-700 pb-0.5 text-[0.85rem] font-semibold text-plum-700 dark:border-primary dark:text-primary"
        >
          All chapter news
        </Link>
      </TabsContent>

      <TabsContent value="programme" className="mt-0">
        <table className="w-full text-left">
          <caption className="sr-only">Forthcoming chapter programme</caption>
          <thead>
            <tr className="border-b border-border text-[0.75rem] font-semibold text-muted-foreground">
              <th scope="col" className="py-2.5 pr-4">
                Activity
              </th>
              <th scope="col" className="py-2.5 pr-4">
                Date
              </th>
              <th scope="col" className="py-2.5">
                Venue
              </th>
            </tr>
          </thead>
          <tbody>
            {programme.map((p) => (
              <tr key={p.id} className="border-b border-border last:border-0">
                <td className="py-3.5 pr-4 align-top">
                  <Link
                    to={p.href}
                    className="font-medium leading-snug hover:text-plum-700 dark:hover:text-primary"
                  >
                    {p.name}
                  </Link>
                </td>
                <td className="tnum py-3.5 pr-4 align-top text-[0.86rem] text-muted-foreground">
                  {p.date}
                </td>
                <td className="py-3.5 align-top text-[0.86rem] text-muted-foreground">{p.venue}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </TabsContent>
    </Tabs>
  )
}
