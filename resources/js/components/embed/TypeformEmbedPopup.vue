<template>
    <div>
        <p-button @click="showForm">
            Show Form
        </p-button>
    </div>
</template>

<script>
    import * as typeform from '@typeform/embed'

    export default {
        name: "TypeformEmbedPopup",

        props: {
            formUrl: {
                required: false,
                type: String,
                default: ''
            },
            hideHeaders: {
                required: false,
                type: Boolean,
                default: true
            },
            hideFooter: {
                required: false,
                type: Boolean,
                default: true
            },
            mode: {
                required: false,
                type: String,
                default: 'popup'
            }

        },

        data() {
            return {
                typeformPopup: null
            }
        },

        mounted() {
            this.typeformPopup = typeform.makePopup(
                this.url,
                {
                    hideHeaders: this.hideHeaders,
                    hideFooter: this.hideFooter,
                    mode: this.mode
                }
            )
        },

        methods: {
            showForm() {
                this.typeformPopup.open();
            }
        },

        computed: {
            url() {
                return this.formUrl;
            }
        }
    }
</script>

<style scoped>

</style>
