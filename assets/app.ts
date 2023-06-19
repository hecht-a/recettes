import dayjs from 'dayjs'
import 'dayjs/locale/fr'
import 'htmx.org';

dayjs.locale('fr')

import './css/app.scss'

import {Alert} from "./elements/Alert";
import {InputChoices, SelectChoices} from "./elements/Choices";
import {IngredientField} from "./elements/IngredientField";
import {IngredientsWrapper} from "./elements/IngredientsWrapper";
import {StepsWrapper} from "./elements/StepsWrapper";
import {GlossaryLetters} from "./elements/GlossaryLetters";
import {SearchButton} from "./elements/search/SearchButton";
import {SearchInput} from "./elements/search/SearchInput";
import {SearchPopup} from "./elements/search/SearchPopup";
import {FavoriteButton} from "./elements/FavoriteButton";
import {Switch} from "./elements/Switch";
import {NumberInput} from "./elements/NumberInput";

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

declare global {
  interface DocumentEventMap {
    "htmx:load": Event & {detail: {elt: HTMLElement}}
  }
}

document.addEventListener('htmx:load', (event: Event & {detail: {elt: unknown}}) => {
  const burger = document.querySelector('.header_burger .burger')!
  const navbar = document.querySelector('.header')!

  burger.addEventListener('click', () => {
    navbar.classList.toggle('is-open')
  })
})
