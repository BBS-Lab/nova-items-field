<script>
import { chipOverflow, normalizeItems, truncateList } from '../support/items.js'

export default {
    props: ['field'],

    computed: {
        items() {
            return normalizeItems(this.field.value)
        },

        count() {
            return this.items.length
        },

        joined() {
            return this.items.join(', ')
        },

        overflow() {
            return chipOverflow(this.items, 3)
        },

        truncated() {
            return truncateList(this.items, 40)
        },
    },
}
</script>

<template>
    <span v-if="field.indexAsChips" class="nif-index-chips">
        <span v-for="(item, index) in overflow.shown" :key="index" class="nif-chip nif-chip--readonly">
            {{ item }}
        </span>
        <span v-if="overflow.more" class="nif-more">+{{ overflow.more }}</span>
        <span v-if="count === 0" class="nif-muted">&mdash;</span>
    </span>

    <span v-else-if="field.indexAsList" class="nif-muted">{{ truncated || '—' }}</span>

    <span v-else v-tooltip="joined" class="nif-count-badge">{{ count }}</span>
</template>
