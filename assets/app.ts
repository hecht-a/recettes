import dayjs from 'dayjs'
import 'dayjs/locale/fr'
import 'htmx.org'

dayjs.locale('fr')

import './styles/app.scss'

import {Alert} from "./elements/Alert.ts";
import {InputChoices, SelectChoices} from "./elements/Choices.ts";
import {IngredientField} from "./elements/IngredientField.ts";
import {IngredientsWrapper} from "./elements/IngredientsWrapper.ts";
import {StepsWrapper} from "./elements/StepsWrapper.ts";
import {GlossaryLetters} from "./elements/GlossaryLetters.ts";
import {SearchButton} from "./elements/search/SearchButton.ts";
import {SearchInput} from "./elements/search/SearchInput.ts";
import {SearchPopup} from "./elements/search/SearchPopup.ts";
import {FavoriteButton} from "./elements/FavoriteButton.ts";
import {Switch} from "./elements/Switch.ts";
import {NumberInput} from "./elements/NumberInput.ts";
import {Loader} from "./elements/Loader.ts";

customElements.define('alert-message', Alert)
customElements.define('input-choices', InputChoices, {extends: 'input'})
customElements.define('select-choices', SelectChoices, {extends: 'select'})
customElements.define('ingredient-field', IngredientField, {extends: 'fieldset'})
customElements.define('ingredients-wrapper', IngredientsWrapper, {extends: 'fieldset'})
customElements.define('steps-wrapper', StepsWrapper, {extends: 'fieldset'})
customElements.define('glossary-letters', GlossaryLetters)
customElements.define('switch-input', Switch, {extends: 'input'})
customElements.define('search-input', SearchInput)
customElements.define('search-popup', SearchPopup)
customElements.define('search-button', SearchButton)
customElements.define('favorite-button', FavoriteButton)
customElements.define('number-input', NumberInput, {extends: 'input'})
customElements.define('circle-loader', Loader)

declare global {
  interface DocumentEventMap {
    "htmx:load": Event & {detail: {elt: HTMLElement}}
  }
}

document.addEventListener('htmx:load', (event) => {
  const burger = document.querySelector('.header_burger .burger')!
  const navbar = document.querySelector('.header')!

  if(burger) {
    burger.addEventListener('click', () => {
      navbar.classList.toggle('is-open')
    })
  }
})
