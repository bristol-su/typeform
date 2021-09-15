<template>
    <div>
        <span v-html="statusHtml"></span>
        <a v-if="canChange && status !== true" href="#" @click.prevent="approve" role="button"><i class="fa fa-check"></i><span class="sr-only">Approve</span></a>
        <a v-if="canChange && status !== false" href="#" @click.prevent="reject"><i class="fa fa-times"></i><span class="sr-only">Reject</span></a>
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
                        this.$emit('updated', true);
                    })
                    .catch(error => this.$notify.alert('Could not reject form response: ' + error.message));
            },
            reject() {
                this.$http.post('/response/' + this.responseId + '/reject')
                    .then(response => {
                        this.$notify.success('Rejected form response ' + this.responseId);
                        this.$emit('updated', false);
                    })
                    .catch(error => this.$notify.alert('Could not reject form response: ' + error.message));

            }
        },

        computed: {
            statusHtml() {
                if(this.status === true) {
                    return 'Approved'
                } else if(this.status === false) {
                    return 'Rejected'
                }
                return 'Awaiting Approval'
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
