<template>
  <div class="input-container">
    <div v-if="label" class="input-label">
      <span v-if="required" style="color:red;">*</span>
      {{ label }}
    </div>
    <div class="input-field">
      <slot name="left"></slot>
      <div class="field-column">
        <van-field
          v-model.trim="_value"
          :required="required"
          :rules="rules"
          :readonly="readonly"
          :disabled="disabled"
          :type="type"
          :autosize="type === 'textarea'"
          :placeholder="placeholder"
          :center="center"
          @click="handleClick"
          :autocomplete="autoComplete"
        </van-field>
      </div>
      <slot name="right" />
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  type: {
    type: String,
    required: false,
    default: 'text'
  },
  label: {
    type: String,
    required: false,
    default: ''
  },
  value: {
    type: [String, Number],
    required: false,
    default: undefined
  },
  rules: {
    type: Array,
    required: false,
    default: () => {
      return undefined
    }
  },
  disabled: {
    type: Boolean,
    required: false,
    default: false
  },
  required: {
    type: Boolean,
    required: false,
    default: false
  },
  readonly: {
    type: Boolean,
    required: false,
    default: false
  },
  placeholder: {
    type: String,
    required: false,
    default: undefined
  },
  center: {
    type: Boolean,
    required: false,
    default: true
  },
  autoComplete:{
    type: String,
    required: false,
    default: undefined
  }
})

const emit = defineEmits(['blur', 'click'])
const _value = computed({
  get() {
    return props.value
  },
  set(val) {
    emit('blur', val)
  }
})
const handleClick = () => {
  emit('click')
}
</script>
<style lang="scss" scoped>
.input-container {
  display: flex;
  flex-direction: column;
  .input-label {
    padding: 1em 0;
    font-size: 1em;
    font-weight: 600;
    letter-spacing: 0.07px;
    color: rgba(120, 130, 138, 1);
  }
  .input-field {
    padding: 0 0.75rem;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    // background: rgba(246, 248, 254, 1);
    background: #ffffff;
    border-radius: 1rem;
    .field-column {
      flex: 1;
      border-radius: 24px;
      ::v-deep(.van-cell) {
        padding: 0;
        border-radius: 24px;
      }
      ::v-deep(.van-field__control) {
        padding: 10px;
        background: #ffffff;
      }
    }
  }
}
</style>
