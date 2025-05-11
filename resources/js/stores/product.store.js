import { defineStore } from 'pinia';
import productService from '@/services/product.service.js'

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        meta: {
            'items_per_page' : 10,
            'total_items': 0,
            'total_pages': 0,
            'current_page': 1
        }
    }),
    actions: {
        async fetchProducts(params) {
            try {
                const result = await productService.getProducts(params);
                this.products = result.items;
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
            await productService.store(item);
            await this.fetchProducts({
                page: this.meta.current_page,
                itemsPerPage: this.meta.items_per_page,
            });
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
