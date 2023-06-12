export function debounce(func: () => unknown, wait: number, immediate: boolean = false) {
  let timeout: number | undefined | null
  return (...args: []) => {
    if(timeout) {
      clearTimeout(timeout)
    }
    timeout = setTimeout(() => {
      timeout = null
      if (!immediate) {
        // @ts-expect-error
        func.apply(this, args)
      }
    }, wait)
    if (immediate && !timeout) {
      // @ts-expect-error
      func.apply(this, args)
    }
  }
}
