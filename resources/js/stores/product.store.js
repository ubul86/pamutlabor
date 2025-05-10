import { defineStore } from 'pinia';
import productService from '@/services/product.service.js'

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        meta: [],
    }),
    actions: {
        async fetchProducts(params) {
            try {
                const result = await productService.getProducts(params);
                this.products = [...result.items];
                this.meta = result.meta;
            }
            catch(error) {
                console.log(error);
            }
        },

        async show(id) {
            return await productService.show(id);
        },

        async store(item) {
            const storedItem = await productService.store(item);
            this.products.push(storedItem);
        },

        async update(index, item) {
            this.products[index] = await productService.update(item);
        },

        async deleteItem(id) {
            await productService.delete(id);
            this.products = this.products.filter(product => product.id !== id);
        },

        async syncTags(productId, tagIds) {
            const result = await productService.syncTags(productId, tagIds);
            this.updateProductInList(result);
        },

        updateProductInList(updatedProduct) {
            const index = this.products.findIndex(p => p.id === updatedProduct.id);
            if (index !== -1) {
                this.products[index] = updatedProduct;
            }
        }
    },
});
