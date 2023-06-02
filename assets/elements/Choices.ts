import TomSelect from 'tom-select'
import {RecursivePartial, TomLoadCallback} from "tom-select/src/types";
import {TomSettings} from "tom-select/dist/types/types";

export class InputChoices extends HTMLInputElement {
  widget!: TomSelect
  connectedCallback() {
    console.log(this.widget)
  }
  disconnectedCallback() {}
}

export class SelectChoices extends HTMLSelectElement {
  widget!: TomSelect

  connectedCallback() {}
  disconnectedCallback() {}
}

/**
 * Ajoute le comportement sur les select / champs
 */
function bindBehaviour (cls: typeof InputChoices|typeof SelectChoices) {
  cls.prototype.connectedCallback = function () {
    if (this.getAttribute('choicesBinded')) {
      return
    }
    this.setAttribute('choicesBinded', 'true')

    // Ajout de plugins suivant le type de champs mappé
    const options: RecursivePartial<TomSettings & {plugins: Record<string, Record<string, string>>}> = {
      hideSelected: true,
      persist: false,
      plugins: {},
      closeAfterSelect: true
    }
    console.log(this.tagName)
    if (this.tagName === 'SELECT') {
      console.log('ok')
      options.allowEmptyOption = true
      if(options.plugins) {
        options.plugins.no_backspace_delete = {}
        options.plugins.dropdown_input = {}
        if (this.getAttribute('multiple')) {
          options.plugins.remove_button = {
            title: 'Supprimer cet élément'
          }
        }
      }
    } else {
      if(options.plugins) {
        options.plugins.remove_button = {
          title: 'Supprimer cet élément'
        }
      }
    }

    // On configure les options en fonction de la situation
    if (this.dataset.remote) {
      options.valueField = this.dataset.value
      options.labelField = this.dataset.label
      options.searchField = [this.dataset.label]
      options.load = async (query: string, callback: TomLoadCallback) => {
        const url = `${this.dataset.remote}?q=${encodeURIComponent(query)}`
        const data = await fetch(url).then((response) => response.json())
        callback(data, [])
      }
    }
    if (this.dataset.create) {
      options.create = true
    }
    this.widget = new TomSelect(this, options)
  }

  cls.prototype.disconnectedCallback = function () {
    if (this.widget) {
      this.widget.destroy()
    }
  }
}

Array.from([InputChoices, SelectChoices]).forEach(bindBehaviour)
