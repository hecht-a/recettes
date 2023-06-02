import dayjs from 'dayjs'
import 'dayjs/locale/fr'

dayjs.locale('fr')

import './css/app.scss'

import {Alert} from "./elements/Alert";
import {InputChoices, SelectChoices} from "./elements/Choices";

customElements.define('alert-message', Alert)
customElements.define('input-choices', InputChoices, {extends: 'input'})
customElements.define('select-choices', SelectChoices, {extends: 'select'})
