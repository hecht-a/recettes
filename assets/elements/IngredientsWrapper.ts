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
        const id = div.id
      }
      const splittedId = id.split('_')

      const item = Number(splittedId[splittedId.length - 1]) + 1

      formIngredients.insertAdjacentHTML(
        'beforeend',
        `<fieldset class="form-group">${prototype!.replace('__name__', String(item))}</fieldset>`
      )
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
}
