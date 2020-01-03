<template>
    <div>
        {{castedValue}}
    </div>
</template>

<script>
    import moment from 'moment';
    
    export default {
        name: "CellDate",

        props: {
            value: {
                required: true,
                default: null
            }
        },

        data() {
            return {}
        },

        methods: {},

        computed: {
            castedValue() {
                return moment(this.value).fromNow();
            }
        }
    }
</script>

<style scoped>

</style>