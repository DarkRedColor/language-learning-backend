const app = Vue.createApp({
    data() {
        return {
            isOpen: false,
            expData: null,
            expChanged: false,
            rewardChanged: false,

            expModal: false,
            rewardModal: false,

            dataSaved: false,
            accessToken: '',
            refreshToken: '',
            secondToken: '',
        }
    },
    methods: {
        async getExp() {
            try {
                const response = await axios.get(
                    expEndpoint,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.expData = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async postExp() {
            try {
                const send = await axios.post(
                    expEndpoint,
                    {
                        for_one_mini_game: this.expData.for_one_mini_game,
                        for_theory: this.expData.for_theory,
                        for_dialogue: this.expData.for_dialogue,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    },
                );
                Object.assign(this.expData, send.data);
                this.saved();
                this.expChanged = false;
                this.expModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        async postReward() {
            try {
                const send = await axios.post(
                    expEndpoint,
                    {
                        init_lvl_capacity: this.expData.init_lvl_capacity,
                        next_lvl_capacity_percent: this.expData.next_lvl_capacity_percent,
                        average_lvl_up_coin_reward: this.expData.average_lvl_up_coin_reward,
                        increase_coins_prize: this.expData.increase_coins_prize,
                        special_lvl_up_coin_reward: this.expData.special_lvl_up_coin_reward,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    },
                );
                Object.assign(this.expData, send.data);
                this.saved();
                this.rewardChanged = false;
                this.rewardModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        toggleMenu() {
            this.isOpen = !this.isOpen;
        },
        saved(){
            this.dataSaved = true;
            setTimeout(() => {
                this.dataSaved = false;
            }, 1000)
        },
    },
    mounted() {
        this.getExp();
        const getToken = () => {
            axios.post(logIn, {
                "email": email,
                "password": password
            })
                .then(response => {
                    const accessToken = response.data.token.access;
                    const refreshToken = response.data.token.refresh;
                    const secondToken = response.data.token.second_access;
                    localStorage.setItem('access_token', accessToken);
                    localStorage.setItem('refresh_token', refreshToken);
                    localStorage.setItem('second_token', secondToken);
                    this.accessToken = `Access token: ${accessToken}`;
                    this.refreshToken = `Refresh token: ${refreshToken}`;
                    this.secondToken = `Second token: ${secondToken}`;
                })
                .catch(error => {
                    console.error(error);
                });
        };

        getToken();
        setInterval(() => {
            getToken();
        }, 5 * 60 * 1000);
    }
});

app.mount('#app');