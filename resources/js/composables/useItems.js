import { computed, ref } from 'vue'
import { normalizeItems } from '../support/items.js'

/**
 * Reactive state and behavior for an Items field, kept free of any Nova
 * dependency so it can be unit-tested in isolation.
 *
 * Items are stored internally as `{ id, value }` entries so the list can be
 * reordered (drag & drop) without key collisions even when values repeat. The
 * public `items` computed always exposes the plain values.
 *
 * @param {{ value?: *, min?: number|null, max?: number|null }} options
 */
export function useItems(options = {}) {
    const { value = null, min = null, max = null } = options

    let sequence = 0
    const nextId = () => ++sequence

    const toEntries = (raw) => normalizeItems(raw).map((value) => ({ id: nextId(), value }))

    const entries = ref(toEntries(value))
    const draft = ref('')

    const items = computed(() => entries.value.map((entry) => entry.value))
    const count = computed(() => entries.value.length)
    const isEmpty = computed(() => count.value === 0)
    const maxReached = computed(() => max !== null && count.value >= max)
    const belowMin = computed(() => min !== null && count.value < min)
    const serialized = computed(() => JSON.stringify(items.value))

    /**
     * Append a value. Strings are trimmed and blanks rejected; other scalars
     * are stored as-is. Returns whether the item was added.
     */
    function addItem(value) {
        if (maxReached.value) {
            return false
        }

        const normalized = typeof value === 'string' ? value.trim() : value

        if (normalized === '' || normalized === null || normalized === undefined) {
            return false
        }

        entries.value.push({ id: nextId(), value: normalized })

        return true
    }

    /**
     * Add the current draft value, clearing it on success.
     */
    function commitDraft() {
        if (!addItem(draft.value)) {
            return false
        }

        draft.value = ''

        return true
    }

    function updateItem(index, value) {
        if (index < 0 || index >= entries.value.length) {
            return
        }

        entries.value[index].value = value
    }

    function removeItem(index) {
        entries.value.splice(index, 1)
    }

    /**
     * Move the item at `index` by `delta` positions (e.g. -1 up, +1 down).
     * Out-of-range moves are ignored.
     */
    function moveItem(index, delta) {
        const target = index + delta

        if (index < 0 || index >= entries.value.length || target < 0 || target >= entries.value.length) {
            return
        }

        const [moved] = entries.value.splice(index, 1)
        entries.value.splice(target, 0, moved)
    }

    function setItems(next) {
        entries.value = toEntries(next)
    }

    return {
        entries,
        items,
        draft,
        count,
        isEmpty,
        maxReached,
        belowMin,
        serialized,
        addItem,
        commitDraft,
        updateItem,
        removeItem,
        moveItem,
        setItems,
    }
}
