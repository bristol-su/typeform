<template>
    <div ref="embedDomNode" style="margin: auto; height: 100%; width: 100%; min-height: 700px;">

    </div>
</template>

<script>
import {createWidget} from '@typeform/embed'
import '@typeform/embed/build/css/widget.css'

export default {
    name: "TypeformEmbedWidget",

    props: {
        formId: {
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
            createWidget(this.formId, {
                container: this.$refs.embedDomNode,
                hideHeaders: this.hideHeaders,
                hideFooter: this.hideFooter,
                opacity: this.opacity,
                hidden: this.hiddenFields,
                onSubmit: (data) => {
                    this.$notify.success('Your responmse has been saved. It may take a few minutes to show on the page.')
                }
            })

            // typeform.createWidget(
            //     this.$refs.embedDomNode,
            //     this.url.toString(),
            //     {
            //         hideHeaders: this.hideHeaders,
            //         hideFooter: this.hideFooter,
            //         opacity: this.opacity,
            //     }
            // )
        }
    },

    computed: {
        hiddenFields() {
            let hiddenFields = {};
            if (this.$tools.environment.authentication.hasUser()) {
                hiddenFields.portal_user_id = this.$tools.environment.authentication.getUser().id;
                hiddenFields.portal_user_forename = this.$tools.environment.authentication.getUser().data.first_name;
                hiddenFields.portal_user_surname = this.$tools.environment.authentication.getUser().data.last_name;
                hiddenFields.portal_user_preferred_name = this.$tools.environment.authentication.getUser().data.preferred_name;
                hiddenFields.portal_user_email = this.$tools.environment.authentication.getUser().data.email;
            }
            if (this.$tools.environment.authentication.hasGroup()) {
                hiddenFields.portal_group_id = this.$tools.environment.authentication.getGroup().id
                hiddenFields.portal_group_name = this.$tools.environment.authentication.getGroup().data.name;
                hiddenFields.portal_group_email = this.$tools.environment.authentication.getGroup().data.email;
            }
            if (this.$tools.environment.authentication.hasRole()) {
                hiddenFields.portal_role_name = this.$tools.environment.authentication.getRole().data.role_name;
                hiddenFields.portal_role_position_name = this.$tools.environment.authentication.getPosition().data.name;
            }

            if (this.$tools.environment.activityInstance.has()) {
                hiddenFields.activity_instance = this.$tools.environment.activityInstance.get().id;
            }

            if (this.$tools.environment.moduleInstance.has()) {
                hiddenFields.module_instance = this.$tools.environment.moduleInstance.get().id;
            }

            return hiddenFields;
        }
    }
}
</script>

<style scoped>

</style>
