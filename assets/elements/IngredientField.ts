// TODO: SEE IF IT'S USEFUL

import TomSelect from "tom-select";
import {TomOption} from "tom-select/src/types/core";
import {escape_html} from "tom-select/src/utils";

type Value = string | number

export class IngredientField extends HTMLFieldSetElement {
  prevOption: Value|null = null

  constructor() {
    super()

    this.toggleOptions = this.toggleOptions.bind(this)
  }

  connectedCallback() {
    const select = this.querySelector('select')!

    const tom = new TomSelect(select, {
      hideSelected: true,
      persist: false,
      closeAfterSelect: true,
      plugins: {
        no_backspace_delete: {},
        dropdown_input: {},
      },
      render: {
        no_results: ({input}: TomOption, escape: typeof escape_html) => {
          return `<div class="no-results">Aucun résultat pour <em>"${escape(input)}"</em></div>`;
        }
      }
    })

    tom.on('change', this.toggleOptions(tom))
    tom.refreshOptions(false)
  }

  toggleOptions(tom: TomSelect) {
    return () => {
      if(this.prevOption) {
        const hiddenOptions = Array.from(document.querySelectorAll(`[data-value="${this.prevOption}"].hidden`))
        hiddenOptions.forEach((hiddenOption) => {
          hiddenOption.classList.remove('hidden')
        })
      }

      const toms = Array.from(document.querySelectorAll<HTMLDivElement>('.ts-wrapper.single .ts-control'))

      toms
        .filter((tomFilter) => tom.control !== tomFilter)
        .forEach((tomFiltered) => {
          const item = tomFiltered.parentElement!.querySelector<HTMLDivElement>(`[data-value="${tom.getValue()}"]`)
          item!.classList.add('hidden')
          this.prevOption = tom.getValue() as string
        })
    }
  }
}
