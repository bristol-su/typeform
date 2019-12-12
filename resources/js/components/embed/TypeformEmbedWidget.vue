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
                    this.url,
                    {
                        hideHeaders: this.hideHeaders,
                        hideFooter: this.hideFooter,
                        opacity: this.opacity,
                    }
                )
            },
            getPortalProperty(...args) {
                let obj = portal;
                let result = args.reduce((obj, level) => obj && obj[level], obj);
                if(result === undefined) {
                    return null;
                }
                return result;
            }
        },

        computed: {
            url() {
                let hiddenUrl = new Url(this.formUrl);
                hiddenUrl.query.portal_user_id = this.getPortalProperty('user', 'id');
                hiddenUrl.query.portal_user_forename = this.getPortalProperty('data_user', 'forename');
                hiddenUrl.query.portal_user_surname = this.getPortalProperty('data_user', 'surname');
                hiddenUrl.query.portal_user_email = this.getPortalProperty('data_user', 'email');
                hiddenUrl.query.portal_group_name = this.getPortalProperty('group', 'name');
                hiddenUrl.query.portal_group_id = this.getPortalProperty('group', 'id');
                hiddenUrl.query.portal_group_email = this.getPortalProperty('group', 'email');
                hiddenUrl.query.portal_role_name = this.getPortalProperty('role', 'name');
                hiddenUrl.query.portal_role_position_name = this.getPortalProperty('role', 'position', 'name');
                return hiddenUrl;
            }
        }
    }
</script>

<style scoped>

</style>