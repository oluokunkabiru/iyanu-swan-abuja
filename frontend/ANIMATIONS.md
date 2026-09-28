# Frontend Animation Setup

This document outlines the animation enhancements made to the React frontend to create a more interactive and modern user experience.

## What's Been Added

### 1. **Framer Motion Library**
- Added `framer-motion` v11.0.0 as a dependency (React 19 compatible)
- Provides smooth, performant animations using requestAnimationFrame

### 2. **Animation Utilities** (`src/lib/animations.ts`)
Predefined animation variants for consistent animations across the app:

- **pageVariants**: Fade-in + slide-up animation for entire pages
- **blockVariants**: Fade-in + slide-up for individual blocks
- **containerVariants**: Staggered animations for child elements
- **itemVariants**: Individual item animations within containers
- **scaleVariants**: Scale-up with fade-in effect
- **slideInVariants**: Directional slide animations (left, right, up, down)
- **hoverScaleVariants**: Interactive scale on hover/tap
- **animationTransition**: Reusable timing configurations

### 3. **AnimatedBlock Component** (`src/components/common/AnimatedBlock.tsx`)
Wrapper component that automatically animates child content when it enters the viewport:

```tsx
<AnimatedBlock delay={0.1} variant="scale">
  <Card>Content animates in when scrolled into view</Card>
</AnimatedBlock>
```

**Props:**
- `children`: React node to animate
- `className`: Additional CSS classes
- `delay`: Stagger delay in seconds
- `variant`: Animation type - 'block' | 'scale' | 'slideUp'

### 4. **Enhanced Layout Component** (`src/components/layout/Layout.tsx`)
- Main content area now animates with `pageVariants`
- Smooth transitions when navigating between pages
- Fade-in + slide-up effect on page load

### 5. **Enhanced Section Component** (`src/components/common/Primitives.tsx`)
- `Section` wrapper now supports built-in animations
- Staggered animations for child elements
- Animations trigger when scrolled into view
- NEW prop: `animate` (boolean) - disable animations if needed

### 6. **Home Page Animations** (`src/pages/Home.tsx`)
Enhanced with staggered animations on:
- Quick desks (4-column grid)
- Chapter statistics (4 stat cards)
- Featured events (3 event cards)
- Chapter council members (5 profile cards)
- Chapter news (3 news cards)

## How It Works

### Page Transitions
When you navigate between routes, the main content area fades in and slides up smoothly over 500ms.

### Block Animations
Blocks (sections, cards, items) animate when they enter the viewport:
1. **Trigger**: Element scrolls into view
2. **Animation**: Staggered fade-in + slide-up
3. **Duration**: 500ms per item with 100ms stagger
4. **Performance**: Uses `viewport={{ once: true }}` to animate only once

### Staggered Containers
Grid layouts use container-based staggering:
```tsx
<motion.div variants={containerVariants}>
  {items.map((item) => (
    <motion.div variants={itemVariants} key={item.id}>
      {/* Each item animates with 100ms delay */}
    </motion.div>
  ))}
</motion.div>
```

## Using Animations in Your Components

### Method 1: Use Section with Built-in Animations
```tsx
<Section animate={true}>
  <div>Content animates automatically</div>
</Section>
```

### Method 2: Use AnimatedBlock
```tsx
<AnimatedBlock delay={0.2} variant="scale">
  <Card>Your card content</Card>
</AnimatedBlock>
```

### Method 3: Use Motion Components Directly
```tsx
import { motion } from 'framer-motion'
import { itemVariants, containerVariants } from '@/lib/animations'

<motion.div variants={containerVariants} initial="initial" whileInView="animate">
  {items.map((item) => (
    <motion.div variants={itemVariants} key={item.id}>
      {item.content}
    </motion.div>
  ))}
</motion.div>
```

## Customization

### Adjust Global Animation Timing
Edit `src/lib/animations.ts`:

```ts
export const pageVariants = {
  animate: {
    transition: {
      duration: 0.5,      // Change page transition speed
      ease: 'easeOut',
    },
  },
}
```

### Create Custom Animations
Add new variants to `src/lib/animations.ts`:

```ts
export const myCustomVariant = {
  initial: { opacity: 0, rotate: -10 },
  animate: {
    opacity: 1,
    rotate: 0,
    transition: { duration: 0.6 },
  },
}
```

Then use in components:
```tsx
import { myCustomVariant } from '@/lib/animations'

<motion.div variants={myCustomVariant} initial="initial" whileInView="animate">
  Content
</motion.div>
```

## Performance Notes

- Animations use `viewport={{ once: true }}` to prevent repeated re-triggering
- Framer Motion uses GPU-accelerated transforms for smooth 60fps animations
- Animations are skipped on reduced-motion preferences (respects `prefers-reduced-motion`)
- Lazy loading via `whileInView` ensures animations only load when needed

## Browser Support

Framer Motion v11 supports all modern browsers:
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Next Steps

To add animations to more pages:

1. Import animation utilities and motion
2. Wrap grids/lists with `motion.div` + `containerVariants`
3. Wrap items with `motion.div` + `itemVariants`
4. Or use the simple `AnimatedBlock` wrapper

Example for any list page:
```tsx
import { motion } from 'framer-motion'
import { containerVariants, itemVariants } from '@/lib/animations'

const MyPage = () => {
  return (
    <motion.div
      className="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
      initial="initial"
      whileInView="animate"
      viewport={{ once: true, amount: 0.2 }}
      variants={containerVariants}
    >
      {items.map((item) => (
        <motion.div key={item.id} variants={itemVariants}>
          {/* Your card or item component */}
        </motion.div>
      ))}
    </motion.div>
  )
}
```
