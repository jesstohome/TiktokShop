<template>
    <div class="container">
        <nav-bar 
            title="客服"
        />
        <div class="content">
            <iframe 
                style="min-height: 100%; width: 100%; overflow: hidden;" 
                frameborder="0" id="feee_c2c_widget" 
                :src="platform.custom_service"
            >
            </iframe>
        </div>
    </div>
</template>
<script setup>
import { platformInfo } from '@/api/home.js'
import toast from '@/utils/toast.js';
const platform=ref({})
const getPlatformInfo=()=>{
    toast.loading({msg:'加载中...'})
    platformInfo().then(res=>{
        platform.value=res.data
    }).catch(err=>err).finally(()=>{
        toast.close()
    })
}

getPlatformInfo()
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container{
    padding: 0;
    .content{
        padding: 0;
        height: calc(100dvh - 50px);
    }
}
</style>