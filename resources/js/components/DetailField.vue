<script>
import { normalizeItems } from '../support/items.js'

export default {
    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    computed: {
        items() {
            return normalizeItems(this.field.value)
        },

        count() {
            return this.items.length
        },

        isEmpty() {
            return this.count === 0
        },
    },
}
</script>

<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <span v-if="isEmpty" class="nif-muted">&mdash;</span>

            <span v-else-if="field.detailsAsTotal" class="nif-count-badge">{{ count }}</span>

            <div v-else-if="field.chips" class="nif-chips">
                <span v-for="(item, i) in items" :key="i" class="nif-chip nif-chip--readonly">
                    {{ item }}
                </span>
            </div>

            <ol v-else class="nif-detail-list">
                <li v-for="(item, i) in items" :key="i">{{ item }}</li>
            </ol>
        </template>
    </PanelItem>
</template>
