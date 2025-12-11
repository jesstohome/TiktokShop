<template>
    <BIcon
        :color="color"
    >
        <BSvg :name="name" :size="size" />
    </BIcon>
</template>
<script setup>
import BIcon from './BIcon.vue'
import BSvg from './BSvg.vue'
defineProps({
    name:{
        type:String,
        required:true
    },
    color:{
        type:String,
        required:false,
        default:'#191919'
    },
    size:{
        type:String,
        required:false,
        default:'1rem'
    }
})
</script>

<style scoped>
</style>