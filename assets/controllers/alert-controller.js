import {Controller} from '@hotwired/stimulus'
import {getComponent} from '@symfony/ux-live-component';

export default class extends Controller {
  static values = {
    delay: Number
  }

  timeoutId = null

  async connect() {
    this.component = await getComponent(this.element)

    this.component.on('render:finished', () => {
      const isVisible = !this.element.classList.contains('hidden')

      if (!isVisible) {
        return
      }

      if (this.timeoutId) {
        clearTimeout(this.timeoutId)
      }

      this.timeoutId = setTimeout(() => {
        this.component.action('reset')
        this.timeoutId = null // on réinitialise
      }, this.delayValue || 4000)
    })
  }

  disconnect() {
    if (this.timeoutId) {
      clearTimeout(this.timeoutId)
    }
  }
}
