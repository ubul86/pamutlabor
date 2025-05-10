<template>
    <v-dialog v-model="localDialogVisible" max-width="500px">
        <v-card>
            <v-card-title>
                <span class="text-h5">{{ title }}</span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-combobox
                        v-model="selectedTags"
                        :items="tags"
                        label="Your favorite hobbies"
                        prepend-icon="mdi-filter-variant"
                        variant="solo"
                        chips
                        clearable
                        closable-chips
                        multiple
                        item-title="name"
                        item-value="id"
                    >
                        <template v-slot:chip="{ props, item }">
                            <v-chip v-bind="props">
                                <strong>{{ item.raw.name || item.raw }}</strong>
                            </v-chip>
                        </template>
                    </v-combobox>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn color="blue-darken-1" variant="text" @click="closeDialog">Cancel</v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="handleSubmit">Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>

<script setup>
import {ref, onMounted, watch} from 'vue'
import {useTagStore} from "@/stores/tag.store.js";
import {useProductStore} from "@/stores/product.store.js";

const props = defineProps({
    dialogVisible: Boolean,
    editedIndex: Number,
    editedItem: Object,
    tags: Array,
});

const localDialogVisible = ref(props.dialogVisible);

const emit = defineEmits(['update:dialogVisible', 'save', 'close']);

const title = ref('Connect Product with tags');

const tagStore = useTagStore();
const productStore = useProductStore();

onMounted(async () => {

});

watch(
    () => props.dialogVisible,
    (newVal) => {
        localDialogVisible.value = newVal;
    }
);

const selectedTags = ref([]);

watch(
    () => props.editedItem,
    (newVal) => {
        selectedTags.value = Array.isArray(newVal?.tags)
            ? newVal.tags.map(tag => tag.id)
            : [];
    },
    { immediate: true }
);

const closeDialog = () => {
    localDialogVisible.value = false;
    emit('close');
};

const handleSubmit = async () => {
    const newTags = selectedTags.value.filter(tag => typeof tag === 'string');
    const existingTagsObjects = selectedTags.value.filter(tag => typeof tag === 'object');
    const existingTagsIds = selectedTags.value.filter(tag => typeof tag === 'number');

    const createdTags = [];
    for (const tagName of newTags) {
        try {
            const newTag = await tagStore.store({ name: tagName });
            createdTags.push(newTag);
        } catch (error) {
            console.error('Tag létrehozása sikertelen:', tagName, error);
        }
    }

    const finalTags = [...existingTagsObjects, ...createdTags];

    const tagIds = [...finalTags.map(tag => tag.id), ...existingTagsIds];

    console.log(props.editedItem.id);
    console.log(tagIds);
    await productStore.syncTags(props.editedItem.id, tagIds);
    emit('close');
};

</script>
