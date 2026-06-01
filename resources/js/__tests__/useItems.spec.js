import { describe, expect, it } from 'vitest'
import { useItems } from '../composables/useItems.js'

describe('useItems', () => {
    it('initializes from a JSON-encoded value', () => {
        const field = useItems({ value: '["a","b"]' })

        expect(field.items.value).toEqual(['a', 'b'])
        expect(field.count.value).toBe(2)
        expect(field.isEmpty.value).toBe(false)
    })

    it('starts empty for a nullish value', () => {
        const field = useItems({ value: null })

        expect(field.items.value).toEqual([])
        expect(field.isEmpty.value).toBe(true)
    })

    it('adds a trimmed string item', () => {
        const field = useItems()

        expect(field.addItem('  hello  ')).toBe(true)
        expect(field.items.value).toEqual(['hello'])
    })

    it('ignores blank additions', () => {
        const field = useItems()

        expect(field.addItem('   ')).toBe(false)
        expect(field.addItem('')).toBe(false)
        expect(field.items.value).toEqual([])
    })

    it('adds non-string values unchanged', () => {
        const field = useItems()

        expect(field.addItem(42)).toBe(true)
        expect(field.items.value).toEqual([42])
    })

    it('respects the maximum and reports maxReached', () => {
        const field = useItems({ value: ['a'], max: 2 })

        expect(field.maxReached.value).toBe(false)
        field.addItem('b')
        expect(field.maxReached.value).toBe(true)
        expect(field.addItem('c')).toBe(false)
        expect(field.items.value).toEqual(['a', 'b'])
    })

    it('never reaches a max when none is set', () => {
        const field = useItems({ value: ['a', 'b', 'c'] })

        expect(field.maxReached.value).toBe(false)
    })

    it('commits the draft and clears it', () => {
        const field = useItems()
        field.draft.value = 'x'

        expect(field.commitDraft()).toBe(true)
        expect(field.items.value).toEqual(['x'])
        expect(field.draft.value).toBe('')
    })

    it('keeps the draft when the addition is rejected', () => {
        const field = useItems({ value: ['a'], max: 1 })
        field.draft.value = 'x'

        expect(field.commitDraft()).toBe(false)
        expect(field.draft.value).toBe('x')
    })

    it('updates an item by index', () => {
        const field = useItems({ value: ['a', 'b'] })

        field.updateItem(1, 'B')

        expect(field.items.value).toEqual(['a', 'B'])
    })

    it('ignores updates for out-of-range indexes', () => {
        const field = useItems({ value: ['a'] })

        field.updateItem(5, 'x')
        field.updateItem(-1, 'y')

        expect(field.items.value).toEqual(['a'])
    })

    it('removes an item by index', () => {
        const field = useItems({ value: ['a', 'b', 'c'] })

        field.removeItem(1)

        expect(field.items.value).toEqual(['a', 'c'])
    })

    it('serializes the items to JSON reactively', () => {
        const field = useItems({ value: ['a'] })

        expect(field.serialized.value).toBe('["a"]')
        field.addItem('b')
        expect(field.serialized.value).toBe('["a","b"]')
    })

    it('reports belowMin against the minimum', () => {
        const field = useItems({ value: ['a'], min: 2 })

        expect(field.belowMin.value).toBe(true)
        field.addItem('b')
        expect(field.belowMin.value).toBe(false)
    })

    it('is never belowMin when no minimum is set', () => {
        expect(useItems({ value: [] }).belowMin.value).toBe(false)
    })

    it('moves an item within the list', () => {
        const field = useItems({ value: ['a', 'b', 'c'] })

        field.moveItem(0, 1)
        expect(field.items.value).toEqual(['b', 'a', 'c'])

        field.moveItem(2, -1)
        expect(field.items.value).toEqual(['b', 'c', 'a'])
    })

    it('ignores out-of-range moves', () => {
        const field = useItems({ value: ['a', 'b'] })

        field.moveItem(0, -1)
        field.moveItem(1, 1)
        field.moveItem(5, -1)

        expect(field.items.value).toEqual(['a', 'b'])
    })

    it('replaces the whole list with setItems', () => {
        const field = useItems({ value: ['a'] })

        field.setItems(['x', 'y'])

        expect(field.items.value).toEqual(['x', 'y'])
    })
})
