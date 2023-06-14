import {SearchPopup} from "./SearchPopup";

export class SearchButton extends HTMLElement {
  constructor() {
    super()

    this.handler = this.handler.bind(this)
  }
  connectedCallback() {
    this.innerHTML = `<button>
                    <svg>
                        <use href="/icons.svg#search"></use>
                    </svg>
                </button>`

    this.querySelector('button')!.addEventListener('click', this.displaySearchBar)

    window.addEventListener('keydown', this.handler)
  }

  disconnectedCallback() {
    window.removeEventListener('keydown', this.handler)
  }

  displaySearchBar() {
    document.body.insertAdjacentElement('beforeend', new SearchPopup())
  }

  handler(e: KeyboardEvent) {
    if (['k', ' '].includes(e.key) && e.ctrlKey) {
      e.preventDefault()
      this.displaySearchBar()
    }
  }
}
