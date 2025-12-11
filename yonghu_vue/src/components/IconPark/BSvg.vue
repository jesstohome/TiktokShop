<template>
  <svg class="bi-svg" :style="{ width: size, height: size }" aria-hidden="true">
    <use :xlink:href="`#${name}`"></use>
  </svg>
</template>
<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'BiSvg',
  props: {
    name: {
      type: String,
      default: ''
    },
    size: {
      type: [String, Number],
      required: false,
      default: '1rem'
    }
  }
})
</script>
<style type="text/css" scoped>
/* .bi-svg {
  width: 1em;
  height: 1em;
  vertical-align: -0.15em;
  fill: currentColor;
  overflow: hidden;
} */
</style>
