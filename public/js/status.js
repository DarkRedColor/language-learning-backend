const app = Vue.createApp({
    data() {
        return {
            expData: [],
            coinData: [],
            dragIndex: null,
            dropIndex: null,
            coinDragIndex: null,
            coinDropIndex: null,
            isItemMoved: false,
            isCoinMoved: false,
            toggleAddStatusOpen: false,

            showEdit: false,
            showCoinEdit: false,
            selectedExpIndex: null,
            selectedCoinIndex: null,
            showExpId: null,
            showCoinId: null,
            discardIndex: null,
            coinDiscardIndex: null,
            showConfirmationDialogExp: false,
            showConfirmationDialogCoin: false,
            showAlertDiscard: false,

            isNewEntity: false,

            imageChange: false,

            inputChanges: false,
            accessToken: '',
            refreshToken: '',
            secondToken: '',
        };
    },
    methods: {
        async getExpStatus(timestamp) {
            try {
                const response = await axios.get(
                    `${status}?timestamp=${timestamp}`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                console.log(timestamp)
                if(response.data.xp.statuses === null){
                    this.expData = [];
                }else{
                    this.expData = response.data.xp.statuses;
                }
            } catch (error) {
                console.error(error);
            }
        },
        async getCoinStatus(timestamp) {
            try {
                const response = await axios.get(
                    `${status}?timestamp=${timestamp}`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                console.log(timestamp)
                if(response.data.coins.statuses === null){
                    this.coinData = [];
                }else{
                    this.coinData = response.data.coins.statuses;
                }
            } catch (error) {
                console.error(error);
            }
        },
        async updatePositionsOnServer(itemId, newPosition) {
            try {
                const response = await axios.get(
                    sort + `?timestamp=${timestamp}?status_type=xp&index=${newPosition}&status_id=${itemId}`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                response.data = this.expData;
                this.getExpStatus(`${timestamp}`);
            } catch (error) {
                console.error(error);
            }
        },
        async updatePositionsCoinOnServer(itemId, newPosition) {
            try {
                const response = await axios.get(
                    sort + `?timestamp=${timestamp}?status_type=coins&index=${newPosition}&status_id=${itemId}`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                response.data = this.coinData;
                this.getCoinStatus(`${timestamp}`);
            } catch (error) {
                console.error(error);
            }
        },
        async saveExpData() {
            try {
                await this.uploadImage();
                const selectedId = this.showExpId;
                const dataToUpdate = this.expData[this.selectedExpIndex];

                const send = await axios.put(
                    `${status}?status_id=${selectedId}`,
                    dataToUpdate,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`,
                        },
                    }
                );
                this.expData[this.selectedExpIndex] = send.data;
                this.inputChanges = false;
                this.isItemMoved = true;
                this.getExpStatus(`${timestamp}`);
            } catch (error) {
                console.log(error);
            }
        },

        async pushExpData() {
            try {
                await this.uploadImage();
                const dataToUpdate = this.expData[this.selectedExpIndex];

                const send = await axios.post(
                    status,
                    dataToUpdate,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`,
                        },
                    }
                );
                this.expData[this.selectedExpIndex] = send.data;
                this.inputChanges = false;
                this.isItemMoved = true;
                this.isNewEntity = false;
                this.getExpStatus(`${timestamp}`);
            } catch (error) {
                console.log(error);
            }
        },
        async saveCoinData() {
            try {
                await this.coinUploadImage();
                const selectedId = this.showCoinId;
                const dataToUpdate = this.coinData[this.selectedCoinIndex];

                const send = await axios.put(
                    `${status}?status_id=${selectedId}`,
                    dataToUpdate,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`,
                        },
                    }
                );
                this.coinData[this.selectedCoinIndex] = send.data;
                this.inputChanges = false;
                this.isCoinMoved = true;
                this.getCoinStatus(`${timestamp}`);
            } catch (error) {
                console.log(error);
            }
        },

        async pushCoinData() {
            try {
                await this.coinUploadImage();
                const dataToUpdate = this.coinData[this.selectedCoinIndex];

                const send = await axios.post(
                    status,
                    dataToUpdate,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`,
                        },
                    }
                );
                this.coinData[this.selectedCoinIndex] = send.data;
                this.inputChanges = false;
                this.isCoinMoved = true;
            } catch (error) {
                console.log(error);
            }
        },
        handleSaveClick(){
            if (this.isNewEntity) {
                this.pushExpData();
            } else {
                this.saveExpData();
            }
        },
        coinHandleSaveClick(){
            if (this.isNewEntity) {
                this.pushCoinData();
            } else {
                this.saveCoinData();
            }
        },
        async uploadImage() {
            const fileInput = this.$refs.fileInput;
            const previewImage = this.$refs.previewImage;

            if (fileInput.files.length > 0 || this.imageChange === true) {
                const formData = new FormData();
                formData.append('image', fileInput.files[0]);
                try {
                    const response = await axios.post(
                        `${imageRequest}?timestamp=${timestamp}`,
                        formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                    this.expData[this.selectedExpIndex].image = response.data;
                } catch (error) {
                    console.error('Image upload failed:', error);
                }
            } else {
                console.error('No file selected.');
            }
        },
        async coinUploadImage() {
            const fileInput = this.$refs.fileInput;
            const previewImage = this.$refs.previewImage;

            if (fileInput.files.length > 0 || this.imageChange === true) {
                const formData = new FormData();
                formData.append('image', fileInput.files[0]);
                try {
                    const response = await axios.post(
                        `${imageRequest}?timestamp=${timestamp}`,
                        formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                    this.coinData[this.selectedCoinIndex].image = response.data;
                } catch (error) {
                    console.error('Image upload failed:', error);
                }
            } else {
                console.error('No file selected.');
            }
        },
        handleImageChange(event) {
            const newImageUrl = event.target.value;
            const currentImageUrl = this.expData[this.selectedExpIndex].image;

            if (newImageUrl !== currentImageUrl) {
                this.imageChange = true;
                this.inputChanges = true;
            }
        },
        coinHandleImageChange(event) {
            const newImageUrl = event.target.value;
            const currentImageUrl = this.coinData[this.selectedCoinIndex].image;

            if (newImageUrl !== currentImageUrl) {
                this.imageChange = true;
                this.inputChanges = true;
            }
        },
        removeImg() {
            this.imageChange = true;
            this.inputChanges = true;
            this.expData[this.selectedExpIndex].image = " ";
        },
        coinRemoveImg() {
            this.imageChange = true;
            this.inputChanges = true;
            this.coinData[this.selectedCoinIndex].image = " ";
        },
        toggleAddStatus() {
            this.toggleAddStatusOpen = !this.toggleAddStatusOpen;
        },
        toggleExpLockState(exp, index) {
            this.selectedExpIndex = index;
            exp.is_deleted = !exp.is_deleted;
        },
        toggleCoinLockState(coin, index) {
            this.selectedCoinIndex = index;
            coin.is_deleted = !coin.is_deleted;
        },

        createNew(type) {
            this.isNewEntity = true;
            const newItem = {
                position: type === 'xp' ? this.expData.length + 1 : this.coinData.length + 1,
                image: "",
                trigger: 1,
                is_deleted: false,
                "status_type": type === 'xp' ? 'xp' : 'coins',
                "name_ru-RU": "",
                "name_ar-AE": "",
                "name_ja-JP": "",
                "name_ko-KR": "",
                "name_pt-BR": "",
                "name_pt-PT": "",
                "name_de-DE": "",
                "name_es-ES": "",
                "name_ex-MX": "",
                "name_zh-CN": "",
                "description_in_profile_ru-RU": "",
                "description_in_profile_ar-AE": "",
                "description_in_profile_ja-JP": "",
                "description_in_profile_ko-KR": "",
                "description_in_profile_pt-BR": "",
                "description_in_profile_pt-PT": "",
                "description_in_profile_de-DE": "",
                "description_in_profile_es-ES": "",
                "description_in_profile_ex-MX": "",
                "description_in_profile_zh-CN": "",
                "motivational_text_ru-RU": "",
                "motivational_text_ar-AE": "",
                "motivational_text_ja-JP": "",
                "motivational_text_ko-KR": "",
                "motivational_text_pt-BR": "",
                "motivational_text_pt-PT": "",
                "motivational_text_de-DE": "",
                "motivational_text_es-ES": "",
                "motivational_text_ex-MX": "",
                "motivational_text_zh-CN": "",
            };

            if (type === 'xp') {
                this.expData.push(newItem);
                const newIndex = this.expData.length - 1;
                this.$nextTick(() => {
                    this.showExpContent(newIndex);
                    // this.expData[newIndex].class = 'check';
                });
            } else if (type === 'coins') {
                this.coinData.push(newItem);
                const newIndex = this.coinData.length - 1;
                this.$nextTick(() => {
                    this.showCoinContent(newIndex);
                    // this.coins[newIndex].class = 'check';
                });
            }
            this.toggleAddStatusOpen = false;
        },
        dragStart(index) {
            this.dragIndex = index;
        },
        dragEnter(index) {
            this.dropIndex = index;
        },
        drop(event) {
            if (this.dragIndex !== null && this.dropIndex !== null) {
                event.preventDefault();
                const draggedItem = this.expData[this.dragIndex];
                const draggedItemId = draggedItem.id;

                this.expData.splice(this.dragIndex, 1);
                this.expData.splice(this.dropIndex, 0, draggedItem);
                this.updatePositionsOnServer(draggedItemId, this.dropIndex + 1);

                this.expData.forEach((item, index) => {
                    item.position = index + 1;
                });
                this.isItemMoved = true;

                this.dragIndex = null;
                this.dropIndex = null;
            }
        },
        coinDragStart(index) {
            this.coinDragIndex = index;
        },
        coinDragEnter(index) {
            this.coinDropIndex = index;
        },
        coinDrop(event) {
            if (this.coinDragIndex !== null && this.coinDropIndex !== null) {
                event.preventDefault();
                const draggedItem = this.coinData[this.coinDragIndex];
                const draggedItemId = draggedItem.id;

                this.coinData.splice(this.coinDragIndex, 1);
                this.coinData.splice(this.coinDropIndex, 0, draggedItem);
                this.updatePositionsCoinOnServer(draggedItemId, this.coinDropIndex + 1);

                this.coinData.forEach((item, index) => {
                    item.position = index + 1;
                });
                this.isCoinMoved = true;

                this.coinDragIndex = null;
                this.coinDropIndex = null;
            }
        },
        async unlockExp(id){
            try {
                await axios.delete(
                    `${status}?status_id=${id}&options=display`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.isItemMoved = true;
            } catch (error) {
                console.error(error);
            }
        },
        async lockExp(id) {
            try {
                await axios.delete(
                    `${status}?status_id=${id}&options=hide`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.isItemMoved = true;
            } catch (error) {
                console.error(error);
            }
        },
        async unlockCoin(id){
            try {
                await axios.delete(
                    `${status}?status_id=${id}&options=display`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.isCoinMoved = true;
            } catch (error) {
                console.error(error);
            }
        },
        async lockCoin(id) {
            try {
                await axios.delete(
                    `${status}?status_id=${id}&options=hide`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.isCoinMoved = true;
            } catch (error) {
                console.error(error);
            }
        },
        async deleteExp(id){
            id = this.showExpId;
            try{
                await axios.delete(
                    `${status}?status_id=${id}&options=delete`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.getExpStatus(`${timestamp}`);
                this.selectedExpIndex = null;
                this.showEdit = false;
                this.showConfirmationDialogExp = false;
            }catch (error){
                console.log(error)
            }
        },
        async deleteCoin(id){
            id = this.showCoinId;
            try{
                await axios.delete(
                    `${status}?status_id=${id}&options=delete`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }
                );
                this.getCoinStatus(`${timestamp}`);
                // this.selectedCoinIndex = null;
                this.showEdit = false;
                this.showConfirmationDialogCoin = false;
            }catch (error){
                console.log(error)
            }
        },
        handleExpChanges(){
            const textareaValue = this.expData[this.selectedExpIndex].name_en;
            if (textareaValue.trim()) {
                this.inputChanges = true; // Allow saving if textarea is not empty
            } else {
                this.inputChanges = false; // Prevent saving if textarea is empty
            }
        },
        handleCoinChanges(){
            const textareaValue = this.coinData[this.selectedCoinIndex].name_en;
            if (textareaValue.trim()) {
                this.inputChanges = true; // Allow saving if textarea is not empty
            } else {
                this.inputChanges = false; // Prevent saving if textarea is empty
            }
        },
        showExpContent(index, id) {
            if(this.inputChanges === true){
                this.alertDiscard();
                this.discardIndex = index;
                this.coinDiscardIndex = null;
            }else{
                this.selectedExpIndex = index;
                this.showExpId = id;
                this.showCoinEdit = false;
                this.showEdit = true;
                this.selectedCoinIndex = null;
                this.$nextTick(() => {
                    $('.ui.accordion').accordion({
                        selector: {
                            trigger: '.main-local .lang-iso'
                        }
                    });
                });
            }
        },
        showCoinContent(index, id) {
            if(this.inputChanges === true){
                this.alertDiscard();
                this.coinDiscardIndex = index;
                this.discardIndex = null;
            }else{
                this.selectedCoinIndex = index;
                this.showCoinId = id;
                this.showEdit = false;
                this.showCoinEdit = true;
                this.selectedExpIndex = null;
                this.$nextTick(() => {
                    $('.ui.accordion').accordion({
                        selector: {
                            trigger: '.main-local .lang-iso'
                        }
                    });
                });
            }
        },

        showRemoveConfirmationExp(index) {
            this.showExpId = index;
            this.showConfirmationDialogExp = true;
        },
        showRemoveConfirmationCoin(index) {
            this.selectedCoinIndex = index;
            this.showConfirmationDialogCoin = true;
        },
        alertDiscard(){
            this.showAlertDiscard = true;
        },
        confirmAlertDiscard(){
            this.showAlertDiscard = false;
            this.inputChanges = false;
            if(this.discardIndex !== null){
                this.showExpContent(this.discardIndex);
            }else if(this.coinDiscardIndex !== null){
                this.showCoinContent(this.coinDiscardIndex);
            }
            this.getExpStatus(`${timestamp}`);
            this.getCoinStatus(`${timestamp}`);
        },
        cancelAlertDiscard(){
            this.showAlertDiscard = false;
        },
        // Function to cancel the remove confirmation dialog
        cancelRemoveConfirmation() {
            this.showConfirmationDialogExp = false;
            this.showConfirmationDialogCoin = false;
            this.selectedCoinIndex = null;
            this.selectedExpIndex = null;
        },
        confirmRemoveCoin() {
            this.coins.splice(this.selectedCoinIndex, 1)
            this.showConfirmationDialogCoin = false;
        },
    },
    watch: {
        isItemMoved(newVal, oldVal) {
            if (newVal) {
                setTimeout(() => {
                    this.isItemMoved = false;
                }, 2200);
            }
        },
        isCoinMoved(newVal, oldVal) {
            if (newVal) {
                setTimeout(() => {
                    this.isCoinMoved = false;
                }, 2200);
            }
        },
    },
    computed: {
        isSaveDisabled() {
            return !this.inputChanges;
        },
    },
    mounted() {
        let timestamp = new Date().getTime();
        this.getExpStatus(timestamp);
        this.getCoinStatus(timestamp);
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
        setInterval(() => {
            getToken();
        }, 5 * 60 * 1000);
    }
});

app.mount('#app');