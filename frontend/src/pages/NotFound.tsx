import { Link } from 'react-router-dom'
import { Compass } from 'lucide-react'
import { Button } from '@/components/ui/button'

export default function NotFound() {
  return (
    <section className="mx-auto flex min-h-[60vh] max-w-md flex-col items-center justify-center px-4 text-center">
      <span className="fade-up flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
        <Compass className="h-7 w-7" />
      </span>
      <h1 className="fade-up mt-6 font-heading text-5xl font-bold [animation-delay:80ms]">404</h1>
      <p className="fade-up mt-2 text-muted-foreground [animation-delay:120ms]">This page could not be found.</p>
      <Button className="fade-up mt-6 shadow-lg shadow-primary/25 [animation-delay:160ms]" asChild>
        <Link to="/">Back home</Link>
      </Button>
    </section>
  )
}
