import {defineStore} from 'pinia';

export const usePopupStore = defineStore('popup', {
    state: () => ({
        head_show: false,
    }),
});
