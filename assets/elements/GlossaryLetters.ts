export class GlossaryLetters extends HTMLElement {
  private links: HTMLAnchorElement[] = [];
  private sectionToAnchor: Map<any, HTMLAnchorElement> = new Map();
  constructor () {
    super()
    this.onIntersection = this.onIntersection.bind(this)
  }

  connectedCallback () {
    const observer = new IntersectionObserver(entries => entries.forEach(this.onIntersection), {
      rootMargin: '-20% 0px -80% 0px'
    })

    const links = Array.from(this.querySelectorAll('a'))
    this.sectionToAnchor = new Map(
      links.map(link => {
        const section = document.querySelector(link.getAttribute('href')!)
        if (!section) {
          link.setAttribute('disabled', '')
          return [null, link]
        }
        observer.observe(section)
        return [section, link]
      })
    )

    this.links = links
  }

  onIntersection (entry: IntersectionObserverEntry) {
    const link = this.sectionToAnchor.get(entry.target)
    if (!link) {
      return
    }
    if (entry.isIntersecting) {
      link.classList.add('is-active')
    } else {
      link.classList.remove('is-active')
    }
  }
}
