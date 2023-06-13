import {debounce} from "../../functions/debouce";

const SEARCH_API = '/api/search'

type Item = {
  title: string,
  url: string,
  categories: string
}

export class SearchInput extends HTMLElement {
  input = ''

  constructor() {
    super()

    this.onInput = this.onInput.bind(this)
    this.suggest = this.suggest.bind(this)
  }

  connectedCallback() {
    this.innerHTML = `
      <form>
        <input autofocus autocomplete="off" type="text" name="q" placeholder="Rechercher une recette..." />
<!--        <button type="submit">-->
<!--          <svg>-->
<!--              <use href="/icons.svg#search"></use>-->
<!--          </svg>-->
<!--        </button>-->
        <ul class="results"></ul>
      </form>
    `

    const input = this.querySelector('input')!
    input.focus()
    input.addEventListener('input', this.onInput)
  }

  onInput(e: Event) {
    const target = e.target as HTMLInputElement
    this.input = target.value

    this.suggest()(this.input)
  }

  suggest() {
    return debounce(async (value: string) => {
      const data = await fetch(`${SEARCH_API}?q=${encodeURI(value)}`).then<Item[]>((response) => response.json())
      const results = this.querySelector('.results')!
      const items: string[] = data.map((item) => {
        return this.buildItem(item).innerHTML
      })

      results.innerHTML = items.join('')
    }, 300)
  }

  buildItem(item: Item): HTMLLIElement {
    const li = document.createElement('li')
    const a = document.createElement('a')

    a.href = item.url
    a.innerHTML = item.title

    li.appendChild(a)

    return li
  }
}
