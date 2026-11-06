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
            dataArcade: null,
            dataRewards: null,
            settingsUpdateAttempts: null,
            isOpen: false,

            addToQueue: [],
            addToPackage: [],
            editPackage: [],

            addDayModal: false,
            editDayModal: false,
            editDailyModal: false,
            addQueueModal: false,
            deleteQueueModal: false,
            editRewardModal: false,
            dayDeleteModal: false,
            vipPackageModal: false,
            vipEditPackageModal: false,
            deleteVipPackageModal: false,

            // validate
            numberOfDays: 1,
            invalidNumberOfDays: false,
            numberOfTokens: 1,
            invalidNumberOfTokens: false,
            invalidPackageName: false,
            invalidPrice: false,
            invalidPackageTime: false,
            promotionId: null,
            day: null,
            reward: null,
            arcadeSettings: false,
            editVipPackageChange: false,

            dailyQueue: false,

            timestamp: new Date().getTime(),

            dataSaved: false,
            accessToken: '',
            refreshToken: '',
            secondToken: '',
        }
    },
    methods: {
        async getDailyRewards() {
            try {
                const response = await axios.get(
                    `${arcade}energy-info-admin?timestamp=${this.timestamp}`,
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                );
                this.dataArcade = response.data;
                this.dataArcade.arcade.time_for_increment = this.secondsToHours(response.data.arcade.time_for_increment);
                this.dataArcade.arcade.time_to_pass = this.secondsToMinutes(response.data.arcade.time_to_pass);
            } catch (error) {
                console.error(error);
            }
        },
        async postConfig() {
            try{
                const response = await axios.post(
                    `${arcade}update-arcade-config?timestamp=${this.timestamp}`,
                    {
                        day_id: id,
                        day: this.day,
                        reward: this.reward,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                await this.getDailyRewards().then(() => {
                    this.getRewards(this.promotionId)
                });
                this.editDayModal = false;
            }catch (error) {
                console.error(error);
            }
        },
        updateAddToQueue() {
            this.addToQueue = {
                name: this.addToQueue.name,
                is_active: false,
            };
        },
        updateAddToPackage() {
            this.addToPackage = {
                name: this.addToPackage.name,
                lifetimeHour: this.addToPackage.lifetimeHour,
                lifetimeMinute: this.addToPackage.lifetimeMinute,
                // lifetime: this.addToPackage.lifetime,
                price: this.addToPackage.price,
                is_active: false,
            };
            this.invalidPackageName = !(this.addToPackage.name.length >= 1 && this.addToPackage.name.length <= 255 );
            this.invalidPackageTime = !(this.addToPackage.lifetimeHour >= 0 && this.addToPackage.lifetimeHour <= 23 && this.addToPackage.lifetimeMinute >= 0 && this.addToPackage.lifetimeMinute <= 59);
            this.invalidPrice = !(this.addToPackage.price >= 1 && this.addToPackage.price <= 100);
            this.editVipPackageChange = true;
        },
        async putNewQueue() {
            try{
                const response = await axios.put(
                    `${arcade}update-daily-promotions-config?timestamp=${this.timestamp}`,
                    {
                        name: this.addToQueue.name,
                        is_active: false,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                this.addQueueModal = false;
                await this.getDailyRewards();
            }catch (error) {
                console.error(error);
            }
        },
        async putNewDay() {
            try{
                const response = await axios.put(
                    `${arcade}update-daily-rewards-config?timestamp=${this.timestamp}`,
                    {
                        promotion_id: this.promotionId,
                        reward: {
                            day: this.numberOfDays,
                            reward: this.numberOfTokens,
                        }
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                await this.getDailyRewards().then(() => {
                    this.getRewards(this.promotionId)
                    this.numberOfDays = 1;
                    this.numberOfTokens = 1;
                });
                this.addDayModal = false;
            }catch (error) {
                console.error(error);
            }
        },
        async editDay(id) {
            try{
                const response = await axios.post(
                    `${arcade}update-daily-rewards-config?timestamp=${this.timestamp}`,
                    {
                        day_id: id,
                        day: this.day,
                        reward: this.reward,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                await this.getDailyRewards().then(() => {
                    this.getRewards(this.promotionId)
                });
                this.editDayModal = false;
            }catch (error) {
                console.error(error);
            }
        },
        async postToQueue(id, name, activity) {
            try{
                const response = await axios.post(
                    `${arcade}update-daily-promotions-config?timestamp=${this.timestamp}`,
                    {
                        promotion_id: id,
                        name: name,
                        is_active: activity,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                this.addQueueModal = false;
                this.editDailyModal = false;
                await this.getDailyRewards();
            }catch (error) {
                console.error(error);
            }
        },
        async putVipPackage() {
            try{
                const response = await axios.put(
                    `${arcade}update-product-config?timestamp=${this.timestamp}`,
                    {
                        name: this.addToPackage.name,
                        price: this.addToPackage.price,
                        // lifetimeHour: this.addToPackage.lifetimeHour,
                        // lifetimeMinute: this.addToPackage.lifetimeMinute,
                        lifetime: this.addToPackage.lifetimeHour * 3600 + this.addToPackage.lifetimeMinute * 60,
                        is_active: this.addToPackage.is_active,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                this.vipPackageModal = false;
                this.addToPackage = [];
                await this.getDailyRewards();
            }catch (error) {
                console.error(error);
            }
        },
        async postVipPackage(id, name, price, lifetime, activity) {
            try{
                const response = await axios.post(
                    `${arcade}update-product-config?timestamp=${this.timestamp}`,
                    {
                        product_id: id,
                        name: name,
                        price: price,
                        lifetime: lifetime,
                        is_active: activity,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                await this.getDailyRewards();
            }catch (error) {
                console.error(error);
            }
        },
        async editVipPackage(id, name, price, activity, hour, minute) {
            try{
                const response = await axios.post(
                    `${arcade}update-product-config?timestamp=${this.timestamp}`,
                    {
                        product_id: id,
                        name: name,
                        price: price,
                        lifetime: hour * 3600 + minute * 60,
                        is_active: activity,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                this.vipEditPackageModal = false;
                await this.getDailyRewards();
            }catch (error) {
                console.error(error);
            }
        },
        async deleteVipPackage(id) {
            try {
                const response = await axios.delete(
                    `${arcade}update-product-config?timestamp=${this.timestamp}`,
                    {
                        data: { id },
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                );
                this.saved();
                this.deleteVipPackageModal = false;
                await this.getDailyRewards();
            } catch (error) {
                console.error(error);
            }
        },
        async deleteFromQueue(id) {
            try {
                const response = await axios.delete(
                    `${arcade}update-daily-promotions-config?timestamp=${this.timestamp}`,
                    {
                        data: { id },
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                );
                this.saved();
                this.deleteQueueModal = false;
                await this.getDailyRewards();
            } catch (error) {
                console.error(error);
            }
        },
        async deleteFromDay(id) {
            try {
                const response = await axios.delete(
                    `${arcade}update-daily-rewards-config?timestamp=${this.timestamp}`,
                    {
                        data: { id },
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                );
                this.saved();
                await this.getDailyRewards().then(() => {
                    this.getRewards(this.promotionId)
                });
                this.dayDeleteModal = false;
            } catch (error) {
                console.error(error);
            }
        },
        openDeleteVipPackageModal(id, name){
            this.id = id;
            this.name = name;
            this.deleteVipPackageModal = true;
        },
        openDeleteQueueModal(id, name) {
            this.id = id;
            this.name = name;
            this.deleteQueueModal = true;
        },
        openEditVipPackagesModal(id, name, hour, minute, price, activity){
            this.id = id;
            this.name = name;
            this.hour = hour;
            this.minute = minute;
            this.price = price;
            this.activity = activity;
            this.vipEditPackageModal = true;
        },
        openEditDailyModal(id, name, activity){
          this.id = id;
          this.name = name;
          this.activity = activity;
          this.editDailyModal = true
        },
        openEditRewardModal(id, day, reward){
            this.id = id;
            this.day = day;
            this.reward = reward;
            this.editDayModal = true
        },
        openDeleteRewardModal(id, day, reward){
            this.id = id;
            this.day = day;
            this.reward = reward;
            this.dayDeleteModal = true
        },
        checkActive(id, name, activity){
            this.postToQueue(id, name, activity).then(async () => {
                this.saved();
                await this.getDailyRewards();
            })
        },
        checkPackageActive(id, name, price, lifetime, activity){
            this.postVipPackage(id, name, price, lifetime, activity).then(async () => {
                this.saved();
                await this.getDailyRewards();
            })
        },
        getTooltipData(package) {
            const lifetimeHour = Math.floor(package.lifetime / 3600);
            const lifetimeMinute = Math.floor((package.lifetime % 3600) / 60);

            return `Lifetime: ${lifetimeHour} hours ${lifetimeMinute} minutes, Price: ${package.price}`;
        },
        getHoursEdit(item){
            const lifetimeHour = Math.floor(item.lifetime / 3600);
            return lifetimeHour;
        },
        getMinuteEdit(item){
            const lifetimeMinute = Math.floor((item.lifetime % 3600) / 60);
            return lifetimeMinute;
        },
        incrementGiftCount() {
            this.giftCount++;
        },
        decrementGiftCount() {
            if (this.giftCount > 0) {
                this.giftCount--;
            }
        },
        saveDay(id, day, reward){
            console.log('id:', id, 'day:', day, 'reward:', reward)
        },
        validateNumberOfDays() {
            this.invalidNumberOfDays = !(this.numberOfDays >= 1 && this.numberOfDays <= 31);
        },
        validNumberOfTokens() {
            this.invalidNumberOfTokens = !(this.numberOfTokens >= 1 && this.numberOfTokens <= 100);
        },
        validateThisDays() {
            this.invalidNumberOfDays = !(this.day >= 1 && this.day <= 31);
        },
        validateThisTokens() {
            this.invalidNumberOfTokens = !(this.reward >= 1 && this.reward <= 100);
        },
        validPackageName(){
            this.invalidPackageName = !(this.name.length >= 1 && this.name.length <= 255 );
            this.editVipPackageChange = true;
        },
        validPrice(){
            this.invalidPrice = !(this.price >= 1 && this.price <= 100);
            this.editVipPackageChange = true;
        },
        validPackageTime(){
            this.invalidPackageTime = !(this.hour >= 0 && this.hour <= 23 && this.minute >= 0 && this.minute <= 59);
            this.editVipPackageChange = true;
        },
        handleDaily() {
            if(this.addDayModal && this.dataRewards || this.editDayModal || this.dayDeleteModal){
                this.dailyQueue = true;
            }else{
                this.dailyQueue = !this.dailyQueue
            }
        },
        getRewards(id) {
            const promotion = this.dataArcade.daily_reward_promotions.find(promo => promo.promotion_id === id);
            this.dataRewards = promotion.rewards;
            this.handleDaily();
            this.promotionId = id;
        },

        // ================ Arcade settings ==================================
        async postSettings() {
            try{
                const response = await axios.post(
                    `${arcade}update-arcade-config?timestamp=${this.timestamp}`,
                    {
                        max_amount: this.dataArcade.arcade.max_amount,
                        reward: this.dataArcade.arcade.reward,
                        time_for_increment: this.dataArcade.arcade.time_for_increment * 3600,
                        time_to_pass: this.dataArcade.arcade.time_to_pass * 60,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`
                        }
                    }
                )
                this.saved();
                this.arcadeSettings = false;
                await this.getDailyRewards();
            }catch (error) {
                console.error(error);
            }
        },
        secondsToHours(seconds) {
            return seconds / 3600;
        },
        secondsToMinutes(minutes) {
            return minutes / 60;
        },
        decrementAmount(property) {
            if (this.dataArcade.arcade[property]) {
                this.dataArcade.arcade[property] = Math.max(this.dataArcade.arcade[property] - 1, 1);
                this.arcadeSettings = true;
            }
        },
        incrementAmount(property) {
            const isTimeForIncrement = property === 'time_for_increment';
            const limit = isTimeForIncrement ? 24 : 100;

            if (this.dataArcade.arcade[property] < limit) {
                this.dataArcade.arcade[property]++;
                this.arcadeSettings = true;
            }
        },

        handleChange() {
            if (this.dataArcade.arcade.max_amount > 100) {
                this.dataArcade.arcade.max_amount = 100;
            } else if (this.dataArcade.arcade.time_for_increment > 24){
                this.dataArcade.arcade.time_for_increment = 24;
            }
            this.arcadeSettings = true;
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
        getToken() {
            return new Promise((resolve, reject) => {
                axios.post(logIn, {
                    "email": email,
                    "password": password
                })
                    .then(response => {
                        this.accessToken = response.data.token.access;
                        this.refreshToken = response.data.token.refresh;
                        this.secondToken = response.data.token.second_access;
                        this.getDailyRewards().then(() => {
                            resolve();
                        }).catch(error => {
                            reject(error);
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        reject(error);
                    });
            });
        },
        firstLoad() {
            this.getToken().then(() => {
                this.getDailyRewards();
            }).then(() => {
                // do something after get data
            }).catch(error => {
                console.error('Error:', error);
            });
        },
    },
    mounted() {
        setInterval(() => {
            this.timestamp = new Date().getTime();
        }, 1000);
        this.timestamp = new Date().getTime();
        this.firstLoad();
        setTimeout(() => {
            this.getToken();
        }, 3 * 60 * 1000);
    }
});

app.mount('#app');