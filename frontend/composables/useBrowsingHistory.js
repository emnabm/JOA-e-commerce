export const useBrowsingHistory = () => {
    const KEY = 'browsing_history'
    const MAX = 20

    const addToHistory = (productId) => {
        const id = parseInt(productId)
        const history = getHistory()
        const updated = [id, ...history.filter(i => i !== id)].slice(0, MAX)
        localStorage.setItem(KEY, JSON.stringify(updated))
    }

    const getHistory = () => {
        try {
            return JSON.parse(localStorage.getItem(KEY) || '[]')
        } catch {
            return []
        }
    }

    return { addToHistory, getHistory }
}