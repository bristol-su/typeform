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

        <b-form @submit.prevent="postComment" inline v-if="canAddComments">
            <label class="sr-only" for="comment">Comment: </label>
            <b-input
                    id="comment"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    placeholder="Leave a comment..."
                    v-model="newComment"
            ></b-input>

            <b-button type="submit" variant="outline-success">Post Comment</b-button>

        </b-form>
    </div>
</template>

<script>
    import Comment from './Comment';
    export default {
        name: "Comments",
        components: {Comment},
        props: {
            responseId: {
                required: false
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
                newComment: ''
            }
        },

        created() {
            this.loadComments();
        },

        methods: {
            loadComments() {
                this.$http.get('/response/' + this.responseId + '/comment')
                    .then(response => {
                        this.comments = response.data;
                        this.$emit('set-comment-count', response.data.length);
                    })
                    .catch(error => this.$notify.alert('Could not load comments'));
            },

            postComment() {
                this.$http.post('/response/' + this.responseId + '/comment', {comment: this.newComment})
                    .then(response => {
                        this.comments.push(response.data);
                        this.newComment = '';
                        this.$emit('comment-added', response.data);
                    })
                    .catch(error => this.$notify.alert('Could not post the comment'));
            }
        },

        computed: {}
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
