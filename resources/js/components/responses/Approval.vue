<template>
    <div>
        <div
            class="text-xs inline-flex items-center font-bold leading-sm px-3 py-1 text-black rounded-full"
            :class="{'bg-success-light': status === true, 'bg-danger-light': status === false, 'bg-warning-light': status === null}">
            <span v-if="status === true">Accepted</span>
            <span v-else-if="status === false">Rejected</span>
            <span v-else>Pending</span>
            <a href="#" class="ml-1" @keydown.enter.prevent="reject" @keydown.space.prevent="reject"
               @click.prevent="reject" role="button"
               v-if="status !== false && !$isLoading('approve-submission-' + this.responseId) && !$isLoading('reject-submission-' + this.responseId) && canChange">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                         content="Reject Submission"
                         v-tippy="{ arrow: true, animation: 'fade', placement: 'top-start', arrow: true, interactive: true}">
                      <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"/>
                    </svg>
                    <span class="sr-only">Reject Submission</span>
                </span>
            </a>

            <a href="#" class="ml-1" @keydown.enter.prevent="approve" @keydown.space.prevent="approve"
               @click.prevent="approve" role="button"
               v-if="status !== true && !$isLoading('approve-submission-' + this.responseId) && !$isLoading('reject-submission-' + this.responseId) && canChange">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                         content="Approve Submission"
                         v-tippy="{ arrow: true, animation: 'fade', placement: 'top-start', arrow: true, interactive: true}">
                      <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"/>
                    </svg>
                    <span class="sr-only">Approve Submission</span>
                </span>
            </a>

            <span :aria-busy="true" aria-live="polite" class="ml-1"
                  v-if="$isLoading('approve-submission-' + this.responseId)">
                    <svg class="animate-spin h-4 w-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" content="Approving Submission"
                         v-tippy="{ arrow: true, animation: 'fade', placement: 'top-start', arrow: true, interactive: true}">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="sr-only">Approving Submission</span>
                </span>

            <span :aria-busy="true" aria-live="polite" class="ml-1 text-black"
                  v-if="$isLoading('reject-submission-' + this.responseId)">
                <svg class="animate-spin h-4 w-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" content="Rejecting Submission"
                     v-tippy="{ arrow: true, animation: 'fade', placement: 'top-start', arrow: true, interactive: true}">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="sr-only">Rejecting Submission</span>
            </span>
        </div>
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
            this.$http.post('/response/' + this.responseId + '/approve', {}, {name: 'approve-submission-' + this.responseId})
                .then(response => {
                    this.$notify.success('Approved form response ' + this.responseId);
                    this.$emit('updated', true);
                })
                .catch(error => this.$notify.alert('Could not reject form response: ' + error.message));
        },
        reject() {
            this.$http.post('/response/' + this.responseId + '/reject', {}, {name: 'reject-submission-' + this.responseId})
                .then(response => {
                    this.$notify.success('Rejected form response ' + this.responseId);
                    this.$emit('updated', false);
                })
                .catch(error => this.$notify.alert('Could not reject form response: ' + error.message));

        }
    },

    computed: {
        statusHtml() {
            if (this.status === true) {
                return 'Approved'
            } else if (this.status === false) {
                return 'Rejected'
            }
            return 'Awaiting Approval'
        },
        statusClass() {
            if (this.status === true) {
                return 'approved'
            } else if (this.status === false) {
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
