/**
 * Coerce any incoming field value into a plain array of items.
 *
 * Accepts an array (cloned), a JSON-encoded array string, or a nullish/empty
 * value. Anything else (invalid JSON, non-array JSON) becomes an empty array.
 *
 * @param {*} value
 * @returns {Array}
 */
export function normalizeItems(value) {
    if (Array.isArray(value)) {
        return [...value]
    }

    if (value === null || value === undefined || value === '') {
        return []
    }

    try {
        const decoded = JSON.parse(value)

        return Array.isArray(decoded) ? decoded : []
    } catch {
        return []
    }
}

/**
 * Decode the structured validation payload produced by the ArrayItemsRule into
 * field-level messages and per-index item messages.
 *
 * @param {Array<string>} messages
 * @returns {{ field: Array<string>, items: Object<string, Array<string>> }}
 */
export function parseItemErrors(messages) {
    const result = { field: [], items: {} }

    if (!Array.isArray(messages)) {
        return result
    }

    for (const message of messages) {
        const payload = tryParse(message)

        if (payload && typeof payload === 'object' && !Array.isArray(payload)) {
            if (Array.isArray(payload.field)) {
                result.field.push(...payload.field)
            }

            if (payload.items && typeof payload.items === 'object') {
                Object.assign(result.items, payload.items)
            }

            continue
        }

        result.field.push(message)
    }

    return result
}

/**
 * Join the items into a comma-separated string, truncated to a max length.
 *
 * @param {Array} items
 * @param {number} limit
 * @returns {string}
 */
export function truncateList(items, limit = 40) {
    const text = (items ?? []).join(', ')

    return text.length > limit ? `${text.slice(0, limit).trimEnd()}…` : text
}

/**
 * Split a list into the first `visible` items and an overflow count.
 *
 * @param {Array} items
 * @param {number} visible
 * @returns {{ shown: Array, more: number }}
 */
export function chipOverflow(items, visible = 2) {
    const list = items ?? []

    return {
        shown: list.slice(0, visible),
        more: Math.max(0, list.length - visible),
    }
}

/**
 * @param {string} value
 * @returns {*}
 */
function tryParse(value) {
    try {
        return JSON.parse(value)
    } catch {
        return null
    }
}
