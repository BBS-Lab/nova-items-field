import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'
import IndexField from '../components/IndexField.vue'

const mountIndex = (field) => mount(IndexField, { props: { field } })

describe('IndexField', () => {
    it('shows a count badge with a hover tooltip by default', () => {
        const wrapper = mountIndex({ value: ['a', 'b', 'c'] })

        expect(wrapper.find('.nif-count-badge').text()).toBe('3')
        expect(wrapper.vm.joined).toBe('a, b, c')
    })

    it('shows a zero count badge for an empty list', () => {
        expect(mountIndex({ value: [] }).find('.nif-count-badge').text()).toBe('0')
    })

    it('shows chips with an overflow counter when indexAsChips', () => {
        const wrapper = mountIndex({ value: ['a', 'b', 'c', 'd', 'e'], indexAsChips: true })

        expect(wrapper.findAll('.nif-chip--readonly')).toHaveLength(3)
        expect(wrapper.find('.nif-more').text()).toBe('+2')
        expect(wrapper.find('.nif-muted').exists()).toBe(false)
    })

    it('shows a dash for an empty chips index', () => {
        const wrapper = mountIndex({ value: [], indexAsChips: true })

        expect(wrapper.find('.nif-more').exists()).toBe(false)
        expect(wrapper.find('.nif-muted').exists()).toBe(true)
    })

    it('shows truncated text when indexAsList', () => {
        expect(
            mountIndex({ value: ['alpha', 'beta'], indexAsList: true })
                .find('.nif-muted')
                .text(),
        ).toBe('alpha, beta')
    })

    it('shows a dash when indexAsList is empty', () => {
        expect(mountIndex({ value: [], indexAsList: true }).find('.nif-muted').text()).toBe('—')
    })
})
