import { Download, FileText } from 'lucide-react'
import { EmptyState, PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useSettings } from '@/context/SettingsContext'

export default function Constitution() {
  const { settings, isLoading } = useSettings()

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'About', to: '/about' }, { label: 'Constitution' }]}
        title="The chapter constitution"
        intro="The governing document behind everything on this page — how the council is elected, how committees are formed, and how the chapter is run."
      />

      <Section>
        <SectionHeading title="Download" className="mb-6" />

        {isLoading ? (
          <Skeleton className="h-32 rounded-xl" />
        ) : settings?.constitutionUrl ? (
          <div className="flex flex-col items-start gap-4 rounded-xl border border-border bg-card p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-center gap-4">
              <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-accent text-accent-foreground">
                <FileText className="h-6 w-6" />
              </span>
              <div>
                <p className="font-medium">Chapter constitution</p>
                <p className="mt-0.5 text-[0.82rem] text-muted-foreground">
                  {settings.constitutionLabel ?? 'PDF document'}
                </p>
              </div>
            </div>
            <Button asChild>
              <a href={settings.constitutionUrl} target="_blank" rel="noreferrer">
                Download the constitution
                <Download aria-hidden="true" className="h-4 w-4" />
              </a>
            </Button>
          </div>
        ) : (
          <EmptyState
            title="Not yet uploaded"
            body="The chapter secretariat hasn't uploaded the constitution document yet. Check back soon."
          />
        )}
      </Section>
    </>
  )
}
