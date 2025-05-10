import {publicApi} from "./api";

class ProductService {

    getTags(params) {
        return publicApi
            .get("/tag", {params})
            .then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to fetch tags:", error);
                throw error;
            });
    }

    store(item) {
        return publicApi.post(`/tag`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }
}

export default new ProductService();
