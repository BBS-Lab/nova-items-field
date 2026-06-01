import { config } from '@vue/test-utils'
import { vi } from 'vitest'

// Nova registers the floating-vue `v-tooltip` directive globally; provide a
// no-op so components using it can be mounted in isolation.
config.global.directives = { tooltip: {} }

// Functional mocks of the Nova field mixins exposed on `window.LaravelNova`,
// providing the slice of contract our components rely on so they can be mounted
// in isolation (props, currentField/visibility, fill helpers).
window.Nova = {
    booting: vi.fn(),
    __: (key) => key,
}

window.LaravelNova = {
    DependentFormField: {
        props: ['field', 'resourceName', 'resourceId', 'mode'],
        computed: {
            currentField() {
                return this.field
            },
            currentlyIsVisible() {
                return this.field?.visible !== false
            },
            fieldAttribute() {
                return this.field?.attribute
            },
        },
        methods: {
            fillIfVisible(formData, attribute, value) {
                if (this.currentlyIsVisible) {
                    formData.append(attribute, value)
                }
            },
            emitFieldValueChange() {},
            handleChange() {},
        },
    },
    HandlesValidationErrors: {
        props: ['errors'],
    },
}
