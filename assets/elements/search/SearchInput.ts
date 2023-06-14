import {debounce} from "../../functions/debouce";

const SEARCH_API = '/api/search'

type Item = {
  title: string,
  url: string,
  categories: string,
  image: string | null
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
        <button type="submit" class="search">
          <svg class="icon">
              <use href="/icons.svg#search"></use>
          </svg>
        </button>
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
      if (data.length < 1) {
        const results = this.querySelector('.results')!
        if (results) {
          results.remove()
        }
        return
      }

      const results = document.createElement('ul')
      results.classList.add('results', 'search-input_suggestions')
      this.querySelector('form')!.appendChild(results
      )
      const items: string[] = data.map((item) => {
        return this.buildItem(item).outerHTML
      })

      results.innerHTML = items.join('')
    }, 300)
  }

  buildItem(item: Item): HTMLLIElement {
    const li = document.createElement('li')
    const a = document.createElement('a')

    a.href = item.url
    a.innerHTML = `
        <span class='search-input_category'>${item.categories}</span>
        <span class='search-input_title'>${item.title}</span>
    `

    if (item.image) {
      a.innerHTML += `<img src="${item.image}" alt="${item.title}" class="search-input_image" />`
    }

    li.appendChild(a)

    return li
  }
}
