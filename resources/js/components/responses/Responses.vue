<template>
    <div>
        <div style="text-align: right;" v-if="canRefreshResponses">
            <p-button variant="secondary" :disabled="refreshingResponses" @click="refreshResponses"><i class="fa fa-refresh" /> Refresh</p-button>
        </div>
        <p-table :columns="columns" :items="filteredRows">
            <template #head(approvals)>
                Approval Filter
                <p-select id="approval-filtering" v-model="approvalFiltering" :select-options="filterOptions" null-label="No Filtering" :null-value="null">
                </p-select>
            </template>

            <template #cell(approvals)="{row}">
                <approval v-if="allowApproval" :can-change="allowApproval" :response-id="row.responseId" :status="row.approved"></approval>
            </template>

            <template #actions="{row}">
                <a href="#" @click="showComments(row)" v-if="canSeeComments"><i class="fa fa-comments"></i> Comments ({{row.comments.length}})</a>
            </template>

            <template #cell(submittedBy)="{row}">
                <span>{{row.user.first_name}} {{row.user.last_name}}</span>
            </template>
            <template #cell(activityInstanceBy)="{row}">
                <span>{{row.identifier}}</span>
            </template>
            <template #cell(submittedAt)="{row}">
                <span>{{row.submitted_at}}</span>
            </template>

            <template #cell()="{row, column}">
                <div v-if="row.hasOwnProperty(column.key)">
                    <div v-if="row[column.key].type === 'file_url'">
                        <cell-file_url :value="row[column.key].id"></cell-file_url>
                    </div>
                    <component v-else-if="componentExists(row[column.key].type)" :is="componentName(row[column.key].type)"
                               :value="row[column.key].answer">
                    </component>
                    <div v-else-if="row[column.key].type">
                        Field Type {{row[column.key].type}} not supported
                    </div>
                    <div v-else>
                        N/A
                    </div>
                </div>
            </template>
        </p-table>

        <p-modal id="commentsModal" title="Comments" @hide="responseBeingCommented = null">
            <comments :can-add-comments="canAddComments"
                      :can-delete-comments="canDeleteComments"
                      :can-update-comments="canUpdateComments"
                      :response="responseBeingCommented"
                      v-if="responseBeingCommented !== null"
                      @commentUpdated="updateComments"></comments>
        </p-modal>
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
                approvalFiltering: null,
                filterOptions: [
                    {id: 'approved', value: 'Only Approved'},
                    {id: 'rejected', value: 'Only Rejected'},
                    {id: 'awaiting', value: 'Awaiting Approval'}
                ],
                responseBeingCommented: null
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
            },
            showComments(file) {
                this.responseBeingCommented = file;
                this.$ui.modal.show('commentsModal');
            },

            updateComments(comments) {
                let response = _.cloneDeep(this.responseBeingCommented);
                response.comments = comments;
                this.responses.splice(this.responses.indexOf(this.responses.filter(r => r.id === response.id)[0]), 1, response);
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
                    return {key: field.id, label: field.title.substring(0, 35), fullLabel: field.title};
                }).filter(cols => {
                    if(fieldIds.indexOf(cols.key) === -1) {
                        fieldIds.push(cols.key);
                        return true;
                    }
                    return false;
                }));
                fields.push({key: 'submittedAt', label: 'Submitted At', sortable: true})
                fields.push({key: 'approvals', label: 'Approval'});
                return fields;
            },

            rows() {
                return [...this.responses.map(response => {
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
                    row['comments'] = response.comments;
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
                })].sort(this.sortCompare);
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
