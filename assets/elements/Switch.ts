export class Switch extends HTMLInputElement {
  switch?: HTMLSpanElement|null

  connectedCallback () {
    if (this.nextElementSibling === null || this.nextElementSibling.tagName !== 'LABEL') {
      console.error('Impossible de greffer le switch')
      return
    }
    this.parentElement!.classList.add('form-switch')
    this.parentElement!.classList.remove('form-check')
    this.switch = document.createElement('span')
    this.switch.classList.add('switch')
    this.nextElementSibling.prepend(this.switch)
    this.addEventListener('change', this.onChange.bind(this))
  }

  onChange () {
    const params = new URLSearchParams(window.location.search)
    if (this.checked) {
      params.set(this.name, this.value)
    } else {
      params.delete(this.name)
    }
    if (params.has('page')) {
      params.delete('page')
    }
  }

  disconnectedCallback () {
    if (this.parentElement) {
      this.parentElement.classList.remove('form-switch')
    }
    this.switch!.parentElement!.removeChild(this.switch!)
  }
}
