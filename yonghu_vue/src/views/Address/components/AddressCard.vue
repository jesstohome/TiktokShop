<template>
  <div class="address-card-container">
    <div class="tag" :class="isDefault ? 'is-default' : 'not-default'">
      {{ address.tag }}
    </div>
    <div class="info">
      <div class="contacts">
        <span class="user-name">{{ address.name }}</span>
        {{ address.mobile }}
      </div>
      <div class="location detail">
        {{ address.detail }}
      </div>
    </div>
    <div class="check">
      <slot name="icon">
        <icon-park name="edit" size="2rem" @click.stop="editAddress" />
      </slot>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  address: {
    type: Object,
    required: true
  }
})
const isDefault = computed(() => {
  return props.address.is_default === 1
})
const emit = defineEmits(['edit'])
const editAddress = () => {
  emit('edit', props.address)
}
</script>
<style lang="scss" scoped>
.address-card-container {
  padding: 1rem 0.72rem 1rem 1.25rem;
  background: #ffffff;
  border-bottom: 1px solid #f1f1f1;
  box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.05);
  border-radius: 0.32rem;
  position: relative;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  .tag {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 2.86rem;
    height: 2.86rem;
    border-radius: 1.43rem;
  }
  .is-default {
    color: #ffffff;
    font-weight: 400;
    font-size: 0.9rem;
    line-height: 1.1rem;
    background: linear-gradient(180deg, #000000 0%, #7f7f7f 100%);
  }
  .not-default {
    border: 1px solid #191919;
    color: #191919;
    background: #ffffff;
  }
  .info {
    flex: 1;
    padding-left: 1rem;
    font-weight: 400;
    font-size: 1rem;
    color: #757575;
    line-height: 1.2rem;
    .contacts {
      letter-spacing: 0.5px;
      .user-name {
        font-weight: 500;
        font-size: 1rem;
        color: #191919;
        line-height: 1.2rem;
      }
    }
    .location {
      letter-spacing: 1.2px;
    }
    .city {
      padding-top: 0.2rem;
    }
    .detail {
      padding-top: 0.2rem;
      display: -webkit-box;
      -webkit-line-clamp: 2; /* 指定行数 */
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }
  .check {
    padding-left: 1rem;
  }
}
</style>
