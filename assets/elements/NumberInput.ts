enum Action {
  Add,
  Remove,
  Change
}

export class NumberInput extends HTMLInputElement {
  input!: HTMLInputElement;
  baseAmountPersons = 0
  quantityElements: HTMLDivElement[] = [];
  baseQuantities: number[] = []

  connectedCallback() {
    const isForm = Boolean(this.getAttribute('is-form'))

    if(isForm) {
      this.outerHTML = `
          <button type="button" class="minus">-</button>
          <input value="${this.value}" type="number" name="${this.name}" id="amount-persons">
          <button type="button" class="plus">+</button>
    `
    } else {
      const classes = this.getAttribute('class')

      this.outerHTML = `
        <div class="amount-persons ${classes}">
          <button type="button" class="minus">-</button>
          <input value="${this.value}" type="number" name="${this.name}" id="amount-persons">
          <button type="button" class="plus">+</button>
        </div>
    `
    }
    this.input = document.querySelector<HTMLInputElement>('#amount-persons')!
    this.baseAmountPersons = this.input.valueAsNumber

    document.querySelector<HTMLButtonElement>('.minus')!.addEventListener('click', this.handler<MouseEvent>(Action.Remove))
    document.querySelector<HTMLButtonElement>('.plus')!.addEventListener('click', this.handler<MouseEvent>(Action.Add))
    this.input.addEventListener('keyup', this.handler<KeyboardEvent>(Action.Change))

    this.quantityElements = Array.from(document.querySelectorAll<HTMLDivElement>('.quantity'))
    this.baseQuantities = this.quantityElements.map((quantity) => Number(quantity.innerHTML.replace(/\D+/g, '')))
  }

  handler<T extends Event>(action: Action) {
    return (e: T) => {
      let value = this.input.valueAsNumber

      if(isNaN(value)) {
        value = 0
      }

      if(value === 0 && action === Action.Remove) {
        return
      }

      switch (action) {
        case Action.Add:
          value += 1
          break;
        case Action.Remove:
          value -= 1
          break;
        case Action.Change:
          const targetValue = (e.target as HTMLInputElement).valueAsNumber
          value = isNaN(targetValue) ? 0 : targetValue
          break;
      }

      this.input.value = String(value)
      this.updateQuantities()
    }
  }

  updateQuantities() {
    this.quantityElements.forEach((quantity, i) => {
      const unit = quantity.innerHTML.replace(/[\d-.]+/g, '').trim()
      const amount = ((this.input.valueAsNumber * this.baseQuantities[i]) / this.baseAmountPersons).toFixed(2)
      quantity.innerHTML = `${amount} ${unit}`
    })
  }
}
