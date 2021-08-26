<template>
    <div ref="embedDomNode" style="margin: auto; height: 100%; width: 100%; min-height: 350px;">

    </div>
</template>

<script>
    import * as typeform from '@typeform/embed'
    import Url from 'domurl';

    export default {
        name: "TypeformEmbedWidget",

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
            opacity: {
                required: false,
                type: Number,
                default: 0
            }

        },

        data() {
            return {}
        },

        mounted() {
            this.embedForm();
        },

        methods: {
            embedForm() {
                typeform.makeWidget(
                    this.$refs.embedDomNode,
                    this.url.toString(),
                    {
                        hideHeaders: this.hideHeaders,
                        hideFooter: this.hideFooter,
                        opacity: this.opacity,
                    }
                )
            }
        },

        computed: {
            url() {
                let hiddenUrl = new Url(this.formUrl);
                if(this.$tools.environment.authentication.hasUser()) {
                    hiddenUrl.query.portal_user_id = this.$tools.environment.authentication.getUser().id;
                    hiddenUrl.query.portal_user_forename = this.$tools.environment.authentication.getUser().data.first_name;
                    hiddenUrl.query.portal_user_surname = this.$tools.environment.authentication.getUser().data.last_name;
                    hiddenUrl.query.portal_user_preferred_name = this.$tools.environment.authentication.getUser().data.preferred_name;
                    hiddenUrl.query.portal_user_email = this.$tools.environment.authentication.getUser().data.email;
                }
                if(this.$tools.environment.authentication.hasGroup()) {
                    hiddenUrl.query.portal_group_id = this.$tools.environment.authentication.getGroup().id
                    hiddenUrl.query.portal_group_name = this.$tools.environment.authentication.getGroup().data.name;
                    hiddenUrl.query.portal_group_email = this.$tools.environment.authentication.getGroup().data.email;
                }
                if(this.$tools.environment.authentication.hasRole()) {
                    hiddenUrl.query.portal_role_name = this.$tools.environment.authentication.getRole().data.role_name;
                    hiddenUrl.query.portal_role_position_name = this.$tools.environment.authentication.getRole().position.data.name;
                }

                if(this.$tools.environment.activityInstance.has()) {
                    hiddenUrl.query.activity_instance = this.$tools.environment.activityInstance.get().id;
                }

                if(this.$tools.environment.moduleInstance.has()) {
                    hiddenUrl.query.module_instance = this.$tools.environment.moduleInstance.get().id;
                }

                return hiddenUrl;
            }
        }
    }
</script>

<style scoped>

</style>
