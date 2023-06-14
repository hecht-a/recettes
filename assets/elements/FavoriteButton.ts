import {debounce} from "../functions/debouce";

export class FavoriteButton extends HTMLElement {
  connectedCallback() {
    const isLiked = this.getAttribute('is-liked')

    this.setButton(isLiked)
  }

  buttonTemplate(label: string, className: string, icon: string) {
    return `
    <button type="button" class="${className} mt1">
        ${label}
        <svg>
            <use href="/icons.svg#${icon}"></use>
        </svg>
      </button>
    `
  }

  setButton(isLiked: string|null) {
    if(isLiked !== null) {
      this.setAttribute('is-liked', 'true')
      this.innerHTML = this.buttonTemplate('Supprimer des favoris', 'btn-dislike', 'emptyheart')
    } else {
      this.removeAttribute('is-liked')
      this.innerHTML = this.buttonTemplate('Ajouter aux favoris', 'btn-like', 'fullheart')
    }
    this.querySelector('button')!.addEventListener('click', this.handler())
  }

  handler() {
    return debounce(async () => {
      const isLiked = this.getAttribute('is-liked')
      const recipe = this.getAttribute('data-recipe')!
      const uri = isLiked ? `/api/dislike/${recipe}` : `/api/like/${recipe}`
      await fetch(uri)
      this.setButton(isLiked)
    }, 300)
  }
}
