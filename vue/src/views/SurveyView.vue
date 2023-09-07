<script setup>
    import PageComponent from '../components/PageComponent.vue';
    import QuestionEditor from '../components/QuestionEditor.vue';
    import {ref, watch, computed} from "vue";
    import { v4 as uuidv4 } from 'uuid';
    import { useRoute, useRouter } from 'vue-router';
    import { useUserStore } from '../store/index';

    const route = useRoute();
    const router = useRouter();
    const store = useUserStore();

    let surveyLoading = computed(() => store.currentSurvey.loading);

    let model = ref({
        title: "",
        status: false,
        description: null,
        image: null,
        expire_date: null,
        questions: []
    });

    watch(store.currentSurvey.data, () => {
        model.value = {
            ...JSON.parse(JSON.stringify(store.currentSurvey.data)),
            status: store.currentSurvey.data.status !== "draft",
            expire_date: store.currentSurvey.data.expire_date.split(" ")[0]
        };

    });

    if(route.params.id){
        store.getSurvey(route.params.id);
    }

    function questionChange(data, index){
        model.value.questions[index] = data;
    }

    function addQuestion(index){
        let newQuestion = {
            id: uuidv4(),
            type: "text",
            question: null,
            description: null,
            data: {}
        }

        model.value.questions.splice(index, 0, newQuestion);
        //add a newQuestion at index without delete a value in array
    }

    function deleteQuestion(index){
        model.value.questions.splice(index, 1);
    }

    function saveSurvey(){
        store.saveSurvey(model.value).then(({data}) => {
            router.push({
                name: "Surveys"
            })
        })
    }

    function deleteSurvey(){
        store.deleteSurvey(route.params.id).then(() => {
            router.push({
                name: 'Surveys'
            })
        })
    }
</script>

<template>
    <PageComponent>
        <template v-slot:header>
            <div class="flex items-center justify-between">
                <h1 class="text-3x1 font-bold text-gray-900">
                    {{ model.id ? model.title : "Create a Survey" }}
                </h1>
                <button
                    v-if="route.params.id"
                    type="button"
                    class="py-2 px-3 text-white bg-red-500 rounded-md hover:bg-red-400"
                    @click="deleteSurvey"
                >
                    Delete Survey
                </button>
            </div>
        </template>
        <div v-if="surveyLoading" class="flex justify-center">Loading...</div>
        <form v-else @submit.prevent="saveSurvey">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
                <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <!-- title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">
                            Title
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            v-model="model.title"
                            autocomplete="survey_title"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                    </div>
                    <!-- description -->
                    <div>
                        <label for="about" class="block text-sm font-mediu text-gray-700">
                            Description
                        </label>
                        <div class="mt-1">
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                v-model="model.description"
                                autocomplete="survey_description"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                                placeholder="Describe your survey"
                            />
                        </div>
                    </div>
                    <!-- expire date -->
                    <div>
                        <label
                            for="expire_date"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Expire Date
                        </label>
                        <input
                            type="date"
                            name="expire_date"
                            id="expire_date"
                            v-model="model.expire_date"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                        />
                    </div>
                </div>
                <!-- status -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="status"
                            name="status"
                            type="checkbox"
                            v-model="model.status"
                            class="
                                ml-5
                                focus:ring-indigo-500
                                h-4 w-4
                                text-indigo-500
                                border-gray-300
                                rounded
                            "
                        />
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="status" class="font-medium text-gray-700">Active</label>
                    </div>
                </div>
                <!-- survey fields -->
                <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h3 class="text-2x1 font-semibold flex items-center justify-between">
                        Questions
                        <button
                            type="button"
                            @click="addQuestion()"
                            class="flex items-center text-sm py-1 px-4 rounded-sm text-white bg-gray-600 hover:bg-gray-700"
                        >
                            Add Question
                        </button>
                    </h3>
                    <div v-if="model.questions.length === 0" class="text-center text-gray-6000">
                        You don't have any questions created
                    </div>
                    <div v-for="(question, index) in model.questions" :key="question.id">
                        <QuestionEditor
                            :question="question"
                            :index="index"
                            @change="questionChange"
                            @addQuestion="addQuestion"
                            @deleteQuestion="deleteQuestion"
                        />
                    </div>
                </div>

                <div class="mt-6 px-4 py-3 bg-gray-500 text-right sm:px-6">
                    <button
                        type="submit"
                        class="
                            inline-flex
                            justify-center
                            py-2
                            px-4
                            border border-transparent
                            shadow-sm
                            text-sm
                            font-medium
                            rounded-md
                            text-white
                            bg-indigo-500
                            hover:bg-indigo-700
                            focus:outline-none
                        "
                    >Save</button>
                </div>
            </div>
        </form>
    </PageComponent>
</template>

