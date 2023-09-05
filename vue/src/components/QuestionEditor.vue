<script setup>
import { ref } from 'vue';

    const props = defineProps({
        question: Object,
        index: Number
    });

    const emit = defineEmits(["change", "addQuestion", "deleteQuestion"]);

    const model = ref(JSON.parse(JSON.stringify(props.question)));
</script>

<template>
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold">
            {{ index + 1 }}. {{ model.question }}
        </h3>
    </div>

    <div class="flex items-center">
        <!-- add new question -->
        <button
            type="button"
            @click="addQuestion()"
            class="flex items-center text-cs py-1 px-3 mr-2 rounded-sm text-white bg-gray-600 hover:bg-gray-700"
        >
            Add
        </button>
        <!-- delete question -->
        <button
            type="button"
            @click="deleteQuetion()"
            class="flex items-center text-cs py-1 px-3 mr-2 rounded-sm text-white"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="red"
                class="w-6 h-6"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
        </button>
    </div>

    <div class="grid gap-3 grid-cols-12">
        <!-- question -->
        <div class="mt-3 col-span-9">
            <label :for="'questin_text_'+model.data"
                class="block text-sm font-medium text-gray-700"
            >
                Question Text
            </label>
            <input
                type="text"
                :name="'question_text_'+model.data"
                v-model="model.question"
                @change="dataChange"
                :id="'question_text_'+model.data"
                class="
                    mt-1
                    focus:ring-indigo-500
                    focus:border-indigo-500
                    block
                    w-full
                    shadow-sm
                    sm:text-sm
                    border-gray-300
                    rounded-md
                "
            />
        </div>
        <!-- question type -->
        <div class="mt-3 col-span-3">
            <label for="question_type" class="block text-sm font-medium text-gray-700">
                Select Question Type
            </label>
            <select
                id="question_type"
                name="question_type"
                v-model="model.type"
                @change="typeChange"
            >

            </select>
        </div>
    </div>
</template>