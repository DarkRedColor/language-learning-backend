const app = Vue.createApp({
    data() {
        return {
            isOpen: false,
            expData: null,
            coinData: null,
            expChanged: false,
            coinChanged: false,

            expIndex: null,
            coinIndex: null,

            expSelectedId: null,
            coinSelectedId: null,

            expModal: false,
            coinModal: false,

            dataSaved: false,
            accessToken: '',
            refreshToken: '',
            secondToken: '',
        }
    },
    methods: {
        async getExp(timestamp) {
            try {
                const response = await axios.get(
                    `${status}?timestamp=${timestamp}`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.expData = response.data.xp;
                this.coinData = response.data.coins;
            } catch (error) {
                console.error(error);
            }
        },
        async postExp() {
            try {
                const statusId = this.expSelectedId;
                const dataToUpdate = this.expData.statuses[this.expIndex];

                const send = await axios.put(
                    `${status}?status_id=${statusId}`,
                    {
                        ...dataToUpdate,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`,
                        },
                    }
                );
                this.expData.statuses[this.expIndex] = send.data;
                this.saved();
                this.expModal = false;
                this.expChanged = false;
                this.expIndex = null;
            } catch (error) {
                console.error(error);
            }
        },
        async postCoin() {
            try {
                const statusId = this.coinSelectedId;
                const dataToUpdate = this.coinData.statuses[this.coinIndex];

                const send = await axios.put(
                    `${status}?status_id=${statusId}`,
                    {
                        ...dataToUpdate,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`,
                        },
                    }
                );
                this.coinData.statuses[this.coinIndex] = send.data;
                this.saved();
                this.coinModal = false;
                this.coinChanged = false;
                this.coinIndex = null;
            } catch (error) {
                console.error(error);
            }
        },
        expShouldDisable(index) {
            return this.expIndex !== null && this.expIndex !== index;
        },
        coinShouldDisable(index) {
            return this.coinIndex !== null && this.coinIndex !== index;
        },
        expDecrementStatus(index) {
            const status = this.expData.statuses[index];
            status.trigger = Math.max(status.trigger - 1, 0);
            status.changed = true;
            this.expChanged = true;
        },
        expIncrementStatus(index) {
            const status = this.expData.statuses[index];
            status.trigger++;
            status.changed = true;
            this.expChanged = true;
        },
        coinDecrementStatus(index) {
            const status = this.coinData.statuses[index];
            status.trigger = Math.max(status.trigger - 1, 0);
            status.changed = true;
            this.coinChanged = true;
        },
        coinIncrementStatus(index) {
            const status = this.coinData.statuses[index];
            status.trigger++;
            status.changed = true;
            this.coinChanged = true;
        },
        getExpIndex(index){
            this.expIndex = index;
            this.expSelectedId = this.expData.statuses[index].id;
        },
        getCoinIndex(index){
            this.coinIndex = index;
            this.coinSelectedId = this.coinData.statuses[index].id;
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
        let timestamp = new Date().getTime();
        this.getExp(timestamp);
        setInterval(() => {
            timestamp = new Date().getTime();
        }, 1000);
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
        setTimeout(() => {
            getToken();
        }, 5 * 60 * 1000);
    }
});

app.mount('#app');