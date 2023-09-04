import {defineStore} from "pinia";

export const useUserStore = defineStore('user', {
    state(){
        return {
            user: {
                data: {},
                token: null
            }
        }
    },
    actions: {
        async register(registerUser){
            return fetch(`http://127.0.0.1:8000/api/register`, {
                mode: 'no-cors',
                method: 'POST',
                body: JSON.stringify(registerUser),
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                }
            })
            .then((res) => res.json())
            .then((res) => {
                console.log(res)
            })
        },
        logout(){
            this.$patch({
                user: {
                    data: {},
                    token: null
                }
            })
        }
    },
});
