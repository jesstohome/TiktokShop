<template>
    <i class="bi-icon">
      <slot></slot>
    </i>
  </template>
<script>
import { defineComponent, computed } from "vue"
export default defineComponent({
name: "BiIcon",
props: {
    size: {
      type: [String, Number],
      required:false,
      default: 12,
    },
    color: {
      type: String,
      required:false,
      default:''
    },
},
  setup(props) {
      const localSize = computed(() => {
      if (typeof props.size === "number") {
          return `${props.size}px`;
      }
      if (/^\d*$/.test(props.size)) {
          return `${props.size}px`;
      }
      return props.size;
      });
      return { localSize };
  },
});
</script>
<style type="text/css" scoped>
.bi-icon {
  color: v-bind(color);
  font-size: v-bind(localSize);
}
</style>