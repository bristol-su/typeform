<template>
    <div>
        <div style="text-align: right;" v-if="canRefreshResponses">
            <b-button variant="outline-secondary" :disabled="refreshingResponses" @click="refreshResponses" size="sm"><i class="fa fa-refresh" /> Refresh</b-button>
        </div>
        <b-table :fields="columns" :items="filteredRows" :sort-compare="sortCompare">
            <template v-slot:head(approved)="data">
                Approval
                <b-form-select v-model="approvalFiltering">
                    <b-form-select-option :value="null">No Filtering</b-form-select-option>
                    <b-form-select-option value="approved">Only Approved</b-form-select-option>
                    <b-form-select-option value="rejected">Only Rejected</b-form-select-option>
                    <b-form-select-option value="awaiting">Awaiting Approval</b-form-select-option>
                </b-form-select>
            </template>
            
            <template v-slot:cell(approved)="data">
                <approval :can-change="allowApproval" :response-id="data.item.responseId" :status="data.item.approved"></approval>
            </template>
            <template v-slot:cell(submittedBy)="data">
                <span>{{data.item.user.first_name}} {{data.item.user.last_name}}</span>
            </template>
            <template v-slot:cell(activityInstanceBy)="data">
                <span>{{data.item.identifier}}</span>
            </template>
            <template v-slot:cell(submittedAt)="data">
                <span>{{data.item.submitted_at}}</span>
            </template>
            <template v-slot:cell()="data">
                <div v-if="data.value.type === 'file_url'">
                    <cell-file_url :value="data.value.id" :query-string="queryString"></cell-file_url>
                </div>
                <component v-else-if="componentExists(data.value.type)" :is="componentName(data.value.type)"
                    :value="data.value.answer">
                </component>
                <div v-else-if="data.value.type">
                    Field Type {{data.value.type}} not supported
                </div>
                <div v-else>
                    N/A
                </div>
            </template>
            <template v-slot:cell(comments)="data">
                <comment-button v-if="canSeeComments"
                          :can-add-comments="canAddComments"
                          :can-delete-comments="canDeleteComments"
                          :can-update-comments="canUpdateComments"
                          :response-id="data.item.responseId"
                          :comment-count="data.item.commentcount"></comment-button>
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
    import Approval from './Approval';
    import CellFileUrl from './CellStyles/CellFileUrl';
    import CellPhoneNumber from './CellStyles/CellPhoneNumber';
    import CellUrl from './CellStyles/CellUrl';
    import CellEmail from './CellStyles/CellEmail';
    import CellChoices from './CellStyles/CellChoices';
    import Comments from './Comments';
    import CommentButton from './CommentButton';
    
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
            },
            showApprovedStatus: {
                required: false,
                type: Boolean,
                default: false
            },
            allowApproval: {
                required: false,
                type: Boolean,
                default: false
            },
            queryString: {
                required: true,
                type: String
            },
            showActivityInstanceBy: {
                required: false,
                default: false
            },
            canAddComments: {
                required: false,
                default: false
            },
            canSeeComments: {
                required: false,
                default: false                
            },
            canDeleteComments: {
                required: false,
                default: false
            },
            canUpdateComments: {
                required: false,
                default: false
            }
        },

        components: {
            CommentButton,
            Comments,
            Approval,
            'cell-boolean': CellBoolean,
            'cell-choice': CellChoice,
            'cell-date': CellDate,
            'cell-number': CellNumber,
            'cell-text': CellText,
            'cell-email': CellEmail,
            'cell-file_url': CellFileUrl,
            'cell-phone_number': CellPhoneNumber,
            'cell-url': CellUrl,
            'cell-choices': CellChoices
        },
        
        data() {
            return {
                refreshingResponses: false,
                approvalFiltering: null
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
            },
            sortCompare(a, b, key) {
                if(key === 'submittedAt') {
                    let aDate = new Date(a.submitted_at);
                    let bDate = new Date(b.submitted_at);
                    return aDate < bDate ? -1 : aDate > bDate ? 1 : 0
                }
                if(key === 'activityInstanceBy') {
                    return (a.identifier).localeCompare(b.identifier)
                }
                return null;
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
                let fieldIds = [];
                let fields = [];
                if(this.showActivityInstanceBy) {
                    fields.push({key: 'activityInstanceBy', label: 'Submission For', sortable: true})
                }
                fields.push({key: 'submittedBy', label: 'Submitted By'});
                fields = fields.concat(this.fields.map(field => {
                    return {key: field.id, label: field.title};
                }).filter(cols => {
                    if(fieldIds.indexOf(cols.key) === -1) {
                        fieldIds.push(cols.key);
                        return true;
                    }
                    return false;
                }));
                fields.push({key: 'submittedAt', label: 'Submitted At', sortable: true})
                if(this.allowApproval) {
                    fields.push({key: 'approved', label: 'Approval'})
                }
                if(this.canSeeComments) {
                    fields.push({key: 'comments', label: 'Comments'})
                }
                return fields;
            },
            
            rows() {
                return this.responses.map(response => {
                    let row = {};
                    response.answers.forEach(answer => {
                        row[answer.field_id] = {
                            id: answer.id,
                            answer: answer.answer,
                            type: answer.type
                        };
                    });
                    row['submitted_at'] = response.submitted_at;
                    row['approved'] = response.approved;
                    row['commentcount'] = response.comments.length;
                    row['responseId'] = response.id;
                    if(response.activity_instance.resource_type === 'user') {
                        row['identifier'] = response.activity_instance.participant.data.first_name + ' ' + response.activity_instance.participant.data.last_name;
                    }
                    if(response.activity_instance.resource_type === 'group') {
                        row['identifier'] = response.activity_instance.participant.data.name;
                    }
                    if(response.activity_instance.resource_type === 'role') {
                        row['identifier'] = response.activity_instance.participant.data.role_name;
                    }
                    row['user'] = response.submitted_by_user.data;
                    return row;
                })
            },
            filteredRows() {
                return this.rows.filter(row => {
                    if(this.approvalFiltering === 'approved') {
                        return row.approved === true;
                    } if(this.approvalFiltering === 'rejected') {
                        return row.approved === false;
                    } if(this.approvalFiltering === 'awaiting') {
                        return row.approved === null;
                    }
                    return true;
                })
            }
        }
    }
</script>

<style scoped>

</style>
