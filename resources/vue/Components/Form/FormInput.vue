<template>
    <div class="flex flex-col">
        <label class="flex flex-col">
            {{ label }}
            <slot></slot>
            <input
                class="rounded"
                :class="{'border-red-500': errors, 'bg-gray-100 border-gray-100 text-gray-700': disabled}"
                :type="type"
                :placeholder="placeholder"
                :value="value"
                :disabled="disabled"
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
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
}
</script>
