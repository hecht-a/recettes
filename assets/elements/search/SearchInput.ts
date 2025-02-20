import {debounce} from "../../functions/debouce.ts";

const SEARCH_API = '/api/search'

type Item = {
  title: string,
  url: string,
  categories: string,
  image: string | null
}

enum Moves {
  DOWN = 1,
  UP = -1
}

export class SearchInput extends HTMLElement {
  input = ''
  results: Item[] = []
  selectedItem: number | null = null

  constructor() {
    super()

    this.onInput = this.onInput.bind(this)
    this.moveFocusHandler = this.moveFocusHandler.bind(this)
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
    window.addEventListener('keydown', this.moveFocusHandler)

    this.querySelector('form')!.addEventListener('submit', (e) => {
      if (this.selectedItem !== null && this.results[this.selectedItem]) {
        e.preventDefault()
        window.location.href = this.results[this.selectedItem].url
      }
    })
  }

  disconnectedCallback() {
    window.removeEventListener('keydown', this.moveFocusHandler)
  }

  onInput(e: Event) {
    const target = e.target as HTMLInputElement
    this.input = target.value

    this.suggest()(this.input)
  }

  suggest() {
    return debounce(async (value: string) => {
      this.results = await fetch(`${SEARCH_API}?q=${encodeURI(value)}`).then<Item[]>((response) => response.json())
      if (this.results.length < 1) {
        const results = this.querySelector('.results')!
        if (results) {
          results.remove()
        }
        return
      }

      const results = document.createElement('ul')
      results.classList.add('results', 'search-input_suggestions', 'card')
      const previousResults = this.querySelector('.search-input_suggestions ')
      if (previousResults) {
        previousResults.remove()
      }

      this.querySelector('form')!.appendChild(results)
      const items: string[] = this.results.map((item) => {
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

  selectItem(item: number | null) {
    const focused = this.querySelector('.focused')
    if (focused) {
      focused.classList.remove('focused')
    }

    if (item === null) {
      return
    }

    Array.from(this.querySelectorAll('a'))[item].classList.add('focused')
  }

  moveFocus(direction: Moves) {
    if (this.results.length === 0) {
      return
    }

    if (this.selectedItem === null && direction === Moves.DOWN) {
      this.selectedItem = 0
      this.selectItem(this.selectedItem)
      return
    }

    if (this.selectedItem === null && direction === Moves.UP) {
      this.selectedItem = this.results.length - 1
      this.selectItem(this.selectedItem)
      return
    }

    const newPosition = this.selectedItem! + direction
    if (newPosition < 0 || newPosition >= this.results.length) {
      this.selectedItem = null
      this.selectItem(this.selectedItem)
      return
    }
    this.selectedItem = newPosition
    this.selectItem(this.selectedItem)
  }

  moveFocusHandler(e: KeyboardEvent) {
    switch (e.key) {
      case 'ArrowDown':
      case 'Tab':
        e.preventDefault()
        this.moveFocus(Moves.DOWN)
        return
      case 'ArrowUp':
        this.moveFocus(Moves.UP)
        break
      default:
    }
  }
}
