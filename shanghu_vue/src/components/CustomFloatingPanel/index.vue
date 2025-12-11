<template>
    <van-floating-panel v-if="show" :height="height">
        <slot />
    </van-floating-panel>
    <van-overlay :show="show" @click="handlerClickOverlay" />
</template>
<script setup>
defineProps({
    height:{
        type:[Number,String],
        required:false,
        default:500
    }
})
const show=ref(false)
const handlerClickOverlay=()=>{
    show.value=false
}
defineExpose({show})
</script>
<style lang="scss" scoped>

</style>