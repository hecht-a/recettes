import './bootstrap.ts'

import dayjs from 'dayjs'
import 'dayjs/locale/fr'
import 'htmx.org'

dayjs.locale('fr')

import './styles/tools/reset.css';
import './styles/tools/mixins.css';
import './styles/tools/layout.css';
import './styles/tools/typography.css';

import './styles/pages/home.css';
import './styles/pages/recipe.css';
import './styles/pages/auth.css';

import './styles/modules/header.css';
import './styles/modules/card.css';
import './styles/modules/pagination.css';
import './styles/modules/step.css';
import './styles/modules/ingredient_utensil.css';
import './styles/modules/form.css';
import './styles/modules/alert.css';
import './styles/modules/table.css';
import './styles/modules/select.css';
import './styles/modules/btn.css';
import './styles/modules/icon.css';
import './styles/modules/glossary.css';
import './styles/modules/search.css';
import './styles/modules/filter.css';
import './styles/modules/progressbar.css';
import './styles/app.css'

import {Alert} from "./elements/Alert.ts";
import {InputChoices, SelectChoices} from "./elements/Choices.ts";
import {IngredientField} from "./elements/IngredientField.ts";
import {IngredientsWrapper} from "./elements/IngredientsWrapper.ts";
import {StepsWrapper} from "./elements/StepsWrapper.ts";
import {GlossaryLetters} from "./elements/GlossaryLetters.ts";
import {SearchButton} from "./elements/search/SearchButton.ts";
import {SearchInput} from "./elements/search/SearchInput.ts";
import {SearchPopup} from "./elements/search/SearchPopup.ts";
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
customElements.define('number-input', NumberInput, {extends: 'input'})
customElements.define('circle-loader', Loader)

declare global {
  interface DocumentEventMap {
    "htmx:load": Event & {detail: {elt: HTMLElement}}
  }
}

document.addEventListener('htmx:load', () => {
  const burger = document.querySelector('.header_burger .burger')!
  const navbar = document.querySelector('.header')!

  if(burger) {
    burger.addEventListener('click', () => {
      navbar.classList.toggle('is-open')
    })
  }
})
