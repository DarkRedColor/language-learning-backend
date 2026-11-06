const app = Vue.createApp({
    data() {
        return {
            data: null,
            isOpen: false,
            showModal: false,
            energyChanged: false,
            typeFastCahnged: false,
            tellFriendChanged: false
        }
    },
    methods: {
        async getData() {
            try {
                const response = await axios.get(
                    'https://3e5a1b3b990187c9fb8e8156ce25c243.langly.ai/api/energy/energy-parameters/'
                );
                this.data = response.data;
                this.data.energy_restoring_period_hour = this.data.energy_restoring_period.split(' ')[0];
                this.data.energy_restoring_period_min = this.data.energy_restoring_period.split(' ')[1];
                this.data.type_fast_restoring_period_hour = this.data.type_fast_restoring_period.split(' ')[0];
                this.data.type_fast_restoring_period_min = this.data.type_fast_restoring_period.split(' ')[1];
                this.data.tell_a_fiend_restoring_period_hour = this.data.tell_a_fiend_restoring_period.split(' ')[0];
                this.data.tell_a_fiend_restoring_period_min = this.data.tell_a_fiend_restoring_period.split(' ')[1];
            } catch (error) {
                console.error(error);
            }
            this.energyChanged = false;
            this.typeFastChanged = false;
            this.tellFriendChanged = false;
        },
        async postData() {
            try {
                const send = await axios.post(
                    'https://3e5a1b3b990187c9fb8e8156ce25c243.langly.ai/api/energy/energy-parameters/',
                    {
                        energy_max_quantities: this.data.energy_max_quantities,
                        energy_restoring_period: this.data.energy_restoring_period_hour + ":" + this.data.energy_restoring_period_min,
                        energy_quantities_for_decrease_after_mistake: this.data.energy_quantities_for_decrease_after_mistake,
                        type_fast_amount_energy_increase: this.data.type_fast_amount_energy_increase,
                        type_fast_restoring_period: this.data.type_fast_restoring_period_hour + ":" + this.data.type_fast_restoring_period_min,
                        type_fast_max_attempts: this.data.type_fast_max_attempts,
                        tell_a_fiend_amount_energy_increase: this.data.tell_a_fiend_amount_energy_increase,
                        tell_a_fiend_restoring_period: this.data.tell_a_fiend_restoring_period_hour + ":" + this.data.tell_a_fiend_restoring_period_min,
                        tell_friend_max_invitation: this.data.tell_friend_max_invitation
                    }
                );
                Object.assign(this.data, send.data);
            } catch (error) {
                console.error(error);
            }
            this.energyChanged = false;
            this.typeFastChanged = false;
            this.tellFriendChanged = false;
            this.showModal = false;
        },
        toggleMenu() {
            this.isOpen = !this.isOpen;
        },
    },
    mounted() {
        this.getData();
    }
});

app.mount('#app');