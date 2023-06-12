import dayjs from 'dayjs'
import 'dayjs/locale/fr'

dayjs.locale('fr')

import './css/app.scss'

import {Alert} from "./elements/Alert";
import {InputChoices, SelectChoices} from "./elements/Choices";
import {IngredientField} from "./elements/IngredientField";
import {IngredientsWrapper} from "./elements/IngredientsWrapper";
import {StepsWrapper} from "./elements/StepsWrapper";
import {AllergenLetters} from "./elements/AllergenLetters";

customElements.define('alert-message', Alert)
customElements.define('input-choices', InputChoices, {extends: 'input'})
customElements.define('select-choices', SelectChoices, {extends: 'select'})
customElements.define('ingredient-field', IngredientField, {extends: 'fieldset'})
customElements.define('ingredients-wrapper', IngredientsWrapper, {extends: 'fieldset'})
customElements.define('steps-wrapper', StepsWrapper, {extends: 'fieldset'})
customElements.define('allergen-letters', AllergenLetters)
