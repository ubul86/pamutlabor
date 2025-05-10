<template>

    <ToggleHeaderComponent :selectedHeaders="toggleHeaders" :headers="headers" @update:selectedHeaders="toggleHeaders = $event" />

    <EditProductDialogForm :edited-index="editedIndex" :edited-item="editedItem" :dialog-visible="dialog" @close="close" />

    <EditTagDialogForm :edited-index="editedIndex" :dialog-visible="dialogTag" :edited-item="editedItem.value" :tags="tags" @close="closeTagDialog" />

    <v-data-table-server
        v-model="selected"
        :headers="computedHeaders"
        show-select
        :items="productStore.products"
        v-model:search="search"
        :filter-keys="['name']"
        :mobile="smAndDown"
        @update:options="loadItems"
        :items-per-page="productStore?.meta?.items_per_page"
        :items-length="productStore?.meta?.total_items"
        :loading="tableLoadingItems"

    >

        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Products</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    density="compact"
                    label="Search"
                    prepend-inner-icon="mdi-magnify"
                    variant="solo-filled"
                    flat
                    hide-details
                    single-line
                ></v-text-field>

                <v-btn color="primary" dark v-bind="props" @click="openDialog">New Product</v-btn>

            </v-toolbar>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
            <v-icon class="me-2" size="small" @click="editItem(item)">mdi-pencil</v-icon>
            <v-icon size="small" @click="openTagDialog(item)">mdi-tag-multiple</v-icon>
            <v-icon size="small" @click="dialogDelete(item)">mdi-delete</v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Reset</v-btn>
        </template>
    </v-data-table-server>

    <v-row v-if="selected.length">
        <v-col>
            <v-btn @click="bulkDelete">Bulk delete</v-btn>
        </v-col>
    </v-row>

    <ProductDialogDeleteComponent
        :is-dialog-delete-open="isDialogDeleteOpen"
        @update:isDialogDeleteOpen="isDialogDeleteOpen = $event"
        :item-id="editedItem?.id"
        @closeDelete="closeDelete"
    />

</template>

<script setup>
import { ref, reactive, computed, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { useProductStore } from '@/stores/product.store.js'
import { useTagStore } from '@/stores/tag.store.js';

import ToggleHeaderComponent from '@/components/ToggleHeaderComponent.vue'
import { useDisplay } from 'vuetify'

import EditProductDialogForm from '@/components/dialogs/EditProductDialogForm.vue'
import ProductDialogDeleteComponent from '@/components/dialogs/ProductDialogDeleteComponent.vue'
import EditTagDialogForm from "@/components/dialogs/EditTagDialogForm.vue";


const isMobileView = ref(window.innerWidth < 960);

const checkScreenSize = () => {
    isMobileView.value = window.innerWidth < 960;
};

const tagStore = useTagStore();

onMounted(async () => {
    await tagStore.fetchTags();
    window.addEventListener('resize', checkScreenSize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', checkScreenSize);
});


const dialogTag = ref(false)

const tags = computed(() => {
    return tagStore.tags;
})

const openTagDialog = async (item) => {
    editedItem.value = item;
    dialogTag.value = true;
    await nextTick();
};

const closeTagDialog = () => {
    editedItem.value = null;
    dialogTag.value = false
}

const { smAndDown } = useDisplay()

const dialog = ref(false)

const productStore = useProductStore();

const headers = [
    {
        title: 'Product ID',
        align: 'start',
        key: 'id',
    },
    { title: 'Name', key: 'name', sortable: false },
    { title: 'Description', key: 'description', sortable: false },
    { title: 'Price', key: 'price' },
    { title: 'Created At', key: 'created_at' },
    { title: 'Updated At', key: 'updated_at' },
    { title: 'Actions', key: 'actions', sortable: false },
]

const selected = ref([])

const editedIndex = ref(-1)
const editedItem = reactive({
    name: null,
    price: '',
    description: null,
})

const defaultItem = {
    name: null,
    price: '',
    description: null,
}

const search = ref('');

const toggleHeaders = ref(['id','name', 'price', 'description', 'created_at', 'updated_at', 'actions']);

const computedHeaders = computed(() => {
    return headers.filter(header => toggleHeaders.value.includes(header.key));
})

const tableLoadingItems = ref(true);

const tableParams = reactive({
    page: 1,
    itemsPerPage: 10,
    sortBy: [],
    search: '',
    additionalFilters: {},
});

const loadItems = async (params) => {

    tableLoadingItems.value = true;

    Object.assign(tableParams, params);

    const filters = {};

    const combinedParams = {
        ...tableParams,
        filters
    };

    await productStore.fetchProducts(combinedParams)
    tableLoadingItems.value = false;
}


const editItem = (item) => {
    editedIndex.value = productStore.products.indexOf(item)
    dialog.value = true
}

const openDialog = () => {
    dialog.value = true;
}

const close = async () => {
    dialog.value = false
    editedIndex.value = -1
}

const isDialogDeleteOpen = ref(false);

const dialogDelete = (item) => {
    isDialogDeleteOpen.value = true;
    editedIndex.value = productStore.products.indexOf(item)
    Object.assign(editedItem, item)
};

const closeDelete = async() => {
    isDialogDeleteOpen.value = false;
    await nextTick();
    Object.assign(editedItem, defaultItem)
    editedIndex.value = -1
};


</script>

<style>

.v-data-table__tr:nth-child(odd) {
    background-color: #e5e7eb;
}

.v-data-table__tr:nth-child(even) {
    background-color: #ffffff;
}

.used-time-container {
    align-items: center;
}

@media (max-width: 960px) {
    .used-time-container {
        align-items: flex-end;
    }
}

.v-card--reveal {
    align-items: center;
    bottom: 0;
    justify-content: center;
    opacity: .9;
    position: absolute;
    width: 100%;
}

</style>
