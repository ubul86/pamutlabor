<template>
    <DialogForm
        :title="title"
        :fields="fields"
        v-model:formData="editedItem"
        :dialog-visible="localDialogVisible"
        @cancel="handleCancel"
        @submit="handleSubmit"
    />
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import DialogForm from './DialogForm.vue';
import useForm from '@/composables/useForm.js';
import { useProductStore } from '@/stores/product.store.js'
import { useToast } from 'vue-toastification';

const { formErrors, resetErrors, handleApiError } = useForm();

const productStore = useProductStore();

const toast = useToast()

const props = defineProps({
    dialogVisible: Boolean,
    editedIndex: Number,
});

const localDialogVisible = ref(props.dialogVisible);

const emit = defineEmits(['update:dialogVisible', 'save', 'close']);

const title = ref('New Product');


const editedItem = ref({
    name: null,
    price: null,
    description: null,
})

const defaultItem = {
    name: null,
    price: null,
    description: null,
}

onMounted(async () => {

});


watch(
    () => props.dialogVisible,
    (newVal) => {
        localDialogVisible.value = newVal;
    }
);

watch(
    () => props.editedIndex,
    (newVal) => {
        title.value = newVal < 0 ? 'New Product' : 'Edit Product';

        editedItem.value = {
            ...defaultItem,
        };

        if (newVal >= 0) {
            const product = productStore.products[newVal];

            if (product) {
                editedItem.value = {
                    ...product,
                };
            }
        }
    }
);

const handleCancel = () => {
    localDialogVisible.value = false;
    editedItem.value = { ...defaultItem };
    emit('close');
};

const handleSubmit = async (itemToSubmit) => {

    console.log(itemToSubmit);

    resetErrors();

    try {
        if (props.editedIndex > -1) {
            await productStore.update(props.editedIndex, itemToSubmit);
            toast.success('You have successfully edited the item!');
        } else {
            await productStore.store(itemToSubmit)
            toast.success('You have successfully created a new item!');
        }
        localDialogVisible.value = false;
        editedItem.value = { ...defaultItem };
        emit('close');
    }
    catch(error) {
        handleApiError(error);
        toast.error(error.response.data.message);
    }

};


const fields = computed(() => [
    { model: 'name', component: 'v-text-field', props: { label: 'name', error: !!formErrors.value.name, 'error-messages': formErrors.value.name || [] } },
    { model: 'description', component: 'v-text-field', props: { label: 'Description', error: !!formErrors.value.description, 'error-messages': formErrors.value.description || [] } },
    { model: 'price', component: 'v-text-field', props: { label: 'price', error: !!formErrors.value.price, 'error-messages': formErrors.value.price || [] } },
]);

</script>
