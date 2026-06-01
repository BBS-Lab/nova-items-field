import { mount } from '@vue/test-utils'
import { describe, expect, it, vi } from 'vitest'
import FormField from '../components/FormField.vue'

const DefaultFieldStub = {
    template: '<div class="default-field"><slot name="field" /></div>',
}

const DraggableStub = {
    props: { modelValue: { type: Array, default: () => [] } },
    emits: ['update:modelValue', 'change'],
    template: `
        <div class="draggable">
            <template v-for="(element, index) in modelValue" :key="element.id">
                <slot name="item" :element="element" :index="index" />
            </template>
        </div>
    `,
}

function makeField(overrides = {}) {
    return {
        attribute: 'tags',
        name: 'Tags',
        value: [],
        chips: false,
        draggable: false,
        readonly: false,
        inputType: 'text',
        addButtonPosition: 'bottom',
        hideAddButton: false,
        suggestions: [],
        min: null,
        max: null,
        labels: {
            add: 'Add',
            addFirst: 'Add first',
            remove: 'Remove',
            empty: 'No items yet.',
            placeholder: 'Add an item…',
        },
        ...overrides,
    }
}

function mountField(fieldOverrides = {}, props = {}) {
    return mount(FormField, {
        props: {
            field: makeField(fieldOverrides),
            errors: { get: () => [] },
            resourceName: 'posts',
            resourceId: 1,
            mode: 'form',
            ...props,
        },
        global: {
            stubs: { DefaultField: DefaultFieldStub, draggable: DraggableStub },
        },
    })
}

