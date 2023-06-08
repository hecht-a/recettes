export class StepsWrapper extends HTMLFieldSetElement {
  connectedCallback() {
    const button = document.createElement('button')
    button.innerHTML = 'Ajouter une étape'
    button.classList.add('btn-primary-outlined', 'add-more')
    button.type = 'button'
    const formIngredients = this.querySelector('div')!

    button.addEventListener('click', () => {
      const parent = this.parentElement!
      const {prototype} = parent.dataset

      let id = '0'
      const div = formIngredients.querySelector('div > fieldset:last-child div')
      if (div) {
        id = div.id
      }
      const splittedId = id.split('_')

      const item = Number(splittedId[splittedId.length - 1]) + 1

      formIngredients.insertAdjacentHTML(
        'beforeend',
        `<fieldset class="form-group">${prototype!.replace('__name__', String(item))}</fieldset>`
      )

      const lastElement = formIngredients.lastElementChild!

      const input = lastElement.querySelector<HTMLInputElement>('textarea')!
      const labels = Array.from(lastElement.querySelectorAll('label'))

      input.id = this.formatName(input.id, item)
      input.name = this.formatName(input.name, item)

      labels.forEach((label) => {
        if (label.id) {
          label.id = this.formatName(label.id, item)
        }

        label.htmlFor = this.formatName(label.htmlFor, item)
      })

      this.initMoveButtons()
      this.initDeleteButtons()

      this.enableDragSort()
    })

    this.initMoveButtons()
    this.initDeleteButtons()
    this.insertAdjacentElement('afterbegin', button)

    this.enableDragSort()
  }

  enableDragSort() {
    const draggables = Array.from(this.querySelectorAll<HTMLFieldSetElement>('.form-group.steps > div fieldset'))

    draggables.forEach((draggable) => {
      draggable.draggable = true

      draggable.addEventListener('dragstart', (e) => {
        const activeClassList = document.activeElement!.classList

        if (!activeClassList.contains('move_row') && !activeClassList.contains('move_row_btn')) {
          e.preventDefault()
          return
        }

        draggable.classList.add("dragging")
      })

      draggable.addEventListener("dragend", () => {
        draggable.classList.remove("dragging")
      });

      draggable.addEventListener('drag', this.handleDrag)
    })
  }

  handleDrag(item: DragEvent) {
    const selectedItem = item.target! as HTMLElement
    const list = selectedItem.parentNode
    const x = item.clientX
    const y = item.clientY

    const swapItem = document.elementFromPoint(x, y) ?? selectedItem!
    const closest = swapItem.closest('fieldset')!

    if (list!.contains(selectedItem) && list!.contains(swapItem.nextSibling) && list!.contains(swapItem)) {
      if (swapItem !== closest) {
        list!.insertBefore(selectedItem, closest)
      } else {
        list!.insertBefore(selectedItem, closest.nextSibling)
      }
    }
  }

  initMoveButtons() {
    const moveRowBtns = Array.from(this.querySelectorAll<HTMLButtonElement>('.move_row button'))

    moveRowBtns.forEach((moveRowBtn) => {
      moveRowBtn.classList.add('move_row_btn')
      moveRowBtn.innerHTML = `<svg><use href="/icons.svg#threedots_move"></use></svg>`

      moveRowBtn.addEventListener('dragstart', () => {
        console.log('click')
      })
    })
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

  private formatName(str: string, replace: string | number): string {
    return str.replace(
      '__name__',
      typeof replace === "string"
        ? replace
        : String(replace)
    )
  }
}
