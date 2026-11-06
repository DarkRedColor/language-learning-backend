// Debounce function
function debounce(func, wait) {
    let timeout;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

const app = Vue.createApp({
    data() {
        return {
            dataEnergy: null,
            coinsData : null,
            offerData : null,
            energyDataPremium : null,
            adsData : null,
            isOpen: false,

            timestamp: new Date().getTime(),

            energyModal: false,
            energyPremiumModal: false,
            typeFastModal: false,
            tellFriendModal: false,
            globalCoinsModal: false,
            exchangeModal: false,
            adsModal: false,
            offerModal: false,

            energyChanged: false,
            energyPremiumChanged: false,
            typeFastChanged: false,
            tellFriendChanged: false,
            globalCoinsChanged: false,
            exchangeChanged: false,
            adsChanged: false,
            offerChanged: false,
            giftsChanged: false,

            searchQuery: '',
            userID: null,
            giftCount: 60,
            searchResults: [],

            dataSaved: false,
            accessToken: '',
            refreshToken: '',
            secondToken: '',
        }
    },
    methods: {
        async searchUsers() {
            if (this.searchQuery.length > 0) {
                debounce(() => {
                    axios.get(users, {
                        params: {
                            search: this.searchQuery
                        },
                        headers: {
                            Authorization: `Bearer ${localStorage.access_token}`
                        },
                    }).then(response => {
                        this.searchResults = response.data.results;
                        if (response.data.results.length > 0) {
                            this.userID = response.data.results[0].id;
                            this.giftsChanged = true;
                        } else {
                            this.userID = null;
                            this.giftsChanged = false;
                        }
                    }).catch(error => {
                        console.error(error);
                    });
                }, 500)();
            } else {
                this.userID = null;
                this.searchResults = [];
            }
        },
        incrementGiftCount() {
            this.giftCount++;
        },
        decrementGiftCount() {
            if (this.giftCount > 0) {
                this.giftCount--;
            }
        },
        handleInputChange() {
            if (this.searchQuery.length === 0 || this.userID === null) {
                this.giftsChanged = false;
            } else {
                this.giftsChanged = true;
            }
        },
        async postCoinsGifts() {
            try {
                const send = await axios.post(
                    gifts + '?source=admin',
                    {
                        user_id: this.userID,
                        coins: this.giftCount,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    },
                );
                // Object.assign(this.data, send.data);
                this.saved();
                this.giftsChanged = false;
            } catch (error) {
                console.error(error);
            }
        },
        async getData() {
            try {
                const response = await axios.get(
                    energy,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.dataEnergy = response.data;
                this.dataEnergy.energy_restoring_period_hour = this.dataEnergy.energy_restoring_period.split(' ')[0];
                this.dataEnergy.energy_restoring_period_min = this.dataEnergy.energy_restoring_period.split(' ')[1];
                this.dataEnergy.type_fast_restoring_period_hour = this.dataEnergy.type_fast_restoring_period.split(' ')[0];
                this.dataEnergy.type_fast_restoring_period_min = this.dataEnergy.type_fast_restoring_period.split(' ')[1];
                this.dataEnergy.tell_a_fiend_restoring_period_hour = this.dataEnergy.tell_a_fiend_restoring_period.split(' ')[0];
                this.dataEnergy.tell_a_fiend_restoring_period_min = this.dataEnergy.tell_a_fiend_restoring_period.split(' ')[1];
            } catch (error) {
                console.error(error);
            }
        },
        async getDataPremium() {
            try {
                const response = await axios.get(
                    energy + '?config_type=advanced_battery',
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.energyDataPremium = response.data;
                this.energyDataPremium.energy_restoring_period_hour = this.energyDataPremium.energy_restoring_period.split(' ')[0];
                this.energyDataPremium.energy_restoring_period_min = this.energyDataPremium.energy_restoring_period.split(' ')[1];

            } catch (error) {
                console.error(error);
            }
        },
        async getCoins() {
            try {
                const response = await axios.get(
                    coins,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.coinsData = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async getOffer() {
            try {
                const response = await axios.get(
                    offer,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.offerData = response.data.products.common_products.energy[0];
            } catch (error) {
                console.error(error);
            }
        },
        async getAds(){
          try{
              const response = await axios.get(
                  ads,
                  {
                      headers: {
                          Authorization: `Bearer ${localStorage.second_token}`
                      }
                  }
              );
              this.adsData = response.data;
              this.adsData.replenish_time_hour = this.adsData.replenish_time.split(' ')[0];
              this.adsData.replenish_time_min = this.adsData.replenish_time.split(' ')[1];
          } catch (error) {
              console.error(error);
          }
        },
        async postOffer() {
            try{
                const response = await axios.put(
                    offer + "?product_id=energy_unit",
                    {
                        quantity: this.offerData.amount,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                )
                this.saved();
                this.offerChanged = false;
                this.offerModal = false;
            }catch (error) {
                console.error(error);
            }
        },
        async postEnergy() {
            try {
                const send = await axios.post(
                    energy,
                    {
                        energy_max_quantities: this.dataEnergy.energy_max_quantities,
                        energy_restoring_period: this.dataEnergy.energy_restoring_period_hour + " " + this.dataEnergy.energy_restoring_period_min,
                        energy_quantities_for_decrease_after_mistake: this.dataEnergy.energy_quantities_for_decrease_after_mistake,
                        tell_friend_max_invitation: this.dataEnergy.tell_friend_max_invitation,
                        tell_a_fiend_amount_energy_increase: this.dataEnergy.tell_a_fiend_amount_energy_increase,
                        type_fast_max_attempts: this.dataEnergy.type_fast_max_attempts,
                        type_fast_restoring_period: this.dataEnergy.type_fast_restoring_period_hour + " " + this.dataEnergy.type_fast_restoring_period_min,
                        type_fast_amount_energy_increase: this.dataEnergy.type_fast_amount_energy_increase,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                Object.assign(this.dataEnergy, send.data);
                this.saved();
                this.energyChanged = false;
                this.energyModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        async postDataPremium() {
            try {
                const send = await axios.post(
                    energy + '?config_type=advanced_battery',
                    {
                        energy_max_quantities: this.energyDataPremium.energy_max_quantities,
                        energy_restoring_period: this.energyDataPremium.energy_restoring_period_hour + " " + this.energyDataPremium.energy_restoring_period_min,
                        energy_quantities_for_decrease_after_mistake: this.energyDataPremium.energy_quantities_for_decrease_after_mistake,
                        tell_friend_max_invitation: this.energyDataPremium.tell_friend_max_invitation,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                Object.assign(this.energyDataPremium, send.data);
                this.saved();
                this.energyPremiumChanged = false;
                this.energyPremiumModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        async postTypeFast() {
            try {
                const send = await axios.post(
                    energy,
                    {
                        energy_max_quantities: this.dataEnergy.energy_max_quantities,
                        energy_restoring_period: this.dataEnergy.energy_restoring_period_hour + " " + this.dataEnergy.energy_restoring_period_min,
                        energy_quantities_for_decrease_after_mistake: this.dataEnergy.energy_quantities_for_decrease_after_mistake,
                        tell_friend_max_invitation: this.dataEnergy.tell_friend_max_invitation,
                        tell_a_fiend_amount_energy_increase: this.dataEnergy.tell_a_fiend_amount_energy_increase,
                        type_fast_max_attempts: this.dataEnergy.type_fast_max_attempts,
                        type_fast_restoring_period: this.dataEnergy.type_fast_restoring_period_hour + " " + this.dataEnergy.type_fast_restoring_period_min,
                        type_fast_amount_energy_increase: this.dataEnergy.type_fast_amount_energy_increase,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                Object.assign(this.dataEnergy, send.data);
                this.saved();
                this.typeFastChanged = false;
                this.typeFastModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        async postTellFriend() {
            try {
                const send = await axios.post(
                    energy,
                    {
                        tell_friend_max_invitation: this.dataEnergy.tell_friend_max_invitation,
                        tell_a_fiend_restoring_period: this.dataEnergy.tell_a_fiend_restoring_period_hour + " " + this.dataEnergy.tell_a_fiend_restoring_period_min,
                        tell_a_fiend_amount_energy_increase: this.dataEnergy.tell_a_fiend_amount_energy_increase,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    },
                );
                Object.assign(this.dataEnergy, send.data);
                this.saved();
                this.tellFriendChanged = false;
                this.tellFriendModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        async postAds() {
            try {
                const send = await axios.post(
                    ads,
                    {
                        energy_increase: this.adsData.energy_increase,
                        replenish_time: this.adsData.replenish_time_hour + " " + this.adsData.replenish_time_min,
                        number_of_impressions: this.adsData.number_of_impressions
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    },
                );
                Object.assign(this.adsData, send.data);
                this.saved();
                this.adsChanged = false;
                this.adsModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        async postGlobalCoins() {
            try {
                const send = await axios.post(
                    coins,
                    {
                        coins_per_correct_answer: parseInt(this.coinsData.coins_per_correct_answer),
                        coins_reward_theory: parseInt(this.coinsData.coins_reward_theory),
                        coins_reward_dialog: parseInt(this.coinsData.coins_reward_dialog),
                        correct_answers_required_combo: parseInt(this.coinsData.correct_answers_required_combo),
                        coins_reward_combo: parseInt(this.coinsData.coins_reward_combo),
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    },
                );
                Object.assign(this.coinsData, send.data);
                this.saved();
                this.globalCoinsChanged = false;
                this.globalCoinsModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        async postExchange() {
            try {
                const send = await axios.post(
                    coins,
                    {
                        exchange_rate: parseInt(this.coinsData.exchange_rate),
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    },
                );
                Object.assign(this.coinsData, send.data);
                this.saved();
                this.exchangeChanged = false;
                this.exchangeModal = false;
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
        }
    },
    mounted() {
        this.getData();
        this.getDataPremium();
        this.getCoins();
        this.getOffer();
        this.getAds();
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
        }, 3 * 60 * 1000);
    }
});

app.mount('#app');