describe('FormField', () => {
    it('renders the empty state in rows mode', () => {
        const wrapper = mountField({ value: [] })

        expect(wrapper.find('.nif-empty').text()).toBe('No items yet.')
    })

    it('renders one input per item in rows mode', () => {
        const wrapper = mountField({ value: ['a', 'b'] })

        const inputs = wrapper.findAll('.nif-input')
        expect(inputs).toHaveLength(2)
        expect(inputs[0].element.value).toBe('a')
    })

    it('adds an item through the add button', async () => {
        const wrapper = mountField({ value: [] })

        await wrapper.find('.nif-add-input').setValue('hello')
        await wrapper.find('.nif-add-btn').trigger('click')

        expect(wrapper.vm.items).toEqual(['hello'])
    })

    it('adds an item when pressing Enter', async () => {
        const wrapper = mountField({ value: [] })

        await wrapper.find('.nif-add-input').setValue('typed')
        await wrapper.find('.nif-add-input').trigger('keydown.enter')

        expect(wrapper.vm.items).toEqual(['typed'])
    })

    it('updates an item when its input changes', async () => {
        const wrapper = mountField({ value: ['a', 'b'] })

        await wrapper.findAll('.nif-input')[1].setValue('B')

        expect(wrapper.vm.items).toEqual(['a', 'B'])
    })

    it('removes an item through its delete button', async () => {
        const wrapper = mountField({ value: ['a', 'b', 'c'] })

        await wrapper.findAll('.nif-x')[1].trigger('click')

        expect(wrapper.vm.items).toEqual(['a', 'c'])
    })

    it('renders chips with remove buttons in chips mode', async () => {
        const wrapper = mountField({ value: ['a', 'b'], chips: true })

        expect(wrapper.findAll('.nif-chip')).toHaveLength(2)
        await wrapper.findAll('.nif-chip-x')[0].trigger('click')
        expect(wrapper.vm.items).toEqual(['b'])
    })

    it('renders the inline empty hint in chips mode', () => {
        expect(mountField({ value: [], chips: true }).find('.nif-empty-inline').exists()).toBe(true)
    })

    it('shows the drag handle only when draggable', () => {
        expect(
            mountField({ value: ['a'], draggable: true })
                .find('.nif-handle')
                .exists(),
        ).toBe(true)
        expect(
            mountField({ value: ['a'], draggable: false })
                .find('.nif-handle')
                .exists(),
        ).toBe(false)
    })

    it('applies a reordering emitted by the draggable and notifies', async () => {
        const wrapper = mountField({ value: ['a', 'b', 'c'], draggable: true })
        const spy = vi.spyOn(wrapper.vm, 'emitFieldValueChange')
        const current = wrapper.vm.entries
        const draggable = wrapper.findComponent(DraggableStub)

        await draggable.vm.$emit('update:modelValue', [current[2], current[0], current[1]])
        await draggable.vm.$emit('change')

        expect(wrapper.vm.items).toEqual(['c', 'a', 'b'])
        expect(spy).toHaveBeenCalled()
    })

    it('reorders items with the keyboard on the drag handle', async () => {
        const wrapper = mountField({ value: ['a', 'b', 'c'], draggable: true })

        await wrapper.findAll('.nif-handle')[0].trigger('keydown.down')

        expect(wrapper.vm.items).toEqual(['b', 'a', 'c'])
    })

    it('emits a field value change when an item is added', async () => {
        const wrapper = mountField({ value: [] })
        const spy = vi.spyOn(wrapper.vm, 'emitFieldValueChange')

        await wrapper.find('.nif-add-input').setValue('x')
        await wrapper.find('.nif-add-btn').trigger('click')

        expect(spy).toHaveBeenCalledWith('tags', '["x"]')
    })

    it('applies a scrollable max-height when configured', () => {
        const style = mountField({ value: ['a'], maxHeight: 300 })
            .find('.draggable')
            .attributes('style')

        expect(style).toContain('max-height: 300px')
    })

    it('gives each item input an accessible label', () => {
        expect(
            mountField({ value: ['a'] })
                .find('.nif-input')
                .attributes('aria-label'),
        ).toBe('Tags 1')
    })

    it('is non-interactive when readonly', () => {
        const wrapper = mountField({ value: ['a'], readonly: true })

        expect(wrapper.find('.nif-add').exists()).toBe(false)
        expect(wrapper.find('.nif-x').exists()).toBe(false)
        expect(wrapper.find('.nif-input').attributes('readonly')).toBeDefined()
    })

    it('hides the add control when hideAddButton is set', () => {
        expect(mountField({ value: [], hideAddButton: true }).find('.nif-add').exists()).toBe(false)
    })

    it('hides the add control when the max is reached', () => {
        expect(
            mountField({ value: ['a'], max: 1 })
                .find('.nif-add')
                .exists(),
        ).toBe(false)
    })

    it('renders a datalist for suggestions', () => {
        const wrapper = mountField({ value: [], suggestions: ['x', 'y'] })

        expect(wrapper.find('datalist').exists()).toBe(true)
        expect(wrapper.findAll('datalist option')).toHaveLength(2)
    })

    it('binds the suggestions datalist to row inputs', () => {
        const wrapper = mountField({ value: ['a'], suggestions: ['x', 'y'] })

        expect(wrapper.find('.nif-input').attributes('list')).toBe('nif-suggestions-tags')
    })

    it('reverses the layout when the add button is positioned on top', () => {
        expect(mountField({ addButtonPosition: 'top' }).find('.nif').classes()).toContain('nif--add-top')
    })

    it('fills the form data with the serialized items', () => {
        const wrapper = mountField({ value: ['a', 'b'] })
        const append = vi.fn()

        wrapper.vm.fill({ append })

        expect(append).toHaveBeenCalledWith('tags', '["a","b"]')
    })

    it('fills an empty string when there are no items', () => {
        const wrapper = mountField({ value: [] })
        const append = vi.fn()

        wrapper.vm.fill({ append })

        expect(append).toHaveBeenCalledWith('tags', '')
    })

    it('re-hydrates items via setInitialValue', async () => {
        const wrapper = mountField({ value: [] })

        await wrapper.setProps({ field: makeField({ value: ['x', 'y'] }) })
        wrapper.vm.setInitialValue()

        expect(wrapper.vm.items).toEqual(['x', 'y'])
    })

    it('renders field-level and per-row validation errors', () => {
        const payload = JSON.stringify({ field: ['Too few items'], items: { 1: ['Invalid email'] } })
        const wrapper = mountField({ value: ['a', 'b'] }, { errors: { get: () => [payload] } })

        expect(wrapper.find('.nif-field-error').text()).toBe('Too few items')
        expect(wrapper.find('.nif-row-error').text()).toBe('Invalid email')
    })

    it('handles an errors prop without a get() method', () => {
        const wrapper = mountField({ value: ['a'] }, { errors: {} })

        expect(wrapper.find('.nif-field-error').exists()).toBe(false)
    })

    it('uses the explicit validation key when present', () => {
        const get = vi.fn(() => [])
        mountField({ value: ['a'], validationKey: 'custom_key' }, { errors: { get } })

        expect(get).toHaveBeenCalledWith('custom_key')
    })

    it('tolerates a field without labels or suggestions', () => {
        const wrapper = mountField({ labels: undefined, suggestions: undefined })

        expect(wrapper.find('.nif').exists()).toBe(true)
        expect(wrapper.find('datalist').exists()).toBe(false)
    })
})
