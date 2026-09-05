import { useEffect, useState, type DependencyList } from 'react'

/**
 * Fetches data from the backend on mount, tracking a loading flag so
 * callers can render a skeleton instead of an empty section while the
 * request is in flight.
 */
export function useApiData<T>(
  fetcher: () => Promise<T>,
  initial: T,
  deps: DependencyList = [],
): { data: T; isLoading: boolean } {
  const [data, setData] = useState<T>(initial)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    let active = true
    setIsLoading(true)

    fetcher()
      .then((result) => {
        if (active) setData(result)
      })
      .catch(() => {
        if (active) setData(initial)
      })
      .finally(() => {
        if (active) setIsLoading(false)
      })

    return () => {
      active = false
    }
  }, deps)

  return { data, isLoading }
}
