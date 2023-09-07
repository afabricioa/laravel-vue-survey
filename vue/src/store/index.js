import {defineStore} from "pinia";
import axiosClient from "../axios";

export const useUserStore = defineStore('user', {
    state(){
        return {
            user: {
                data: {},
                token: sessionStorage.getItem('TOKEN')
            },
            currentSurvey: {
                loading: false,
                data: {}
            },
            surveys: {
                loading: false,
                data: []
            },
            questionTypes: ["text", "select", "radio", "checkbox", "textarea"]
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
        },

        async saveSurvey(survey){
            let response;
            if(survey.id){
                console.log(survey.id)
                response = axiosClient.put(`/survey/${survey.id}`, survey)
                .then((res) => {
                    return res.data;
                })

                return response;
            }else{
                response = axiosClient.post("/survey", survey)
                .then((res) => {
                    this.$patch({
                        surveys: {
                            data: [...this.surveys.data, res.data]
                        }
                    })
                    return res.data;
                })

                return response;
            }
        },

        async getSurvey(id){
            this.$patch({
                currentSurvey: {
                    loading: true
                }
            })
            return axiosClient
                    .get(`/survey/${id}`)
                    .then(({data}) => {
                        this.$patch({
                            currentSurvey: {
                                loading: false,
                                data: data.data
                            }
                        })
                        return data;
                    })
                    .catch((err) => {
                        this.$patch({
                            currentSurvey: {
                                loading: false
                            }
                        });
                        throw err;
                    });
        },

        async deleteSurvey(id){
            return axiosClient.delete(`/survey/${id}`)
        },

        async getSurveys(){
            this.$patch({
                surveys: {
                    loading: true
                }
            });
            return axiosClient.get("/survey").then(({data}) => {
                this.$patch({
                    surveys: {
                        loading: false,
                        data: data.data
                    }
                });
            })
        }
    },
});
