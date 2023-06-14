import {SearchPopup} from "./SearchPopup";

export class SearchButton extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `<button>
                    <svg>
                        <use href="/icons.svg#search"></use>
                    </svg>
                </button>`

    this.querySelector('button')!.addEventListener('click', () => {
      document.body.insertAdjacentElement('beforeend', new SearchPopup())
    })
  }
}
