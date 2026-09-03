import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { ArrowRight, Newspaper } from 'lucide-react'
import { getNews } from '@/api/content'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { NewsPost } from '@/types'

export function NewsPreview() {
  const [posts, setPosts] = useState<NewsPost[]>([])

  useEffect(() => {
    getNews().then((data) => setPosts(data.slice(0, 3))).catch(() => setPosts([]))
  }, [])

  if (posts.length === 0) return null

  return (
    <section className="mx-auto max-w-6xl px-4 py-20">
      <div className="flex items-end justify-between">
        <div>
          <h2 className="font-heading text-3xl font-bold tracking-tight">Latest News</h2>
          <p className="mt-2 text-muted-foreground">Updates and announcements from the chapter</p>
        </div>
        <Button variant="ghost" className="hidden sm:inline-flex" asChild>
          <Link to="/news">
            View all <ArrowRight className="h-4 w-4" />
          </Link>
        </Button>
      </div>

      <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {posts.map((post, i) => (
          <Link key={post.id} to={`/news/${post.slug}`} className="fade-up" style={{ animationDelay: `${i * 100}ms` }}>
            <Card className="card-hover h-full overflow-hidden py-0">
              <div className="h-40 w-full">
                {post.cover_url ? (
                  <img src={post.cover_url} alt={post.title} className="h-full w-full object-cover" />
                ) : (
                  <div className="flex h-full w-full items-center justify-center bg-secondary">
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
    </section>
  )
}
