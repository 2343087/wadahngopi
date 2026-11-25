import { ref } from 'vue'
import { api } from '../api'
import { localBookmarks, setLocalBookmark } from '../bookmarks'

const savedCafes = ref(new Set(localBookmarks('cafe')))
const savedRoasteries = ref(new Set(localBookmarks('roastery')))
const pendingKeys = ref(new Set())
const accountLoaded = ref(false)
let accountPromise = null

function keyFor(kind, id) {
    return `${kind}:${id}`
}

function setFor(kind) {
    return kind === 'roastery' ? savedRoasteries : savedCafes
}

function applySaved(kind, id, nextSaved) {
    const next = new Set(setFor(kind).value)
    if (nextSaved) next.add(id)
    else next.delete(id)
    setFor(kind).value = next
}

/**
 * Shared bookmark state synchronized across VenueCard, Detail, and Saved views.
 */
export function useBookmarks() {
    async function ensureAccountBookmarks(signedIn) {
        if (!signedIn || accountLoaded.value) return
        if (!accountPromise) {
            accountPromise = api('/api/bookmarks')
                .then((data) => {
                    savedCafes.value = new Set([...localBookmarks('cafe'), ...(data?.cafes || [])])
                    savedRoasteries.value = new Set([...localBookmarks('roastery'), ...(data?.roasteries || [])])
                    accountLoaded.value = true
                })
                .catch(() => {})
                .finally(() => {
                    accountPromise = null
                })
        }
        await accountPromise
    }

    function isSaved(kind, id) {
        return setFor(kind).value.has(id)
    }

    function isBookmarkBusy(kind, id) {
        return pendingKeys.value.has(keyFor(kind, id))
    }

    async function toggleBookmark(kind, id, signedIn) {
        const key = keyFor(kind, id)
        if (pendingKeys.value.has(key)) return { saved: isSaved(kind, id) }

        const nextPending = new Set(pendingKeys.value)
        nextPending.add(key)
        pendingKeys.value = nextPending

        try {
            const currentlySaved = isSaved(kind, id)
            let nextSaved
            if (signedIn) {
                const result = await api('/api/bookmarks/toggle', {
                    method: 'POST',
                    body: { bookmarkable_id: id, bookmarkable_type: kind, saved: !currentlySaved },
                })
                nextSaved = result.action === 'added'
                setLocalBookmark(kind, id, false)
            } else {
                nextSaved = !currentlySaved
                setLocalBookmark(kind, id, nextSaved)
            }
            applySaved(kind, id, nextSaved)
            return { saved: nextSaved }
        } finally {
            const cleaned = new Set(pendingKeys.value)
            cleaned.delete(key)
            pendingKeys.value = cleaned
        }
    }

    async function syncGuestBookmarks(signedIn) {
        if (!signedIn) return
        const cafes = localBookmarks('cafe')
        const roasteries = localBookmarks('roastery')
        if (!cafes.length && !roasteries.length) {
            await ensureAccountBookmarks(true)
            return
        }

        await api('/api/bookmarks/sync', { method: 'POST', body: { cafes, roasteries } })
        const account = await api('/api/bookmarks')
        for (const id of cafes.filter(id => account.cafes.includes(id))) setLocalBookmark('cafe', id, false)
        for (const id of roasteries.filter(id => account.roasteries.includes(id))) setLocalBookmark('roastery', id, false)
        savedCafes.value = new Set([...localBookmarks('cafe'), ...(account?.cafes || [])])
        savedRoasteries.value = new Set([...localBookmarks('roastery'), ...(account?.roasteries || [])])
        accountLoaded.value = true
    }

    return {
        savedCafes,
        savedRoasteries,
        ensureAccountBookmarks,
        isSaved,
        isBookmarkBusy,
        toggleBookmark,
        syncGuestBookmarks,
    }
}
