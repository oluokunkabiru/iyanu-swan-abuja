/** CPD is disabled on the website; retain the managed content for later use. */
export function containsCpd(...values: unknown[]): boolean {
  return values.some((value) => {
    if (typeof value === 'string') {
      return /\bcpd\b|\bcontinu(?:ing|ous)\s+professional\s+development\b/i.test(value)
    }
    if (Array.isArray(value)) return value.some((item) => containsCpd(item))
    if (value && typeof value === 'object') return containsCpd(...Object.values(value))
    return false
  })
}

/** Hide CPD paragraphs and links in rich text while retaining the rest of an article. */
export function withoutCpdHtml(html: string): string {
  const document = new DOMParser().parseFromString(html, 'text/html')
  if (!containsCpd(document.body.textContent, html)) return html
  const blocks = 'p, li, h1, h2, h3, h4, h5, h6, blockquote, figcaption, a'
  for (const element of document.body.querySelectorAll(blocks)) {
    if (containsCpd(element.textContent, element.getAttribute('href'))) element.remove()
  }

  // Also cover unwrapped text and custom editor markup.
  const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT)
  const hidden: Node[] = []
  while (walker.nextNode()) {
    if (containsCpd(walker.currentNode.textContent)) hidden.push(walker.currentNode)
  }
  hidden.forEach((node) => node.parentNode?.removeChild(node))
  return document.body.innerHTML
}
