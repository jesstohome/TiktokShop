import {defineStore} from 'pinia';

export const useCounterStore = defineStore('counter', {
    state: () => ({
        meta: {},
    }),
    persist: true,
});
