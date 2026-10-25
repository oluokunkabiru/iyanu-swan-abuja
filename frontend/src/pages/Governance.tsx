import { motion } from 'framer-motion'
import { UserRound } from 'lucide-react'
import { getExecutives, getPastChairpersons } from '@/api/content'
import { LazyImage } from '@/components/common/LazyImage'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { TiltCard } from '@/components/common/TiltCard'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { containerVariants, itemVariants } from '@/lib/animations'
import type { ExecutiveMember } from '@/types'

function tenureYears(member: ExecutiveMember): string | null {
  const { termStartYear: start, termEndYear: end } = member
  if (start && end) return start === end ? String(start) : `${start}–${end}`
  if (start) return `From ${start}`
  if (end) return `Until ${end}`
  return null
}

export default function Governance() {
  const { data: executives, isLoading } = useApiData(getExecutives, [] as ExecutiveMember[])
  const { data: pastChairpersons, isLoading: loadingPastChairpersons } = useApiData(
    getPastChairpersons,
    [] as ExecutiveMember[],
  )
  const principals = executives.filter((e) => e.isPrincipal)
  const others = executives.filter((e) => !e.isPrincipal)

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'About', to: '/about' }, { label: 'Governance' }]}
        title="Governance and the chapter council"
        intro="The council is elected by financial members at the annual general meeting and serves a one-year session. Its duties, the election procedure and the standing rules are set out in the chapter constitution."
      />

      <Section>
        <SectionHeading
          title="How the chapter is governed"
          className="mb-8"
        />
        <div className="grid gap-8 md:grid-cols-3">
          <div className="border-l-2 border-gold-500 pl-5">
            <h3 className="text-[1.05rem]">The council</h3>
            <p className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
              Twelve elected officers, including five principal officers. The council sets the
              annual programme, approves the budget, and answers to members at the general meeting.
            </p>
          </div>
          <div className="border-l-2 border-gold-500 pl-5">
            <h3 className="text-[1.05rem]">Committees</h3>
            <p className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
              Ten standing committees carry the working load, each under a written mandate. Any
              financial member may serve; places are confirmed by council at the start of each session.
            </p>
          </div>
          <div className="border-l-2 border-gold-500 pl-5">
            <h3 className="text-[1.05rem]">Elections</h3>
            <p className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
              Nominations open eight weeks before the annual general meeting. Only members whose
              dues are current may nominate, stand or vote. The Immediate Past Chairperson chairs
              the elections committee.
            </p>
          </div>
        </div>
      </Section>

      <Section tone="tinted">
        <SectionHeading
          id="executives"
          title="Principal officers"
          lede="The offices with day-to-day responsibility for the chapter."
          className="mb-8"
        />
        <motion.ul
          className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
          initial="initial"
          whileInView="animate"
          viewport={{ once: true, amount: 0.2 }}
          variants={containerVariants}
        >
          {isLoading
            ? Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-32" />)
            : principals.map((exec) => (
            <motion.li
              key={exec.id}
              className="flex gap-4 border border-border bg-card p-5"
              variants={itemVariants}
              whileHover="hover"
            >
              {exec.photoUrl && (
                <TiltCard max={12} className="h-20 w-20 shrink-0 overflow-hidden rounded-sm">
                  <LazyImage
                    src={exec.photoUrl}
                    alt=""
                    className="h-20 w-20 object-cover object-top"
                  />
                </TiltCard>
              )}
              <div className="min-w-0">
                <h3 className="text-[1rem] leading-snug">
                  {exec.name}, {exec.credential}
                </h3>
                <p className="mt-1 text-[0.8rem] font-semibold text-accent-foreground">{exec.position}</p>
                {exec.bio && (
                  <div
                    className="prose prose-sm mt-2 text-[0.84rem] leading-relaxed text-muted-foreground prose-p:my-1"
                    dangerouslySetInnerHTML={{ __html: exec.bio }}
                  />
                )}
              </div>
            </motion.li>
          ))}
        </motion.ul>
      </Section>

      <Section>
        <SectionHeading title="Other members of council" className="mb-8" />
        <motion.ul
          className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
          initial="initial"
          whileInView="animate"
          viewport={{ once: true, amount: 0.2 }}
          variants={containerVariants}
        >
          {!isLoading && others.map((exec) => (
            <motion.li
              key={exec.id}
              className="flex gap-4 border border-border bg-card p-5"
              variants={itemVariants}
              whileHover="hover"
            >
              {exec.photoUrl && (
                <LazyImage
                  src={exec.photoUrl}
                  alt=""
                  className="h-16 w-16 shrink-0 rounded-sm object-cover object-top"
                />
              )}
              <div className="min-w-0">
                <h3 className="text-[0.98rem] leading-snug">
                  {exec.name}, {exec.credential}
                </h3>
                <p className="mt-1 text-[0.78rem] font-semibold text-accent-foreground">{exec.position}</p>
                <p className="mt-2 text-[0.82rem] leading-relaxed text-muted-foreground">
                  {exec.bio}
                </p>
              </div>
            </motion.li>
          ))}
        </motion.ul>
      </Section>

      <Section tone="tinted">
        <div id="past-chairpersons" className="scroll-mt-24">
          <SectionHeading
            title="Past chairpersons"
            lede="The women who helped establish and lead the Abuja Chapter, from its earliest years onward."
            className="mb-8"
          />
          <motion.ul
            className="flex flex-wrap justify-center gap-x-6 gap-y-10"
            initial="initial"
            whileInView="animate"
            viewport={{ once: true, amount: 0.2 }}
            variants={containerVariants}
          >
            {loadingPastChairpersons
              ? Array.from({ length: 4 }).map((_, i) => (
                  <li key={i} className="w-full sm:w-[calc(50%-0.75rem)] lg:w-[calc(25%-1.125rem)]">
                    <Skeleton className="aspect-[4/5] w-full" />
                    <Skeleton className="mx-auto mt-5 h-5 w-3/4" />
                    <Skeleton className="mx-auto mt-3 h-4 w-1/2" />
                  </li>
                ))
              : pastChairpersons.length > 0
                ? pastChairpersons.map((exec) => (
                  <motion.li
                    key={exec.id}
                    className="flex w-full flex-col items-center text-center sm:w-[calc(50%-0.75rem)] lg:w-[calc(25%-1.125rem)]"
                    variants={itemVariants}
                    whileHover="hover"
                  >
                    <div className="w-full max-w-xs border border-border border-b-4 border-b-gold-500 bg-card p-2 shadow-md">
                      {exec.photoUrl ? (
                        <div className="aspect-[4/5] overflow-hidden bg-secondary">
                          <LazyImage
                            src={exec.photoUrl}
                            alt={exec.name}
                            className="h-full w-full object-cover object-top"
                            hoverZoom={false}
                          />
                        </div>
                      ) : (
                        <div
                          role="img"
                          aria-label={`${exec.name}: portrait unavailable`}
                          className="flex aspect-[4/5] items-center justify-center bg-secondary text-muted-foreground/40"
                        >
                          <UserRound aria-hidden="true" className="h-24 w-24 stroke-1" />
                        </div>
                      )}
                    </div>
                    <div className="mt-5 flex w-full min-w-0 flex-1 flex-col items-center">
                      <h3 className="text-[0.95rem] leading-snug uppercase">
                        {exec.name}{exec.credential ? `, ${exec.credential}` : ''}
                      </h3>
                      {tenureYears(exec) && (
                        <p className="mt-2 text-[0.85rem] text-muted-foreground">
                          {tenureYears(exec)}
                        </p>
                      )}
                      <p className="mt-3 inline-flex border-b-2 border-gold-500 bg-plum-900 px-4 py-1.5 text-[0.8rem] font-semibold text-white">
                        {exec.position || 'Chairperson'}
                      </p>
                    </div>
                  </motion.li>
                ))
                : (
                  <li className="w-full border border-dashed border-border bg-card px-5 py-8 text-sm text-muted-foreground">
                    Past chairperson records will appear here as the chapter archive is added.
                  </li>
                )}
          </motion.ul>
        </div>
      </Section>
    </>
  )
}
