<script>
import draggable from 'vuedraggable'
import { useItems } from '../composables/useItems.js'
import { parseItemErrors } from '../support/items.js'

export default {
    components: { draggable },

    mixins: [window.LaravelNova.DependentFormField, window.LaravelNova.HandlesValidationErrors],

    setup(props) {
        return useItems({
            value: props.field.value,
            min: props.field.min,
            max: props.field.max,
        })
    },

    computed: {
        labels() {
            return this.currentField.labels ?? {}
        },

        suggestionsList() {
            return this.currentField.suggestions ?? []
        },

        hasSuggestions() {
            return this.suggestionsList.length > 0
        },

        suggestionsId() {
            return `nif-suggestions-${this.currentField.attribute}`
        },

        canAdd() {
            return !this.currentField.hideAddButton && !this.maxReached
        },

        parsedErrors() {
            const key = this.currentField.validationKey ?? this.currentField.attribute
            const messages = typeof this.errors?.get === 'function' ? this.errors.get(key) : []

            return parseItemErrors(messages)
        },

        fieldLevelErrors() {
            return this.parsedErrors.field
        },

        listStyle() {
            return this.currentField.maxHeight
                ? { maxHeight: `${this.currentField.maxHeight}px`, overflowY: 'auto' }
                : null
        },
    },

    methods: {
        /**
         * Re-hydrate the items when Nova (re)syncs the field.
         */
        setInitialValue() {
            this.setItems(this.currentField.value)
        },

        /**
         * Provide the field value to the submitted form data (only when visible).
         */
        fill(formData) {
            this.fillIfVisible(formData, this.fieldAttribute, this.isEmpty ? '' : this.serialized)
        },

        rowError(index) {
            const messages = this.parsedErrors.items[index]

            return Array.isArray(messages) && messages.length > 0 ? messages[0] : null
        },

        /**
         * Notify Nova that the value changed so fields depending on this one re-resolve.
         */
        emitChange() {
            this.emitFieldValueChange(this.fieldAttribute, this.serialized)
        },

        onInput(index, event) {
            this.updateItem(index, event.target.value)
            this.emitChange()
        },

        onAdd() {
            this.commitDraft()
            this.emitChange()
        },

        onRemove(index) {
            this.removeItem(index)
            this.emitChange()
        },

        onReorder() {
            this.emitChange()
        },

        move(index, delta) {
            this.moveItem(index, delta)
            this.emitChange()
        },
    },
}
</script>

<template>
    <DefaultField
        :field="currentField"
        :errors="errors"
        :show-errors="false"
        :full-width-content="currentField.fullWidth"
    >
        <template #field>
            <div class="nif" :class="{ 'nif--add-top': currentField.addButtonPosition === 'top' }">
                <!-- Chips mode -->
                <div v-if="currentField.chips" class="nif-chipbox" :style="listStyle">
                    <span v-for="(item, index) in items" :key="index" class="nif-chip">
                        <span class="nif-chip-label">{{ item }}</span>
                        <button
                            v-if="!currentField.readonly"
                            type="button"
                            class="nif-chip-x"
                            :aria-label="labels.remove"
                            @click="onRemove(index)"
                        >
                            &times;
                        </button>
                    </span>
                    <span v-if="isEmpty" class="nif-empty-inline">{{ labels.empty }}</span>
                </div>

                <!-- Structured rows -->
                <template v-else>
                    <p v-if="isEmpty" class="nif-empty">{{ labels.empty }}</p>

                    <draggable
                        v-else
                        v-model="entries"
                        item-key="id"
                        handle=".nif-handle"
                        :disabled="!currentField.draggable || currentField.readonly"
                        class="nif-rows"
                        :style="listStyle"
                        @change="onReorder"
                    >
                        <template #item="{ element, index }">
                            <div class="nif-row" :class="{ 'nif-row--error': rowError(index) }">
                                <span class="nif-num">{{ index + 1 }}</span>
                                <button
                                    v-if="currentField.draggable && !currentField.readonly"
                                    type="button"
                                    class="nif-handle"
                                    :aria-label="`${currentField.name} ${index + 1}`"
                                    @keydown.up.prevent="move(index, -1)"
                                    @keydown.down.prevent="move(index, 1)"
                                >
                                    &#x2807;
                                </button>
                                <div class="nif-input-wrap">
                                    <input
                                        class="nif-input form-control form-input form-control-bordered w-full"
                                        :type="currentField.inputType"
                                        :value="element.value"
                                        :readonly="currentField.readonly"
                                        :aria-label="`${currentField.name} ${index + 1}`"
                                        :list="hasSuggestions ? suggestionsId : null"
                                        @input="onInput(index, $event)"
                                    />
                                </div>
                                <button
                                    v-if="!currentField.readonly"
                                    type="button"
                                    class="nif-x"
                                    :aria-label="labels.remove"
                                    @click="onRemove(index)"
                                >
                                    &times;
                                </button>
                                <p v-if="rowError(index)" class="nif-row-error help-text-error">
                                    {{ rowError(index) }}
                                </p>
                            </div>
                        </template>
                    </draggable>
                </template>

                <!-- Add control -->
                <div v-if="canAdd && !currentField.readonly" class="nif-add">
                    <div class="nif-input-wrap">
                        <input
                            v-model="draft"
                            class="nif-add-input form-control form-input form-control-bordered w-full"
                            :type="currentField.inputType"
                            :placeholder="labels.placeholder"
                            :aria-label="labels.add"
                            :list="hasSuggestions ? suggestionsId : null"
                            @keydown.enter.prevent="onAdd"
                        />
                    </div>
                    <button type="button" class="nif-add-btn" @click="onAdd">
                        {{ labels.add }}
                    </button>
                </div>

                <datalist v-if="hasSuggestions" :id="suggestionsId">
                    <option v-for="(suggestion, index) in suggestionsList" :key="index" :value="suggestion" />
                </datalist>

                <p
                    v-for="(error, index) in fieldLevelErrors"
                    :key="index"
                    class="nif-field-error help-text-error"
                >
                    {{ error }}
                </p>
            </div>
        </template>
    </DefaultField>
</template>
