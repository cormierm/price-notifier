<template>
    <div :class="changeClass">
        {{ changePercentage }}%
        <div
            v-if="changeAmount"
            class="change-amount"
        >
            ({{ (changeAmount).toFixed(2) }})
        </div>
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
    font-size: 0.8em;
}

.font-red {
    color: red;
}

.font-green {
    color: green;
}

</style>
