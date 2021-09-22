<template>
    <div class="comment-wrapper">
        <div class="commentText">
            <span class="commenterName">{{ commenterName }}</span> <span
            class="date sub-text">{{ comment.created_at | datetime }}</span>
            <p-api-form :schema="form" @submit="updateComment" v-if="editing" button-text="Update Comment">

            </p-api-form>

            <p v-else>{{comment.comment}}</p>

        </div>
        <div>
            <a href="#" @click="deleteComment" v-if="ownsComment && canDeleteComments"><i class="fa fa-trash"></i></a>
            <a href="#" @click="editComment" v-if="ownsComment && canUpdateComments"><i class="fa fa-edit"></i></a>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    name: "Comment",

    props: {
        canDeleteComments: {
            type: Boolean,
            required: true,
            default: false
        },
        canUpdateComments: {
            type: Boolean,
            required: true,
            default: false
        },
        comment: {
            required: true,
            type: Object
        }
    },

    data() {
        return {
            editing: false
        }
    },

    filters: {
        datetime(val) {
            return moment(val).format('lll')
        }
    },

    methods: {
        deleteComment() {
            this.$ui.confirm.delete('Deleting comment', 'Are you sure you want to delete this comment?')
                .then(() => {
                    this.$http.delete('/comment/' + this.comment.id)
                        .then(response => {
                            this.$notify.success('Comment deleted');
                            this.$emit('updated')
                        })
                        .catch(error => this.$notify.alert('Could not delete comment: ' + error.message));
                })
        },
        editComment() {
            this.editing = true;
        },
        updateComment(data) {
            this.$http.patch('/comment/' + this.comment.id, {
                comment: data.comment
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
            return this.$tools.environment.authentication.hasUser() && this.$tools.environment.authentication.getUser().id === this.comment.posted_by.id;
        },
        form() {
            return this.$tools.generator.form.newForm()
                .withGroup(this.$tools.generator.group.newGroup()
                    .withField(
                        this.$tools.generator.field.text('comment')
                            .label('Your comment')
                            .required(true)
                            .value(this.comment.comment)
                    )
                )
                .generate()
                .asJson();
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
