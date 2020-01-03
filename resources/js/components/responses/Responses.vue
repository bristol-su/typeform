<template>
    <div>
        <div style="text-align: right;" v-if="canRefreshResponses">
            <b-button variant="outline-secondary" :disabled="refreshingResponses" @click="refreshResponses" size="sm"><i class="fa fa-refresh" /> Refresh</b-button>
        </div>
        <b-table :fields="columns" :items="rows">
            <template v-slot:cell()="data">
                <component v-if="componentExists(data.value.type)" :is="componentName(data.value.type)"
                    :value="data.value.answer">
                    
                </component>
                <div v-else>
                    Field Type {{data.value.type}} not supported
                </div>
            </template>
        </b-table>
    </div>
</template>

<script>
    import CellBoolean from './CellStyles/CellBoolean';
    import CellChoice from './CellStyles/CellChoice';
    import CellDate from './CellStyles/CellDate';
    import CellNumber from './CellStyles/CellNumber';
    import CellText from './CellStyles/CellText';
    
    export default {
        name: "Responses",

        props: {
            responses: {
                required: false,
                type: Array,
                default: function() {
                    return [];
                }
            },
            canRefreshResponses: {
                required: false,
                type: Boolean,
                default: false
            }
        },

        components: {
            'cell-boolean': CellBoolean,
            'cell-choice': CellChoice,
            'cell-date': CellDate,
            'cell-number': CellNumber,
            'cell-text': CellText
        },
        
        data() {
            return {
                refreshingResponses: false
            }
        },

        methods: {
            componentName(component) {
                return 'cell-' + component;
            },
            componentExists(component) {
                return this.componentName(component) in this.$options.components;
            },
            refreshResponses() {
                this.refreshingResponses = true;
                this.$http.post('/response/refresh')
                    .then(response => this.$notify.success('Refresh the page to see any new changes'))
                    .catch(error => this.$notify.alert('Could not refresh responses: ' + error.message))
                    .then(() => this.refreshingResponses = false );
            }
        },

        computed: {
            fields() {
                let fields = [];
                this.responses.forEach(response => response.answers.forEach(answer => {
                    if(fields.indexOf(answer.field) === -1) {
                        fields.push(answer.field);
                    }
                }));
                return fields;
            },
            
            columns() {
                return this.fields.map(field => {
                    return {key: field.id, label: field.title};
                });
            },
            
            rows() {
                return this.responses.map(response => {
                    let row = {};
                    response.answers.forEach(answer => {
                        row[answer.field_id] = {
                            answer: answer.answer,
                            type: answer.type
                        };
                    });
                    return row;
                })
            }
        }
    }
</script>

<style scoped>

</style>