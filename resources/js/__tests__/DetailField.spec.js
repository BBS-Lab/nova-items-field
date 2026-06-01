import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'
import DetailField from '../components/DetailField.vue'

const PanelItemStub = {
    props: ['index', 'field'],
    template: '<div class="panel-item"><slot name="value" /></div>',
}

const mountDetail = (field) =>
    mount(DetailField, {
        props: { field, index: 0 },
        global: { stubs: { PanelItem: PanelItemStub } },
    })

describe('DetailField', () => {
    it('shows a dash when empty', () => {
        expect(mountDetail({ value: [] }).find('.nif-muted').exists()).toBe(true)
    })

    it('shows the count when detailsAsTotal', () => {
        expect(
            mountDetail({ value: ['a', 'b'], detailsAsTotal: true })
                .find('.nif-count-badge')
                .text(),
        ).toBe('2')
    })

    it('shows readonly chips when chips mode', () => {
        expect(mountDetail({ value: ['a', 'b'], chips: true }).findAll('.nif-chip--readonly')).toHaveLength(2)
    })

    it('shows an ordered list by default', () => {
        const items = mountDetail({ value: ['a', 'b', 'c'] }).findAll('.nif-detail-list li')

        expect(items).toHaveLength(3)
        expect(items[0].text()).toBe('a')
    })
})
