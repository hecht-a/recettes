import {debounce} from "../functions/debouce.ts";

type Status = 'liked' | 'disliked' | 'loading'

export class FavoriteButton extends HTMLElement {
  connectedCallback() {
    const isLiked = this.getAttribute('is-liked')

    this.setButton(isLiked ? 'liked' : 'disliked')
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

  setButton(status: Status) {
    if(status === 'liked') {
      this.setAttribute('is-liked', 'true')
      this.innerHTML = this.buttonTemplate('Supprimer des favoris', 'btn btn-like-dislike btn-dislike', 'emptyheart')
    } else if(status === 'disliked') {
      this.removeAttribute('is-liked')
      this.innerHTML = this.buttonTemplate('Ajouter aux favoris', 'btn btn-like-dislike btn-like', 'fullheart')
    } else {
      this.innerHTML = `<button type="button" class="btn btn-like-dislike btn-loading mt1" disabled>
        Chargement
        <circle-loader></circle-loader>
      </button>`
    }
    this.querySelector('button')!.addEventListener('click', this.handler())
  }

  handler() {
    return debounce(async () => {
      const isLiked = this.getAttribute('is-liked')
      const recipe = this.getAttribute('data-recipe')!
      const uri = isLiked ? `/api/dislike/${recipe}` : `/api/like/${recipe}`

      this.setButton('loading')
      const {status} = await fetch(uri).then<{status: Status}>((response) => response.json())

      this.setButton(status)
    }, 300)
  }
}
