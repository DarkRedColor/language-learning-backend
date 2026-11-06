const app = Vue.createApp({
    data() {
        return {
            searchQuery: '',
            searchQueryTransaction: '',
            searchQueryIdTransaction: {
                user_id: null,
                transaction_id: null
            },
            searchResults: [],
            searchTransactionResults: [],
            selectedGroup: null,
            isDeleted: false,
            modal: {
                show: false,
                user: null
            },
            isOpen: false,
            thisUser: false,
            userSelected: null,
            activeAside: null,
            activeTransactions: false,

            accessToken: '',
            refreshToken: '',
            secondToken: '',
            parsedDateAuth: null,
            parsedDateVal: null,
            activeInput: 'searchQueryIdTransaction.user_id',
            buttonText: 'User Information'
        };
    },
    methods: {
        async searchUsers() {
            if (this.searchQuery.length > 0) {
                setTimeout(() => {
                    axios.get(users, {
                        params: {
                            search: this.searchQuery
                        },
                        headers: {
                            Authorization: `Bearer ${localStorage.access_token}`
                        },
                    }).then(response => {
                        const users = response.data.results;

                        const filteredUsers = users.filter(user => {
                            return (
                                user.id.toString().includes(this.searchQuery) ||
                                user.email.includes(this.searchQuery) ||
                                user.username.includes(this.searchQuery) ||
                                user.date_joined.includes(this.searchQuery)
                            );
                        });
                        // this.searchQueryIdTransaction.user_id = users[users.length - 1].id;
                        // this.fetchTransactions(this.searchQueryIdTransaction.user_id)
                        this.searchResults = filteredUsers;
                        // this.searchTransactions();
                        // this.$nextTick(() => {
                        //   const firstUser = document.querySelector('.user-default');
                        //   if (firstUser) {
                        //     firstUser.classList.add('user-selected');
                        //     this.userSelected = firstUser.id;
                        //   }
                        // });
                    }).catch(error => {
                        console.error(error);
                    });
                }, 500);
            } else {
                this.fetchUsersList();
                this.searchTransactionResults = [];
            }
        },
        async searchTransactions() {
            if (this.searchQueryIdTransaction.transaction_id !== null && this.searchQueryIdTransaction.transaction_id.length > 0) {
                setTimeout(() => {
                    axios.get(transactions, {
                        params:{
                            transaction_id: this.searchQueryIdTransaction.transaction_id ? this.searchQueryIdTransaction.transaction_id : null,
                            user_id: this.searchQueryIdTransaction.user_id ? this.searchQueryIdTransaction.user_id : null
                        },
                        headers: {
                            Authorization: `Bearer ${localStorage.second_token}`
                        }
                    }).then(response => {
                        if (response.data && response.data.length > 0) {
                            const transactions = response.data;
                            const filteredTransactions = transactions.filter(transaction => {
                                return (
                                    transaction.user_id.toString().includes(this.searchQueryIdTransaction.user_id) ||
                                    transaction.transaction_id.includes(this.searchQueryIdTransaction.transaction_id) ||
                                    transaction.product_id.includes(this.searchQueryIdTransaction.user_id)
                                );
                            });
                            // this.searchQuery = transactions[0].user_id;
                            // this.searchQueryIdTransaction.user_id = this.searchQuery;
                            // this.fetchQueryById(this.searchQuery)
                            // this.searchUsers();
                            this.searchTransactionResults = filteredTransactions;
                        } else {
                            this.searchTransactionResults = []
                        }
                    }).catch(error => {
                        console.error(error);
                    });
                }, 500);
            } else {
                this.searchTransactionResults = [];
            }
        },
        async fetchUsersList(){
            await axios.get(users,{
                headers: {
                    Authorization: `Bearer ${localStorage.access_token}`
                }
            }).then(response => {
                const usersList = response.data.results;
                this.searchResults = usersList;
            }).catch(err => {
                console.error(err);
            });
        },
        async fetchTransactionsList(){
            await axios.get(transactions,{
                headers: {
                    Authorization: `Bearer ${localStorage.second_token}`
                }
            }).then(response => {
                const transactionsList = response.data;
                this.searchTransactionResults = transactionsList;
            }).catch(err => {
                console.error(err);
            });
        },

        getTransactionByUserId(userId){
            this.fetchTransactions(userId);
        },
        toggleActiveTransactions(id) {
            if (this.activeTransactions === id) {
                this.activeTransactions = null;
            } else {
                this.activeTransactions = id;
            }
        },
        toggleAside(id) {
            if (this.activeAside === id) {
                this.activeAside = null;
            } else {
                this.activeAside = id;
                // this.fetchTransactions(id);

                const findDateAuth = this.searchResults.find(user => user.id === id);
                if (findDateAuth) {
                    const date = new Date(findDateAuth.date_joined);
                    const year = String(date.getFullYear()).slice(-2);
                    const month = String(date.getMonth() + 1).padStart(2, "0");
                    const day = String(date.getDate()).padStart(2, "0");
                    const hours = String(date.getHours()).padStart(2, "0");
                    const minutes = String(date.getMinutes()).padStart(2, "0");
                    const seconds = String(date.getSeconds()).padStart(2, "0");

                    this.parsedDateAuth = `${day}-${month}-${year} / ${hours}:${minutes}:${seconds}`;
                } else {
                    this.parsedDateAuth = "";
                };
                const findDateVal = this.searchResults.find(user => user.id === id);
                if (findDateVal) {
                    const date = new Date(findDateVal.account_link_date);
                    const year = String(date.getFullYear()).slice(-2);
                    const month = String(date.getMonth() + 1).padStart(2, "0");
                    const day = String(date.getDate()).padStart(2, "0");
                    const hours = String(date.getHours()).padStart(2, "0");
                    const minutes = String(date.getMinutes()).padStart(2, "0");
                    const seconds = String(date.getSeconds()).padStart(2, "0");

                    this.parsedDateVal = `${day}-${month}-${year} / ${hours}:${minutes}:${seconds}`;
                } else {
                    this.parsedDateVal = "";
                }
            }
        },
        toggleUser(user) {
            if (this.userSelected !== user.id) {
                // Remove '.user-selected' class from sibling elements
                const userElements = document.querySelectorAll('.user');
                userElements.forEach((element) => {
                    if (element.id !== user.id) {
                        element.classList.remove('user-selected');
                    }
                });
                this.userSelected = user.id;
            }
        },
        toggleTransaction(transaction) {
            if (this.activeTransactions !== transaction.transaction_id) {
                // Remove '.user-selected' class from sibling elements
                const userElements = document.querySelectorAll('.transaction');
                userElements.forEach((element) => {
                    if (element.id !== transaction.transaction_id) {
                        element.classList.remove('user-selected');
                    }
                });
                this.activeTransactions = transaction.transaction_id;
            }
        },
        async fetchTransactions(userId){
            await axios.get(transactions,{
                params: {
                    user_id: userId
                },
                headers: {
                    Authorization: `Bearer ${localStorage.second_token}`
                }
            }).then(response => {
                if (response){
                    const transactions = response.data;
                    this.searchTransactionResults = transactions;
                }else{
                    this.searchTransactionResults = null;
                }
            }).catch(err => {
                console.error(err);
            });
        },
        async fetchQueryById(userId){
            await axios.get(users,{
                params: {
                    search: userId
                },
                headers: {
                    Authorization: `Bearer ${localStorage.access_token}`
                }
            }).then(response => {
                const useridname = response.data.results;
                this.searchResults = useridname;
            }).catch(err => {
                console.error(err);
            });
        },
        confirmDelete() {
            this.deleteUser(this.modal.user.id);
            this.modal.show = false;
        },
        cancelDelete() {
            this.modal.show = false;
        },
        // delete user by id
        async deleteUser(id) {
            try {
                this.modal.show = true;
                this.selectedUserId = id;
                await axios.delete(userDel,{
                    headers: {
                        Authorization: `Bearer ${localStorage.access_token}`
                    },
                    data: {
                        user_id: `${id}`
                    }
                });
                this.searchQuery = '';
                this.searchUsers();
                this.isDeleted = true;
                setTimeout(() => {
                    this.isDeleted = false;
                }, 5000);
            } catch (error) {
                console.error(error);
            }
        },
        toggleInputs() {
            this.activeInput = this.activeInput === 'searchQueryIdTransaction.user_id' ? 'searchQueryIdTransaction.transaction_id' : 'searchQueryIdTransaction.user_id';
            this.buttonText = this.activeInput === 'searchQueryIdTransaction.user_id' ? 'User Information' : 'By Transaction';

            this.searchQuery = this.searchQuery === this.searchQuery ? this.searchQuery = null : this.searchQuery;

            this.searchQueryIdTransaction.user_id = this.searchQueryIdTransaction.user_id === this.searchQueryIdTransaction.user_id ? this.searchQueryIdTransaction.user_id = null : this.searchQueryIdTransaction.user_id;

            this.searchQueryIdTransaction.transaction_id = this.searchQueryIdTransaction.transaction_id === this.searchQueryIdTransaction.transaction_id ? this.searchQueryIdTransaction.transaction_id = null : this.searchQueryIdTransaction.transaction_id;
            this.check()
        },
        toggleMenu() {
            this.isOpen = !this.isOpen;
        },
        toggleSelectedGroup(productId) {
            if (this.isSelectedGroup(productId)) {
                this.selectedGroup = null;
            } else {
                this.selectedGroup = productId;
            }
        },
        isSelectedGroup(productId) {
            return this.selectedGroup === productId;
        },
        check(){
            if(this.buttonText === 'By Transaction') {
                this.fetchTransactionsList();
                this.searchResults = [];
                this.userSelected = null;
            }else{
                this.fetchUsersList();
                this.searchTransactionResults = [];
            }
        }
    },
    computed: {
        groupedTransactionResults() {
            const groupedTransactions = {};

            if (Array.isArray(this.searchTransactionResults)) {
                for (const transaction of this.searchTransactionResults) {
                    const productId = transaction.product_id;
                    if (!groupedTransactions[productId]) {
                        groupedTransactions[productId] = [];
                    }
                    groupedTransactions[productId].push(transaction);
                }
            }

            return groupedTransactions;
        }
    },
    mounted() {
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
                    this.fetchUsersList();
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