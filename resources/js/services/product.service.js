import {publicApi} from "./api";

class ProductService {

    getProducts(params) {
        return publicApi
            .get("/product", {params})
            .then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to fetch products:", error);
                throw error;
            });
    }

    store(item) {
        return publicApi.post(`/product`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    show(id) {
        return publicApi.get(`/product/${id}`).then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to get product:", error);
                throw error;
            });
    }

    update(item) {
        return publicApi.put(`/product/${item.id}`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    delete(id) {
        return publicApi
            .delete(`/product/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    syncTags(productId, tagIds) {
        return publicApi.put(`/products/${productId}/tags`, {
            tag_ids: tagIds
        }).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }
}

export default new ProductService();
