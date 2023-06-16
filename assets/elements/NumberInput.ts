export class NumberInput extends HTMLInputElement {
  connectedCallback() {
    console.log('ok')
    this.outerHTML = `
        <button>plus</button>
        <input type="number" name="recipe_form[amountPersons]" id="amount-persons">
        <button>moins</button>
    `
  }
}
