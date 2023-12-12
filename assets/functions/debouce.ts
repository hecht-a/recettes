export function debounce<T extends CallableFunction>(func: T, wait: number, immediate: boolean = false): T {
  let timeout: NodeJS.Timeout | null
  return ((...args: []) => {
    if (timeout) {
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
  }) as unknown as T
}
