import { beforeEach, describe, expect, it, vi } from 'vitest'

describe('field entry', () => {
    beforeEach(() => {
        vi.resetModules()
    })

    it('registers the three field components when Nova boots', async () => {
        const app = { component: vi.fn() }
        window.Nova.booting = vi.fn((callback) => callback(app))

        await import('../field.js')

        const registered = app.component.mock.calls.map((call) => call[0])

        expect(window.Nova.booting).toHaveBeenCalledOnce()
        expect(registered).toEqual([
            'index-nova-items-field',
            'detail-nova-items-field',
            'form-nova-items-field',
        ])
    })
})
