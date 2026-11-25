const keys = { cafe: 'wadah-bookmarks', roastery: 'wadah-roastery-bookmarks' }

export function localBookmarks(kind) {
    try {
        const saved = JSON.parse(localStorage.getItem(keys[kind]) || '[]')
        return Array.isArray(saved) ? [...new Set(saved.map(Number).filter(id => Number.isSafeInteger(id) && id > 0))].slice(0, 50) : []
    } catch {
        return []
    }
}

export function setLocalBookmark(kind, id, saved) {
    const items = localBookmarks(kind).filter(item => item !== id)
    if (saved) {
        if (items.length >= 50) throw new Error('Daftar simpanan sudah penuh.')
        items.push(id)
    }
    localStorage.setItem(keys[kind], JSON.stringify(items))
}
