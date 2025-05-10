import { defineStore } from 'pinia';
import tagService from '@/services/tag.service.js'

export const useTagStore = defineStore('tag', {
    state: () => ({
        tags: [],
    }),
    actions: {
        async fetchTags(params) {
            try {
                const result = await tagService.getTags(params);
                this.tags = [...result.items];
            }
            catch(error) {
                console.log(error);
            }
        },

        async store(item) {
            const storedItem = await tagService.store(item);
            this.tags.push(storedItem);
            return storedItem;
        },

    },
});
