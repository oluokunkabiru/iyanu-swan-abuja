import { Link, useParams } from 'react-router-dom'
import { EmptyState, PageHeader, Section, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { findNews, news } from '@/data'
import { formatDate } from '@/lib/format'

export default function NewsDetail() {
  const { slug } = useParams<{ slug: string }>()
  const post = slug ? findNews(slug) : undefined

  if (!post) {
    return (
      <Section>
        <EmptyState
          title="Article not found"
          body="That article is not in the archive. It may have been moved."
          action={
            <Button variant="outline" asChild>
              <Link to="/news">Back to news</Link>
            </Button>
          }
        />
      </Section>
    )
  }

  const others = news.filter((n) => n.id !== post.id).slice(0, 3)

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'News', to: '/news' }, { label: post.category }]}
        title={post.title}
        intro={post.excerpt}
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
          <article>
            <div className="flex flex-wrap items-center gap-3 text-[0.85rem] text-muted-foreground">
              <StatusTag tone="gold">{post.category}</StatusTag>
              <time dateTime={post.publishedAt}>{formatDate(post.publishedAt)}</time>
              <span>{post.author}</span>
            </div>

            {post.coverUrl && (
              <img
                src={post.coverUrl}
                alt=""
                className="mt-6 aspect-[16/9] w-full border border-border object-cover"
              />
            )}

            <div className="mt-8 max-w-[70ch] space-y-4 text-[1.02rem] leading-relaxed">
              {post.body.map((para) => (
                <p key={para.slice(0, 40)}>{para}</p>
              ))}
            </div>
          </article>

          <aside className="space-y-6">
            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">More from the chapter</h2>
              <ul className="mt-4 divide-y divide-border border-y border-border">
                {others.map((n) => (
                  <li key={n.id}>
                    <Link to={`/news/${n.slug}`} className="group block py-3.5">
                      <span className="block text-[0.9rem] leading-snug group-hover:text-plum-700 dark:group-hover:text-primary">
                        {n.title}
                      </span>
                      <span className="mt-1 block text-[0.78rem] text-muted-foreground">
                        {formatDate(n.publishedAt)}
                      </span>
                    </Link>
                  </li>
                ))}
              </ul>
            </div>

            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">Publications library</h2>
              <p className="mt-2 text-[0.88rem] leading-relaxed text-muted-foreground">
                Communiqués, technical bulletins and the annual report are filed in full.
              </p>
              <Button variant="outline" asChild className="mt-4 w-full">
                <Link to="/publications">Open the library</Link>
              </Button>
            </div>
          </aside>
        </div>
      </Section>

      <Section tone="tinted">
        <SectionHeading title="Keep reading" className="mb-8" />
        <ul className="grid gap-px bg-border md:grid-cols-3">
          {others.map((n) => (
            <li key={n.id} className="bg-card p-5">
              <p className="text-[0.8rem] text-muted-foreground">{formatDate(n.publishedAt)}</p>
              <h3 className="mt-2 text-[1rem] leading-snug">
                <Link to={`/news/${n.slug}`} className="hover:text-plum-700 dark:hover:text-primary">
                  {n.title}
                </Link>
              </h3>
            </li>
          ))}
        </ul>
      </Section>
    </>
  )
}
