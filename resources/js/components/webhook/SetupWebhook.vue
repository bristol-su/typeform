<template>
    <div>
        <b-form @submit.prevent="linkWebhook">
            <b-form-group
                    id="connection-group"
                    label="Connection:"
                    label-for="connection"
                    description="Choose the Typeform connection to use"
            >
                <b-form-select
                        id="connection"
                        v-model="form.connection_id"
                        :options="connectionOptions"
                        required
                >
                    <template v-slot:first>
                        <option value="" disabled>-- Please select/create a connection --</option>
                    </template>
                </b-form-select>
            </b-form-group>

            <b-button type="submit" variant="primary">Create Webhook</b-button>
        </b-form>
    </div>
</template>

<script>
    export default {
        name: "SetupWebhook",

        props: {},

        data() {
            return {
                connections: [],
                form: {
                    connection_id: null
                }
            }
        },

        created() {
            this.refreshConnections();
        },
        
        methods: {
            refreshConnections() {
                this.$http.get('connection')
                    .then(response => this.connections = response.data)
                    .catch(error => this.$notify.alert('Could not load connections: ' + error.message));
            },

            linkWebhook() {
                this.$http.post('webhook', this.form)
                    .then(response => window.location.reload())
                    .catch(error => this.$notify.alert('Could not create webhook: ' + error.message));
            }
        },

        computed: {
            connectionOptions() {
                return this.connections.map(connection => {
                    return {text: connection.name, value: connection.id}
                })
            }
        }
    }
</script>

<style scoped>

</style>