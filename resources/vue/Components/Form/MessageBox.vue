<template>
    <div>
        <div :class="['font-bold text-white px-4 py-2 rounded-t', colorClasses.header]">
            <h4>{{ title }}</h4>
        </div>
        <div :class="['p-4 rounded-b', colorClasses.body]">
            <slot/>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        colour: {
            type: String,
            default: 'green',
            validator(value) {
                return ['green', 'red', 'blue', 'black'].includes(value);
            }
        }
    },
    computed: {
        colorClasses() {
            const colorMap = {
                green: {
                    header: 'bg-green-400',
                    body: 'text-green-700 bg-green-100',
                },
                red: {
                    header: 'bg-red-400',
                    body: 'text-red-700 bg-red-100',
                },
                blue: {
                    header: 'bg-blue-400',
                    body: 'text-blue-700 bg-blue-100',
                },
                black: {
                    header: 'bg-gray-700',
                    body: 'text-gray-700 bg-gray-100',
                },
            };
            return colorMap[this.colour] || colorMap.green;
        }
    }
}
</script>
