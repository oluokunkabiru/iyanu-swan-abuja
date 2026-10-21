import { Download, FileText } from 'lucide-react'
import { EmptyState, PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { useSettings } from '@/context/SettingsContext'

export default function Constitution() {
  const { settings, isLoading } = useSettings()
  const documents = [
    { id: 'constitution', label: 'Constitution', url: settings?.constitutionUrl, version: settings?.constitutionLabel },
    { id: 'brochure', label: 'Brochure', url: settings?.brochureUrl, version: null },
  ]

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'About', to: '/about' }, { label: 'Constitution' }]}
        title="Constitution and brochure"
        intro="Read the chapter's governing document and learn more about the Society through our brochure."
      />

      <Section>
        <Tabs defaultValue="constitution" className="gap-6">
          <TabsList aria-label="Chapter documents" className="w-full sm:w-fit">
            {documents.map((document) => (
              <TabsTrigger key={document.id} value={document.id}>
                {document.label}
              </TabsTrigger>
            ))}
          </TabsList>

          {documents.map((document) => (
            <TabsContent key={document.id} value={document.id}>
              <SectionHeading title={`Read the ${document.id}`} className="mb-6" />

              {isLoading ? (
                <div className="space-y-6">
                  <Skeleton className="h-32 rounded-xl" />
                  <Skeleton className="h-[75vh] rounded-xl" />
                </div>
              ) : document.url ? (
                <div className="space-y-6">
                  <div className="flex flex-col items-start gap-4 rounded-xl border border-border bg-card p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-4">
                      <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-accent text-accent-foreground">
                        <FileText aria-hidden="true" className="h-6 w-6" />
                      </span>
                      <div>
                        <p className="font-medium">Chapter {document.id}</p>
                        <p className="mt-0.5 text-[0.82rem] text-muted-foreground">
                          {document.version ?? 'PDF document'}
                        </p>
                      </div>
                    </div>
                    <Button asChild>
                      <a href={document.url} target="_blank" rel="noreferrer">
                        View/Download the {document.label}
                        <Download aria-hidden="true" className="h-4 w-4" />
                      </a>
                    </Button>
                  </div>

                  <iframe
                    title={`Chapter ${document.id} PDF preview`}
                    src={document.url}
                    className="h-[75vh] w-full rounded-xl border border-border bg-card"
                  >
                    <a href={document.url} target="_blank" rel="noreferrer">
                      View/Download the {document.label}
                    </a>
                  </iframe>
                </div>
              ) : (
                <EmptyState
                  title="Not yet uploaded"
                  body={`The chapter secretariat hasn't uploaded the ${document.id} document yet. Check back soon.`}
                />
              )}
            </TabsContent>
          ))}
        </Tabs>
      </Section>
    </>
  )
}
