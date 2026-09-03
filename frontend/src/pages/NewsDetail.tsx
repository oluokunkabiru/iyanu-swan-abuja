import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { ArrowLeft } from 'lucide-react'
import { getNewsPost } from '@/api/content'
import type { NewsPost } from '@/types'

function NewsDetailSkeleton() {
  return (
    <article className="mx-auto max-w-3xl animate-pulse px-4 py-16">
      <div className="mb-8 h-72 w-full rounded-2xl bg-muted" />
      <div className="h-8 w-3/4 rounded bg-muted" />
      <div className="mt-3 h-4 w-32 rounded bg-muted" />
      <div className="mt-8 space-y-3">
        <div className="h-4 w-full rounded bg-muted" />
        <div className="h-4 w-full rounded bg-muted" />
        <div className="h-4 w-2/3 rounded bg-muted" />
      </div>
    </article>
  )
}

export default function NewsDetail() {
  const { slug } = useParams<{ slug: string }>()
  const [post, setPost] = useState<NewsPost | null>(null)

  useEffect(() => {
    if (!slug) return
    getNewsPost(slug).then(setPost)
  }, [slug])

  if (!post) return <NewsDetailSkeleton />

  return (
    <article className="mx-auto max-w-3xl px-4 py-16">
      <Link
        to="/news"
        className="fade-up inline-flex items-center gap-1.5 text-sm font-medium text-muted-foreground transition-colors hover:text-primary"
      >
        <ArrowLeft className="h-4 w-4" /> Back to news
      </Link>

      {post.cover_url && (
        <img
          src={post.cover_url}
          alt={post.title}
          className="fade-up mt-6 mb-8 h-72 w-full rounded-2xl border object-cover shadow-lg shadow-primary/5"
        />
      )}
      <h1 className="fade-up font-heading text-3xl font-bold tracking-tight text-balance sm:text-4xl">
        {post.title}
      </h1>
      {post.published_at && (
        <p className="fade-up mt-3 text-sm text-muted-foreground">
          {new Date(post.published_at).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
          })}
        </p>
      )}
      {post.body && (
        <div
          className="fade-up prose prose-neutral dark:prose-invert prose-headings:font-heading prose-a:text-primary mt-8 max-w-none"
          dangerouslySetInnerHTML={{ __html: post.body }}
        />
      )}
    </article>
  )
}
