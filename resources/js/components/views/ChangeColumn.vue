<template>
    <div class="change-column" :class="changeClass">
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
                return 'font-red';
            }
            if (this.changePercentage < 0) {
                return 'font-green';
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
<style scoped>
.change-column {
    font-size: 0.9em;
}

.font-red {
    color: red;
}

.font-green {
    color: green;
}

</style>
