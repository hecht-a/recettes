import dayjs from 'dayjs'
import 'dayjs/locale/fr'

dayjs.locale('fr')

import './css/app.scss'

import {Alert} from "./elements/Alert";

customElements.define('alert-message', Alert)
