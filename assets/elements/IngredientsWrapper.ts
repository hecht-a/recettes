export class IngredientsWrapper extends HTMLFieldSetElement {
  connectedCallback() {
    const button = document.createElement('button')
    button.innerHTML = 'Ajouter un ingrédient'
    button.classList.add('btn-primary-outlined', 'add-more')
    button.type = 'button'
    const formIngredients = this.querySelector('div')!

    button.addEventListener('click', () => {
      const parent = this.parentElement!
      const {prototype} = parent.dataset

      let id = '0'
      const div = formIngredients.querySelector('div > fieldset:last-child div')
      if(div) {
        id = div.id
      }
      const splittedId = id.split('_')

      const item = Number(splittedId[splittedId.length - 1]) + 1

      formIngredients.insertAdjacentHTML(
        'beforeend',
        `<fieldset class="form-group">${prototype!.replace('__name__', String(item))}</fieldset>`
      )

      const lastElement = formIngredients.lastElementChild!
      const select = lastElement.querySelector('select')!
      select.id = this.formatName(select.id, item)
      select.name = this.formatName(select.name, item)

      const tsControl = lastElement.querySelector('.ts-control')!
      const input = lastElement.querySelector<HTMLInputElement>('input[inputmode=decimal]')!
      const labels = Array.from(lastElement.querySelectorAll('label'))

      tsControl.setAttribute('aria-controls', this.formatName(tsControl.getAttribute('aria-controls')!, item))
      tsControl.setAttribute('aria-labelledby', this.formatName(tsControl.getAttribute('aria-labelledby')!, item))
      tsControl.id = this.formatName(tsControl.id, item)

      input.id = this.formatName(input.id, item)
      input.name = this.formatName(input.name, item)

      labels.forEach((label) => {
        if(label.id) {
          label.id = this.formatName(label.id, item)
        }

        label.htmlFor = this.formatName(label.htmlFor, item)
      })

      this.initDeleteButtons()
    })

    this.initDeleteButtons()
    this.insertAdjacentElement('afterbegin', button)
  }

  initDeleteButtons() {
    const deleteRowBtns = Array.from(this.querySelectorAll<HTMLButtonElement>('.delete_row button'))

    deleteRowBtns.forEach((deleteRowBtn) => {
      deleteRowBtn.innerHTML = `<svg><use href="/alert.svg#error"></use></svg>`

      deleteRowBtn.addEventListener('click', () => {
        deleteRowBtn.closest('fieldset')!.remove()
      })
    })
  }

  private formatName(str: string, replace: string|number): string {
    return str.replace(
      '__name__',
      typeof replace === "string"
        ? replace
        : String(replace)
    )
  }
}
