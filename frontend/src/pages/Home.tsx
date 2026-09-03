import { CoreValues } from '@/components/sections/CoreValues'
import { ExecutiveGrid } from '@/components/sections/ExecutiveGrid'
import { Hero } from '@/components/sections/Hero'
import { MembershipSteps } from '@/components/sections/MembershipSteps'
import { MemberSpotlightCarousel } from '@/components/sections/MemberSpotlightCarousel'
import { MissionVision } from '@/components/sections/MissionVision'
import { NewsPreview } from '@/components/sections/NewsPreview'
import { Partners } from '@/components/sections/Partners'
import { UpcomingEvents } from '@/components/sections/UpcomingEvents'

export default function Home() {
  return (
    <>
      <Hero />
      <MissionVision />
      <CoreValues />
      <MembershipSteps />
      <UpcomingEvents />
      <ExecutiveGrid limit={5} />
      <MemberSpotlightCarousel />
      <NewsPreview />
      <Partners />
    </>
  )
}
