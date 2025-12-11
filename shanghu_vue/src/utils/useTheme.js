//控制页面主题样式
import {ref} from 'vue';
//dark
const theme = ref('light');


watchEffect(() => {
    document.documentElement.dataset.theme = theme.value;
});


export function useTheme() {
    return {
        theme,
    };
}