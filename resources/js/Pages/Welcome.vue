<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    bookmarks: Object,
    tags: Object,
    filters: Object,
});

const selectedTags = ref(props.filters.tags?.map(Number) ?? []);

watch(selectedTags.value, (value) => {
    router.get(
        "/",
        { tags: value },
        {
            only: ["bookmarks"],
            preserveState: true,
            replace: true,
        }
    );
});

const toggleTag = (id) => {
    if (selectedTags.value.includes(id)) {
        selectedTags.value.splice(selectedTags.value.indexOf(id), 1);
    } else {
        selectedTags.value.push(id);
    }
};
</script>
<template>
    <div class="grid grid-cols-5 gap-2 max-w-xl mx-auto mt-20">
        <button
            v-for="tag in tags.data"
            :key="tag.id"
            @click="toggleTag(tag.id)"
            class="flex-none rounded-full px-3.5 py-1 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
            :class="
                selectedTags.includes(tag.id) ? 'bg-gray-900' : 'bg-gray-400'
            "
        >
            {{ tag.name }}
        </button>
    </div>

    <ul role="list" class="divide-y divide-gray-100 max-w-lg mx-auto mt-20">
        <li v-for="bookmark in bookmarks.data" :key="bookmark.id" class="py-4">
            <div class="flex items-center gap-x-3">
                <h3
                    class="flex-auto truncate text-sm font-semibold leading-6 text-gray-900"
                >
                    {{ bookmark.title }}
                </h3>
            </div>
            <p class="mt-3 truncate text-sm text-gray-500">
                {{ bookmark.description }}
            </p>
            <div class="grid grid-cols-5 gap-2 mt-2">
                <div
                    v-for="tag in bookmark.tags"
                    :key="tag.id"
                    class="flex-none text-center rounded-full bg-gray-900 px-3.5 py-1 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
                >
                    {{ tag.name }}
                </div>
            </div>
        </li>
    </ul>
</template>
