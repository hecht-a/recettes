type AlertProps = {
  type: 'success' | 'error' | 'info' | 'warning' | "",
  title: string,
  description: string
}

const initialize: AlertProps = {
  type: '',
  title: '',
  description: ''
}

export class Alert extends HTMLElement {
  type: AlertProps['type']
  title: AlertProps['title']
  description: AlertProps['description']

  constructor({type, title, description}: AlertProps = initialize) {
    super()

    this.type = type
    this.title = title
    this.description = description

    this.close = this.close.bind(this)
  }

  connectedCallback() {
    this.type = this.type === "" ? this.getAttribute('type') as AlertProps['type'] : this.type
    this.description = this.description || this.getAttribute('description')!
    this.title = this.title || this.getAttribute('title')!
    const duration = this.getAttribute('duration')

    let progressBar = ''
    if (duration !== null) {
      progressBar = `<div class="alert-progress" style="animation-duration: ${duration}s">`
      window.setTimeout(this.close, Number(duration) * 1000)
    }

    this.innerHTML = `<div class="alert ${(this.type)}">
        <div class="icon">
            <svg>
                <use href="/alert.svg#${(this.type)}"></use>
            </svg>
        </div>
        <div class="content">
            <h3>${this.title}</h3>
            <p>${this.description}</p>
        </div>
        <div class="close">
            <svg>
                <use href="/alert.svg#close"></use>
            </svg>
        </div>
        ${progressBar}
    </div>`

    this.querySelector('.close')?.addEventListener('click', (e) => {
      e.preventDefault()
      this.close()
    })
  }

  close() {
    this.querySelector('.alert')?.classList.add('out')

    window.setTimeout(async () => {
      this.parentElement?.removeChild(this)
      this.dispatchEvent(new CustomEvent('close'))
    }, 500)
  }
}

export function flash (title: string, description: string, type = 'success', duration = 3) {
  const alert = document.createElement('alert-message')

  if (duration) {
    alert.setAttribute('duration', String(duration))
  }

  alert.setAttribute('type', type as string)
  alert.setAttribute('title', title)
  alert.setAttribute('description', description)
  document.body.appendChild(alert)
}

export function flashAccessDenied() {
  flash('Accès refusé', "Vous ne pouvez pas effectuer cette action.", 'error')
}
