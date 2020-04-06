<template>
    <div class="comment-wrapper">
        <b-col>
            <div class="commenterImage">
                <b-avatar rounded="sm" size="lg"></b-avatar>
            </div>
            <div class="commentText">
                <span class="commenterName">{{ commenterName }}</span> <span
                    class="date sub-text">{{ comment.created_at | datetime }}</span>
                <p v-if="editing">
                    <b-input-group>
                        <b-input type="text" size="sm" v-model="newComment"></b-input>

                        <b-input-group-append>
                            <b-button variant="info" @click="updateComment" size="sm">Save</b-button>
                        </b-input-group-append>
                    </b-input-group>
                    
                </p>
                <p v-else>{{comment.comment}}</p>

            </div>
            <div>
                <b-button size="sm" variant="danger" @click="deleteComment" v-if="ownsComment && canDeleteComments"><i class="fa fa-trash"></i></b-button>
                <b-button size="sm" variant="info" @click="editComment" v-if="ownsComment && canUpdateComments"><i class="fa fa-edit"></i></b-button>
            </div>
        </b-col>
    </div>
</template>

<script>
    import moment from 'moment';
    
    export default {
        name: "Comment",

        props: {
            comment: {
                required: true,
                type: Object
            },
            canDeleteComments: {
                required: true,
                type: Boolean,
                default: false
            },
            canUpdateComments: {
                required: true,
                type: Boolean,
                default: false
            },
        },

        data() {
            return {
                editing: false,
                newComment: null
            }
        },
        
        filters: {
            datetime(val) {
                return moment(val).format('lll')
            }
        },

        methods: {
            deleteComment() {
                this.$bvModal.msgBoxConfirm('Are you sure you want to delete this comment?', {
                    title: 'Deleting comment',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'danger',
                    okTitle: 'Delete',
                    cancelTitle: 'Cancel',
                    footerClass: 'p-2',
                    hideHeaderClose: true,
                    centered: true
                })
                    .then(confirmed => {
                        if (confirmed) {
                            this.$http.delete('/comment/' + this.comment.id)
                                .then(response => {
                                    this.$notify.success('Comment deleted');
                                    this.$emit('updated')
                                })
                                .catch(error => this.$notify.alert('Could not delete comment: ' + error.message));
                        } else {
                            this.$notify.warning('No comments deleted');
                        }
                    })
                    .catch(error => this.$notify.alert('Could not delete comment: ' + error.message));
            },
            editComment() {
                this.newComment = this.comment.comment;
                this.editing = true;
            },
            updateComment() {
                this.$http.patch('/comment/' + this.comment.id, {
                    comment: this.newComment
                })
                    .then(response => {
                        this.$notify.success('Comment updated');
                        this.editing = false;
                        this.$emit('updated');
                    })
                    .catch(error => this.$notify.alert('Could not update the comment: ' + error.message));
            }
        },

        computed: {
            commenterName() {
                return this.comment.posted_by.data.first_name + ' ' + this.comment.posted_by.data.last_name
            },
            ownsComment() {
                return portal.user.id === this.comment.posted_by.id;
            }
        }
    }
</script>

<style scoped>

    .comment-wrapper {
        width: 100%
    }

    .commenterImage {
        width: 30px;
        margin-right: 5px;
        height: 100%;
        float: left;
    }

    .commenterImage.owner {
        float: right;
    }

    .commentText p {
        margin: 0;
    }

    .sub-text {
        color: #aaa;
        font-family: verdana;
        font-size: 11px;
    }

    .commenterName {
        font-weight: bold;
    }
</style>