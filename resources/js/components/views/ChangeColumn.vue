<template>
    <div class="change-amount" :class="changeClass">
        <div v-if="changeAmount">{{ changePercentage }}%</div>
        <div v-else>-</div>
    </div>
</template>
<script>
export default {
  name: 'change-column',
  props: {
      initialValue: {
          type: String,
          default: 0
      },
      currentValue: {
          type: String,
          default: 0,
      }
  },
    computed: {
        changeClass() {
            if (this.changeAmount > 0) {
                return 'font-red';
            }
            if (this.changeAmount < 0) {
                return 'font-green';
            }
            return '';
        },
        changePercentage() {
            return ((this.currentValue - this.initialValue) / this.currentValue * 100).toFixed(1);
        },
        changeAmount() {
            return this.currentValue - this.initialValue;
            ;
        }
    }
}
</script>
<style scoped>
.change-amount {
    font-size: 0.9em;
}

.font-red {
    color: red;
}

.font-green {
    color: green;
}

</style>
