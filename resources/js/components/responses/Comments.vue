<template>
    <div>
        <div v-if="comments.length > 0">
            <ul class="commentList">
                <li v-for="comment in comments" v-if="comments.length > 0">
                    <comment :comment="comment" :can-delete-comments="canDeleteComments" :can-update-comments="canUpdateComments" @updated="loadComments"></comment>
                    <hr/>
                </li>
            </ul>
        </div>
        <div v-else>
            No comments have been left.
        </div>

        <p-api-form :schema="form" @submit="postComment" v-if="canAddComments" button-text="Post Comment">

        </p-api-form>
    </div>
</template>

<script>
import Comment from './Comment';
export default {
    name: "Comments",
    components: {Comment},
    props: {
        response: {
            required: true,
            type: Object
        },
        canAddComments: {
            type: Boolean,
            required: true,
            default: false
        },
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
    },

    data() {
        return {
            comments: [],
        }
    },

    created() {
        this.loadComments();
    },

    methods: {
        loadComments() {
            this.$http.get('/response/' + this.response.responseId + '/comment')
                .then(response => {
                    this.comments = response.data;
                    this.$emit('commentUpdated', response.data);
                })
                .catch(error => this.$notify.alert('Could not load comments'));
        },

        postComment(data) {
            this.$http.post('/response/' + this.response.responseId + '/comment', {comment: data.comment})
                .then(response => {
                    this.comments.push(response.data);
                    this.$emit('commentUpdated', this.comments);
                })
                .catch(error => this.$notify.alert('Could not post the comment'));
        }
    },

    computed: {
        form() {
            return this.$tools.generator.form.newForm()
                .withGroup(this.$tools.generator.group.newGroup()
                    .withField(
                        this.$tools.generator.field.text('comment')
                            .label('Your comment')
                            .required(true)
                    )
                )
                .generate()
                .asJson();
        }
    }
}
</script>

<style scoped>

.commentList {
    padding: 0;
    list-style: none;
    overflow: auto;
}

.commentList li {
    margin: 0;
    margin-top: 10px;
}


</style>
