import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { Newspaper } from 'lucide-react'
import { getNews } from '@/api/content'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { NewsPost } from '@/types'

export default function News() {
  const [posts, setPosts] = useState<NewsPost[]>([])

  useEffect(() => {
    getNews().then(setPosts).catch(() => setPosts([]))
  }, [])

  return (
    <section className="mx-auto max-w-6xl px-4 py-20">
      <div className="mx-auto max-w-xl text-center">
        <h1 className="font-heading text-4xl font-bold tracking-tight">News</h1>
        <p className="mt-3 text-muted-foreground">Updates and announcements from the chapter</p>
      </div>

      {posts.length === 0 ? (
        <div className="flex flex-col items-center py-16 text-center text-muted-foreground">
          <Newspaper className="h-10 w-10 text-muted-foreground/50" />
          <p className="mt-3">No news posted yet — check back soon.</p>
        </div>
      ) : (
        <div className="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {posts.map((post, i) => (
            <Link
              key={post.id}
              to={`/news/${post.slug}`}
              className="fade-up"
              style={{ animationDelay: `${i * 80}ms` }}
            >
              <Card className="card-hover h-full overflow-hidden py-0">
                <div className="h-40 w-full">
                  {post.cover_url ? (
                    <img src={post.cover_url} alt={post.title} className="h-full w-full object-cover" />
                  ) : (
                    <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/25 via-accent/40 to-primary/10">
                      <Newspaper className="h-8 w-8 text-primary/60" />
                    </div>
                  )}
                </div>
                <CardHeader>
                  <CardTitle className="font-heading text-lg leading-snug">{post.title}</CardTitle>
                </CardHeader>
                {post.excerpt && (
                  <CardContent className="pb-6 text-sm text-muted-foreground">{post.excerpt}</CardContent>
                )}
              </Card>
            </Link>
          ))}
        </div>
      )}
    </section>
  )
}
