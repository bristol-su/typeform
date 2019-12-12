<template>
    <div>
        <div v-if="loading">
            Loading...
        </div>
        <div v-else-if="hasWebhook">
            <b-alert show variant="success">Webhook set up!</b-alert>
        </div>
        <div v-else>
            <b-alert show variant="warning">Typeform Webhook not set up <b-button variant="primary" size="sm" v-b-modal:setup-webhook-modal>Link</b-button></b-alert>
            <b-modal id="setup-webhook-modal" title="Setup Webhook">
                <setup-webhook></setup-webhook>
            </b-modal>
        </div>
    </div>
</template>

<script>
    import SetupWebhook from './SetupWebhook';
    
    export default {
        name: "WebhookSetupAlert",
        components: {SetupWebhook},
        props: {},

        data() {
            return {
                webhooksLoading: false,
                webhooks: []
            }
        },

        created() {
            this.loadWebhook();
        },
        
        methods: {
            loadWebhook() {
                this.webhooksLoading = true;
                this.$http.get('/webhook')
                    .then(response => this.webhooks = response.data)
                    .catch(error => this.webhooks = [])
                    .then(() => this.webhooksLoading = false);
            },
        },

        computed: {
            loading() {
                return this.webhooksLoading;
            },
            
            hasWebhook() {
                return this.webhooks.length > 0;
            }
        }
    }
</script>

<style scoped>

</style>