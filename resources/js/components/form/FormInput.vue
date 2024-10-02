<template>
    <div class="flex flex-col">
        <label class="flex flex-col">
            {{ label }}
            <slot></slot>
            <input
                class="rounded"
                :class="{'border-red-500': errors}"
                :type="type"
                :placeholder="placeholder"
                :value="value"
                @input="$emit('input', $event.target.value)"
            ></input>
        </label>
        <div v-if="errors" class="text-sm text-red-500">{{ errors[0] }}</div>
    </div>
</template>

<script>
export default {
    props: {
        label: {
            type: String,
            required: false
        },
        type: {
            type: String,
            default: 'text'
        },
        placeholder: {
            type: String,
            default: ''
        },
        errors: {
            type: Array,
            default: null
        },
        value: {
            type: [String, Number],
            required: true
        }
    },
}
</script>
