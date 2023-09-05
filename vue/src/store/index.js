import {defineStore} from "pinia";
import axiosClient from "../axios";

const tmpSurveys = [
    {
        id: 1,
        title: "TheCodeholic Youtube Channel content",
        slug: "thecodeholic-youtube-channel-content",
        status: "draft",
        image: "https://cdn.cms-twdigitalassets.com/content/dam/business-twitter/pt/resources/blog/q1-2022/dos-donts-images.jpg.twimg.768.jpg",
        description: "My name is Antonio. <br> I am a Web Developer with 3+ years of exeperience.",
        created_at: "2023-09-05",
        updated_at: "2023-09-05",
        expire_date: "2023-09-07",
        questions: [
            {
                id: 1,
                type: "select",
                question: "Which country are you?",
                description: null,
                data: {
                    options: [
                        {uuid: "9bdc8c18-4c29-11ee-be56-0242ac120002", text: "USA"},
                        {uuid: "a68ad0a2-4c29-11ee-be56-0242ac120002", text: "BRAZIL"}
                    ]
                }
            },
            {
                id: 2,
                type: "checkbox",
                question: "Which language videos do you want to see on my channel?",
                description: "Lorem ipsum",
                data: {
                    options: [
                        {uuid: "e12cba4a-4c29-11ee-be56-0242ac120002", text: "JavaScript"},
                        {uuid: "e9a738b2-4c29-11ee-be56-0242ac120002", text: "RactJS"}
                    ]
                }
            },
            {
                id: 3,
                type: "text",
                question: "What's your favorite youtube channel?",
                description: null,
                data: {}
            }
        ],
    },
    {
        id: 2,
        title: "Laravel 8",
        slug: "laravel-8",
        status: "active",
        image: "https://cdn.cms-twdigitalassets.com/content/dam/business-twitter/pt/resources/blog/q1-2022/dos-donts-images.jpg.twimg.768.jpg",
        description: "Laravel is a web application framework based on PHP.",
        created_at: "2023-09-04",
        updated_at: "2023-09-04",
        expire_date: "2023-09-07",
        questions: [],
    }
];

export const useUserStore = defineStore('user', {
    state(){
        return {
            user: {
                data: {},
                token: sessionStorage.getItem('TOKEN')
            },
            surveys: [...tmpSurveys]
        }
    },
    actions: {
        async register(registerUser){
            return axiosClient.post('/register', registerUser)
                    .then(({data}) => {
                        this.$patch({
                            user: {
                                data: data.user,
                                token: data.token
                            }
                        })
                        sessionStorage.setItem('TOKEN', data.token);
                        return data;
                    })
        },
        async logout(){
            return axiosClient.post('/logout')
                .then(response => {
                    this.$patch({
                        user: {
                            data: {},
                            token: null
                        }
                    });
                    sessionStorage.clear();
                    return response;
                })
        },
        async login(credentials){
            return axiosClient.post('/login', credentials)
                    .then(({data}) => {
                        this.$patch({
                            user: {
                                data: data.user,
                                token: data.token
                            }
                        })
                        sessionStorage.setItem('TOKEN', data.token);
                        return data;
                    })
        }
    },
});
