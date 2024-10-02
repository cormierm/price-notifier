<template>
    <div class="text-xs" :class="changeClass">
        <div v-if="showChange">{{ changePercentage }}%</div>
        <div v-else>-</div>
    </div>
</template>
<script>
export default {
  name: 'change-column',
  props: {
      initialValue: {
          type: String,
          default: null
      },
      currentValue: {
          type: String,
          default: null
      }
  },
    computed: {
        changeClass() {
            if (this.changePercentage > 0) {
                return 'text-red-500';
            }
            if (this.changePercentage < 0) {
                return 'text-green-500';
            }
            return '';
        },
        changePercentage() {
            return ((this.initialValue - this.currentValue) / this.initialValue * 100).toFixed(1) * -1;
        },
        showChange() {
            return this.currentValue && this.initialValue && this.currentValue !== this.initialValue;
        }
    }
}
</script>
