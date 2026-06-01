import { describe, expect, it } from 'vitest'
import { chipOverflow, normalizeItems, parseItemErrors, truncateList } from '../support/items.js'

describe('normalizeItems', () => {
    it('returns an empty array for nullish or empty values', () => {
        expect(normalizeItems(null)).toEqual([])
        expect(normalizeItems(undefined)).toEqual([])
        expect(normalizeItems('')).toEqual([])
    })

    it('clones an array value', () => {
        const source = ['a', 'b']
        const result = normalizeItems(source)

        expect(result).toEqual(['a', 'b'])
        expect(result).not.toBe(source)
    })

    it('parses a JSON-encoded array string', () => {
        expect(normalizeItems('["a","b"]')).toEqual(['a', 'b'])
    })

    it('returns an empty array for invalid or non-array JSON', () => {
        expect(normalizeItems('not-json')).toEqual([])
        expect(normalizeItems('"a string"')).toEqual([])
        expect(normalizeItems('42')).toEqual([])
    })
})

describe('parseItemErrors', () => {
    it('returns an empty structure for a non-array input', () => {
        expect(parseItemErrors(null)).toEqual({ field: [], items: {} })
    })

    it('decodes the structured validation payload', () => {
        const messages = [JSON.stringify({ field: ['Too few'], items: { 1: ['Invalid'] } })]

        expect(parseItemErrors(messages)).toEqual({
            field: ['Too few'],
            items: { 1: ['Invalid'] },
        })
    })

    it('treats plain (non-JSON) messages as field-level errors', () => {
        expect(parseItemErrors(['Something went wrong'])).toEqual({
            field: ['Something went wrong'],
            items: {},
        })
    })

    it('ignores JSON payloads that are not objects', () => {
        expect(parseItemErrors(['42'])).toEqual({ field: ['42'], items: {} })
    })
})

describe('truncateList', () => {
    it('joins short lists untouched', () => {
        expect(truncateList(['a', 'b'], 40)).toBe('a, b')
    })

    it('truncates long lists with an ellipsis', () => {
        const result = truncateList(['alphabet', 'beta', 'gamma', 'delta'], 10)

        expect(result.endsWith('…')).toBe(true)
        expect(result.length).toBeLessThanOrEqual(11)
    })

    it('handles a nullish list', () => {
        expect(truncateList(null)).toBe('')
    })
})

describe('chipOverflow', () => {
    it('splits visible chips from the overflow count', () => {
        expect(chipOverflow(['a', 'b', 'c', 'd'], 2)).toEqual({ shown: ['a', 'b'], more: 2 })
    })

    it('reports no overflow when the list fits', () => {
        expect(chipOverflow(['a'], 2)).toEqual({ shown: ['a'], more: 0 })
    })

    it('handles a nullish list', () => {
        expect(chipOverflow(null, 2)).toEqual({ shown: [], more: 0 })
    })
})
