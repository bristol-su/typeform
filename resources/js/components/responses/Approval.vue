<template>
    <div>
        <b-row>
            <b-col>
                <span v-html="statusHtml"></span>
            </b-col>
            <b-col v-if="canChange">
                <b-button variant="success" @click="approve" size="sm"><i class="fa fa-check"></i></b-button>
                <b-button variant="danger" @click="reject" size="sm"><i class="fa fa-times"></i></b-button>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        name: "Approval",

        props: {
            canChange: {
                required: false,
                type: Boolean,
                default: false
            },
            responseId: {
                required: true,
                type: String
            },
            status: {
                required: false,
                type: Boolean,
                default: null
            },
        },

        data() {
            return {}
        },

        methods: {
            approve() {
                this.$http.post('/response/' + this.responseId + '/approve')
                    .then(response => {
                        this.$notify.success('Approved form response ' + this.responseId);
                        window.location.reload();
                    })
                    .catch(error => this.$notify.alert('Could not reject form response: ' + error.message));
            },
            reject() {
                this.$http.post('/response/' + this.responseId + '/reject')
                    .then(response => {
                        this.$notify.success('Rejected form response ' + this.responseId);
                        window.location.reload();
                    })
                    .catch(error => this.$notify.alert('Could not reject form response: ' + error.message));
                
            }
        },

        computed: {
            statusHtml() {
                if(this.status === true) {
                    return '<i class="fa fa-check"></i> Approved'
                } else if(this.status === false) {
                    return '<i class="fa fa-times"></i> Rejected'
                }
                return '<i class="fa fa-hourglass ' + this.statusClass + '"></i> Awaiting Approval'
            },
            statusClass() {
                if(this.status === true) {
                    return 'approved'
                } else if(this.status === false) {
                    return 'rejected'
                }
                return 'waiting';
            }
        }
    }
</script>

<style scoped>
    .approved {
        background-color: #1e7e34;
    }
    .rejected {
        background-color: red;
    }
    .waiting {
        background-color: darkorange;
    }
</style>