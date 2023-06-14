export class SearchPopup extends HTMLElement {
  constructor() {
    super()

    this.handler = this.handler.bind(this)
  }
  connectedCallback() {
    this.innerHTML = `
        <search-input></search-input>
    `

    window.addEventListener('keyup', this.handler)
    this.addEventListener('click', (e) => {
      if(e.target === e.currentTarget) {
        this.remove()
      }
    })
  }

  disconnectedCallback() {
    window.removeEventListener('keyup', this.handler)
  }

  handler(e: KeyboardEvent) {
    if(e.key === 'Escape') {
      this.remove()
    }
  }
}
